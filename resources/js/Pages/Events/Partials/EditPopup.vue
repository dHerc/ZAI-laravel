<script setup lang="ts">
import {Event} from "@/types/Events/common";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {Category} from "@/types/Categories/common";

const props = defineProps<{
    event: Event;
    categories: Category[]
    close: () => void
}>();
const event = props.event;

const form = useForm({
    _method: 'patch',
    name: event.name,
    start_date: event.start_date,
    end_date: event.end_date,
    description: event.description,
    category_id: event.category_id,
    file: null as File|null
});

let imageUrl = ref(`/images/${event.image.filename}`);

function createImage(file: File) {
    const reader = new FileReader();

    reader.onload = e => {
        const newUrl = e.target?.result?.toString();
        if (newUrl) {
            imageUrl.value = newUrl;
        }
    };
    reader.readAsDataURL(file);
}
function onFileChange(files: File[]) {
    if (!files || !files.length) {
        return;
    }
    form.file = files[0];
    createImage(files[0]);
}
function resetImage()
{
    form.file = null;
    imageUrl.value = `/images/${event.image.filename}`;
}
</script>

<template>
    <div class="main-popup-container" v-on:click="close">
        <button v-on:click="close">Test</button>
        <div class="popup-subcontainer w-3/4 h-3/4" v-on:click="(e) => e.stopPropagation()">
            <form @submit.prevent="form.post(`api/events/${event.id}`)">
                <input type="text" v-model="form.name">
                <input type="date" v-model="form.start_date">
                <input type="date" v-model="form.end_date">
                <textarea v-model="form.description"/>
                <select v-model="form.category_id">
                    <option v-for="category in categories" v-bind:value="category.id">{{category.name}}</option>
                </select>
                <div>
                    <v-file-input @update:modelValue="onFileChange" @click:clear="resetImage" style="width: 50%;"/>
                    <v-img :src="imageUrl" class="w-1/2"/>
                </div>
                <div v-if="form.file"></div>
                <button>Save</button>
            </form>
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
}
input, textarea {
    display: block;
}
</style>
