<script setup lang="ts">
import {ref} from "vue";
import {Category} from "@/types/Categories/common";
import {determineBorderColor, determineTextColor} from "@/Helpers/Colors";
import {maxLength, required} from "@/Helpers/InputRules";

const props = defineProps<{
    category: Category,
    darkMode: boolean,
    close: () => void,
    updateCategory: (category: Category) => void,
    deleteCategory: (id: number) => void
}>();
const category = props.category;

const isFormValid = ref(true);
const deleting = ref(false);

const form = ref({
    name: category.name,
    color: category.color
});

async function submit() {
    if (!isFormValid.value) {
        return
    }
    if (category.id) {
        const response = await window.axios.patch<Category>(
            `api/categories/${category.id}`,
            form.value
        );
        props.updateCategory(response.data)
        return
    }
    const response = await window.axios.post<Category>(
        `api/categories`,
        form.value
    );
    props.updateCategory(response.data)
}

async function submitDelete() {
    await window.axios.delete(`api/categories/${category.id}`)
    props.deleteCategory(category.id)
}
</script>

<template>
    <div class="main-popup-container" v-on:click="close()">
        <div
            class="popup-subcontainer"
            :style="{'border-color': determineBorderColor(category.color, darkMode), 'background-color': category.color}"
            v-on:click="(e) => e.stopPropagation()"
        >
            <v-form @submit.prevent="submit" validate-on="input" v-model="isFormValid">
                <v-text-field
                    :style="{color: determineTextColor(category.color, darkMode)}"
                    label="Name"
                    style="width: 350px"
                    v-model="form.name"
                    :rules="[required(), maxLength(255)]"
                    required
                    counter
                    maxlength="255"
                />
                <v-color-picker width="350px" v-model="form.color"></v-color-picker>
                <template v-if="!deleting">
                    <v-btn type="button" ripple @click="close()" :style="{width: category.id ? '33%' : '50%'}" color="orange" style="color: white !important;">Cancel</v-btn>
                    <v-btn v-if="category.id" type="button" ripple @click="deleting = true" style="width: 34%" color="red">Delete</v-btn>
                    <v-btn type="submit" ripple :style="{width: category.id ? '33%' : '50%'}" color="green" :disabled="!isFormValid">Save</v-btn>
                </template>
                <template v-if="deleting">
                    <div style="text-align: center; width: 100%" :style="{color: determineTextColor(category.color, darkMode)}">Are you sure?</div>
                    <v-btn type="button" ripple @click="deleting = false" style="width: 50%" color="orange">No</v-btn>
                    <v-btn type="button" ripple @click="submitDelete()" style="width: 50%" color="green">Yes</v-btn>
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
    background-color: rgba(0,0,0,0.7);
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}
.popup-subcontainer {
    background-color: white;
    opacity: 1;
    width: 380px;
    padding-right: 10px;
    padding-left: 10px;
    padding-top: 10px;
    border-style: solid;
    border-radius: 5px;
    border-width: 5px;
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
:deep(.v-field__field input) {
  background-color: #00000000;
}
</style>
