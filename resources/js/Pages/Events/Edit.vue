<script setup lang="ts">
import {Event} from "@/types/Events/common";
import {useForm} from "@inertiajs/vue3";
import {Category} from "@/types/Categories/common";
import {ref} from "vue";

const props = defineProps<{
    event: Event;
    categories: Category[]
}>();
const event = props.event;

const form = useForm({
    _method: 'patch',
    name: event.name,
    start_date: event.start_date,
    end_date: event.end_date,
    description: event.description,
    category_id: event.category_id,
    file: undefined as File|undefined
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
</script>

<template>
    <form @submit.prevent="form.post(`update`)">
        <input type="text" v-model="form.name">
        <input type="date" v-model="form.start_date">
        <input type="date" v-model="form.end_date">
        <textarea v-model="form.description"/>
        <select v-model="form.category_id">
            <option v-for="category in categories" v-bind:value="category.id">{{category.name}}</option>
        </select>
        <v-file-input @update:modelValue="onFileChange"></v-file-input>
        <v-img :src="imageUrl"></v-img>
        <div v-if="form.file"></div>
        <button>Save</button>
    </form>
</template>

<style scoped>

</style>
