<script setup lang="ts">
import {useForm} from "@inertiajs/vue3";
import {Category} from "@/types/Categories/common";

const props = defineProps<{
    category: Category;
}>();
const category = props.category;

const form = useForm({
    name: category.name,
    color: category.color
});
</script>

<template>
    <form @submit.prevent="form.patch(`update`)">
        <input type="text" v-model="form.name">
        <v-color-picker v-model="form.color"></v-color-picker>
        <button>Save</button>
    </form>
</template>

<style scoped>

</style>
