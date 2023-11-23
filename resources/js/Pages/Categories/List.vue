<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Category } from "@/types/Categories/common";
import Layout from "@/Layouts/Layout.vue";
import {reactive, ref, Ref} from "vue";
import EditPopup from "@/Pages/Categories/Partials/EditPopup.vue";
import {determineBorderColor, determineTextColor} from "@/Helpers/Colors";
import {loadDarkMode} from "@/Helpers/DarkMode";

const editCategory: Ref<Category|null> = ref(null);

const props = defineProps<{
    categories?: Category[];
}>();

const categories = reactive(props.categories ?? [])

const darkMode = ref(loadDarkMode() ?? false);

function getDefaultColor(): string {
    return darkMode.value ? '#172439' : '#FFFFFF'
}

function close() {
    editCategory.value = null
}

function updateCategory(category: Category) {
    const categoryIndex = categories.findIndex(({id}) => id === category.id)
    if (categoryIndex >= 0) {
        categories[categoryIndex] = category
    } else {
        categories.push(category)
    }
    close()
}

function deleteCategory(id: number) {
    const categoryIndex = categories.findIndex(({id: categoryId}) => categoryId === id)
    categories.splice(categoryIndex, 1)
    close()
}
</script>

<template>
    <Head title="Categories"/>
    <EditPopup v-if="editCategory" :category="editCategory" :dark-mode="darkMode" :close="close" :update-category="updateCategory" :delete-category="deleteCategory"/>
    <Layout v-model="darkMode">
        <div class="main-container">
            <div v-for="category in categories"
                 class="sub-container"
                 :style="{
                     'border-color': determineBorderColor(category.color, darkMode),
                     'background-color': category.color,
                     'color': determineTextColor(category.color, darkMode)
                 }"
                 @click="editCategory = $page.props.auth.user ? category : null"
            >
                {{category.name}}
            </div>
            <div
                @click="editCategory = {id: 0, name: '', color: getDefaultColor()}"
                class="sub-container"
                :style="{
                    'border-color': determineBorderColor(getDefaultColor(), darkMode),
                    'background-color': getDefaultColor()
                }"
                v-if="$page.props.auth.user"
            ><v-icon icon="mdi-plus" :color="darkMode ? 'rgb(156 163 175)' : 'black'"/></div>
        </div>
    </Layout>
</template>

<style scoped>
.main-container {
    padding: 20px;
    display: grid;
    gap: 15px;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}
.sub-container {
    display: inline-block;
    padding: 10px;
    min-width: 200px;
    max-width: 350px;
    border-radius: 5px;
    border-width: 5px;
    text-align: center;
    overflow-wrap: break-word;
}
</style>
