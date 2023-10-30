<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Category } from "@/types/Categories/common";
import Layout from "@/Layouts/Layout.vue";

defineProps<{
    categories?: Category[];
}>();
</script>

<template>
    <Head title="Categories"/>
    <Layout>
        <div v-for="category in categories">
            <div class="square" :style="{ 'background-color': category.color }"></div>
            {{category.name}}
            <Link v-bind:href="`/categories/${category.id}/edit`"><v-icon icon="mdi-pencil-outline"/></Link>
        </div>
    </Layout>
</template>

<style scoped>
.square {
    height: 16px;
    display: inline-block;
    aspect-ratio: 1;
}
</style>
