<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Event } from "@/types/Events/common";
import Layout from "@/Layouts/Layout.vue";
import EditPopup from "@/Pages/Events/Partials/EditPopup.vue";
import {Ref, ref} from "vue";
import {Category} from "@/types/Categories/common";

const editEvent: Ref<Event|null> = ref(null);

defineProps<{
    events?: Event[];
    categories: Category[]
}>();
function close() {
    editEvent.value = null
}
</script>

<template>
    <Head title="Events"/>
    <EditPopup v-if="editEvent" :event="editEvent" :categories="categories" :close="close"/>
    <Layout>
        <div v-for="event of events">
            {{event.name}}
            {{event.start_date}}
            {{event.end_date}}
            <button v-if="$page.props.auth.user" v-on:click="editEvent = event"><v-icon icon="mdi-pencil-outline"/></button>
        </div>
    </Layout>
</template>

<style scoped>

</style>
