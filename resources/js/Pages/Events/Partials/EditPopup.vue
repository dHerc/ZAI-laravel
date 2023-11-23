<script setup lang="ts">
import {Event} from "@/types/Events/common";
import {reactive, Ref, ref} from "vue";
import {Category} from "@/types/Categories/common";
import {determineBorderColor, determineTextColor} from "@/Helpers/Colors";
import {maxLength, required} from "@/Helpers/InputRules";
import {usePage} from "@inertiajs/vue3";

const props = defineProps<{
    event: Partial<Event>,
    darkMode: boolean,
    categories: Category[],
    close: () => void,
    updateEvent: (event: Event) => void,
    deleteEvent: (id: number) => void
}>()
const event = props.event

const baseUrl = window.location.protocol + "//" + window.location.host;
const isFormValid = ref(true)
const deleting = ref(false)
const addingImage = ref(false)
const maxRows = (window.innerHeight - 500) / 25
const allCategories = reactive([...props.categories])
if (event.category && allCategories.every((category) => category.id !== event.category?.id)) {
    allCategories.push(event.category)
}
const currentCategory = ref<Category|undefined>(event.category);
const textColor = ref(determineTextColor(currentCategory.value?.color, props.darkMode));
const backgroundColor = ref(currentCategory.value?.color || (props.darkMode ? '#172439' : 'white'));
const borderColor = ref(determineBorderColor(currentCategory.value?.color, props.darkMode))
const editable = Boolean(usePage().props.auth.user)

const form = ref({
    _method: 'patch',
    name: event.name,
    start_date: event.start_date,
    end_date: event.end_date,
    description: event.description,
    category_id: event.category?.id,
    image: null as File | null | ''
})

const imageUrl: Ref<string | null> = ref(event.image ? `/images/${event.image.filename}` : null)

function validateEndDate(date: string) {
    if (!form.value.start_date) {
        return 'Field is required'
    }
    return date >= form.value.start_date || 'End date cannot be before start date'
}

function recalculateCategory(id: number) {
    currentCategory.value = props.categories.find((category) => category.id === id)
    textColor.value = determineTextColor(currentCategory.value?.color, props.darkMode)
    backgroundColor.value = currentCategory.value?.color || (props.darkMode ? '#172439' : 'white')
    borderColor.value = determineBorderColor(currentCategory.value?.color, props.darkMode)
}

async function submit() {
    if (!isFormValid.value) {
        return
    }
    const formData = new FormData()
    for (const [field, value] of Object.entries(form.value)) {
        if (value !== null && (event.id || field !== '_method')) {
            formData.append(field, typeof value === 'object' ? value : String(value))
        }
    }
    const response = await window.axios.post<Event>(
        event.id ? `${baseUrl}/api/events/${event.id}` : `${baseUrl}/api/events`,
        formData
    )
    props.updateEvent(response.data)
    props.updateEvent(response.data)
}

async function submitDelete() {
    if (event.id) {
        await window.axios.delete(`${baseUrl}/api/events/${event.id}`)
        props.deleteEvent(event.id)
    }
}

function createImage(file: File) {
    const reader = new FileReader()

    reader.onload = e => {
        const newUrl = e.target?.result?.toString()
        if (newUrl) {
            imageUrl.value = newUrl
        }
    }
    reader.readAsDataURL(file)
}

function closeFileAdder() {
    addingImage.value = false
}

function onFileChange(files: File[]) {
    if (!files || !files.length) {
        return
    }
    form.value.image = files[0]
    createImage(files[0])
    closeFileAdder()
}

function resetImage() {
    form.value.image = null
    imageUrl.value = event.image ? `/images/${event.image.filename}` : null
}

function removeImage() {
    form.value.image = ''
    imageUrl.value = null
}
</script>

<template>
    <div class="main-popup-container" v-on:click="close()">
        <div
            class="popup-subcontainer"
            :style="{'border-color': borderColor, 'background-color': backgroundColor}"
            v-on:click="(e) => e.stopPropagation()"
        >
            <v-form @submit.prevent="submit" validate-on="input" v-model="isFormValid">
                <v-text-field
                    :style="{color: textColor}"
                    label="Name"
                    style="width: 400px"
                    v-model="form.name"
                    :rules="[required(), maxLength(255)]"
                    required
                    counter
                    maxlength="255"
                    :disabled="!editable"
                />
                <v-select
                    label="Category"
                    :style="{color: textColor}"
                    v-model="form.category_id"
                    :items="allCategories"
                    :rules="[required()]"
                    item-title="name"
                    item-value="id"
                    @update:modelValue="recalculateCategory"
                    :disabled="!editable"
                />
                <div class="date-selection" :class="{inverted: textColor === 'white'}"
                     :style="{color: textColor}">
                    <v-text-field
                        type="date"
                        v-model="form.start_date"
                        :rules="[required()]"
                        :disabled="!editable"
                    />
                    <v-icon icon="mdi-arrow-right-bold" class="px-10" style="vertical-align: top; padding-top: 30px"></v-icon>
                    <v-text-field
                        type="date"
                        v-model="form.end_date"
                        :rules="[required(), validateEndDate]"
                        :disabled="!editable"
                    />
                </div>
                <div v-if="addingImage">
                    <v-file-input
                        :style="{color: textColor}"
                        label="Image"
                        accept="image/png,image/jpeg"
                        @update:modelValue="onFileChange"
                        @click:clear="closeFileAdder"
                        :prepend-icon="undefined"
                    >
                        <template v-slot:prepend>
                            <v-icon style="opacity: 1; margin-left: 10px" icon="mdi-camera"
                                    :color="textColor"/>
                        </template>
                        <template v-slot:append>
                            <v-icon role="button" style="opacity: 1; margin-right: 10px" icon="mdi-close-circle-outline"
                                    :color="textColor"/>
                        </template>
                    </v-file-input>
                </div>
                <div v-else>
                    <div class="outer" style="width: 100%" v-if="imageUrl">
                        <div v-if="editable" class="top opacity-0 hover:bg-black/[.50] hover:opacity-100 w-100 h-100 flex justify-center items-center">
                            <div class="image_buttons">
                                <button type="button" @click="addingImage = true">
                                    <v-icon icon="mdi-pencil" color="white" size="xxx-large"></v-icon>
                                </button>
                                <button type="button" @click="resetImage"
                                        v-if="event.image && `/images/${event.image.filename}` !== imageUrl">
                                    <v-icon icon="mdi-refresh" color="white" size="xxx-large"></v-icon>
                                </button>
                                <button type="button" @click="removeImage">
                                    <v-icon icon="mdi-delete" color="white" size="xxx-large"></v-icon>
                                </button>
                            </div>
                        </div>
                        <div class="below" style="min-height: 100px">
                            <v-img :src="imageUrl" width="300">
                                <template v-slot:placeholder>
                                    <div class="d-flex align-center justify-center fill-height">
                                        <v-progress-circular
                                            color="grey-lighten-4"
                                            indeterminate
                                        ></v-progress-circular>
                                    </div>
                                </template>
                            </v-img>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="editable" class="top w-100 h-100 flex justify-center items-center">
                            <div class="image_buttons">
                                <v-icon icon="mdi-image" :color="textColor"
                                        size="xxx-large"></v-icon>
                                <button type="button" @click="addingImage = true">
                                    <v-icon icon="mdi-plus" :color="textColor"
                                            size="xxx-large"></v-icon>
                                </button>
                                <button type="button" v-if="event.image" @click="resetImage">
                                    <v-icon icon="mdi-refresh" :color="textColor"
                                            size="xxx-large"></v-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <v-textarea
                        :style="{color: textColor}"
                        label="Description"
                        v-model="form.description"
                        autocomplete
                        auto-grow
                        :max-rows="maxRows"
                        :rules="[required(), maxLength(15000)]"
                        no-resize
                        counter
                        maxlength="15000"
                        :disabled="!editable"
                    />
                </div>
                <template v-if="editable">
                    <template v-if="!deleting">
                        <v-btn type="button" ripple @click="close()" :style="{width: event.id ? '33%' : '50%'}"
                               color="orange" style="color: white !important;">Cancel
                        </v-btn>
                        <v-btn v-if="event.id" type="button" ripple @click="deleting = true" style="width: 34%" color="red">
                            Delete
                        </v-btn>
                        <v-btn type="submit" ripple :style="{width: event.id ? '33%' : '50%'}" color="green"
                               :disabled="!isFormValid">Save
                        </v-btn>
                    </template>
                    <template v-if="deleting">
                        <div style="text-align: center; width: 100%"
                             :style="{color: textColor}">Are you sure?
                        </div>
                        <v-btn type="button" ripple @click="deleting = false" style="width: 50%" color="orange">No</v-btn>
                        <v-btn type="button" ripple @click="submitDelete()" style="width: 50%" color="green">Yes</v-btn>
                    </template>
                </template>
                <template v-else>
                    <v-btn type="button" style="width: 100%" color="orange" @click="close">Close</v-btn>
                </template>
            </v-form>
        </div>
    </div>
</template>

<style scoped>
.main-popup-container {
    height: 100vh;
    width: 100vw;
    position: absolute;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1100;
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup-subcontainer {
    background-color: white;
    opacity: 1;
    width: 430px;
    padding-right: 10px;
    padding-left: 10px;
    padding-top: 10px;
    border-style: solid;
    border-radius: 5px;
    border-width: 5px;
}

.date-selection .v-input {
    display: inline-block;
    background-color: inherit;
    width: 160px;
}

.date-selection.inverted ::-webkit-calendar-picker-indicator {
    filter: invert(1);
}

input, textarea {
    display: block;
}

:deep(.v-messages__message) {
    text-shadow: 1px 1px lightgray;
}

:deep(.v-counter) {
    text-shadow: 1px 1px lightgray;
}

:deep(.v-field__input input) {
    background-color: #00000000;
}

:deep(.v-field__field input) {
    background-color: #00000000;
}

.outer {
    display: grid;
    grid-template: 1fr / 1fr;
    place-items: center;
}

.outer > * {
    grid-column: 1 / 1;
    grid-row: 1 / 1;
}

.outer .below {
    z-index: 1;
}

.outer .top {
    z-index: 2;
}

.image_buttons button {
    padding: 5px;
}
:deep(.v-field--disabled) {
    opacity: 1;
}
</style>
