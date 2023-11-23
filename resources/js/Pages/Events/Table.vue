<script setup lang="ts">
import {Head, usePage} from '@inertiajs/vue3';
import { Event } from "@/types/Events/common";
import Layout from "@/Layouts/Layout.vue";
import EditPopup from "@/Pages/Events/Partials/EditPopup.vue";
import {reactive, Ref, ref} from "vue";
import {Category} from "@/types/Categories/common";
import {loadDarkMode} from "@/Helpers/DarkMode";

const editEvent: Ref<Partial<Event>|null> = ref(null);

const props = defineProps<{
    events: Event[];
    categories: Category[]
}>();

const darkMode = ref(loadDarkMode());

const events = reactive<Event[]>(props.events)
const filteredEvents = reactive([...events])
const startDate = ref<string|null>(null)
const endDate = ref<string|null>(null)
const mode = ref<'any'|'start'|'end'>('any');

const headers = [
    {
        title: 'Name',
        key: 'name',
    }, {
        title: 'Start date',
        key: 'start_date'
    }, {
        title: 'End date',
        key: 'end_date'
    }, {
        title: 'Category',
        key: 'category.name'
    }, {
        title: 'Description',
        key: 'description'
    }, {
        title: 'Image',
        sortable: false,
        key: 'image'
    }
]

if (usePage().props.auth.user) {
    headers.push({
        title: 'Edit',
        sortable: false,
        key: 'edit'
    })
}

function recalculateEvents()
{
    filteredEvents.splice(0);
    filteredEvents.push(...events.filter((event) => {
        if (!startDate.value && !endDate.value) {
            return true
        }
        if (mode.value === 'any') {
            if (startDate.value && endDate.value) {
                return (event.start_date <= endDate.value && event.end_date >= startDate.value) ||
                    (event.end_date >= startDate.value && event.start_date <= endDate.value)
            }
            return (!endDate.value || event.start_date <= endDate.value) &&
                (!startDate.value || event.end_date >= startDate.value)
        }
        if (mode.value === 'start') {
            return (!endDate.value || event.start_date <= endDate.value) &&
                (!startDate.value || event.start_date >= startDate.value)
        }
        if (mode.value === 'end') {
            return (!endDate.value || event.end_date <= endDate.value) &&
                (!startDate.value || event.end_date >= startDate.value)
        }
        return true
    }))
}
function close() {
    editEvent.value = null
    recalculateEvents()
}

function updateEvent(event: Event) {
    const eventIndex = events.findIndex(({id}) => id === event.id)
    if (eventIndex >= 0) {
        events[eventIndex] = event
    } else {
        events.push(event)
    }
    close()
}

function deleteEvent(id: number) {
    const eventIndex = events.findIndex(({id: eventId}) => eventId === id)
    if (eventIndex >= 0) {
        events.splice(eventIndex, 1)
    }
    close()
}
</script>

<template>
    <Head title="Events"/>
    <EditPopup v-if="editEvent" :event="editEvent" :categories="categories" :dark-mode="darkMode" :close="close" :update-event="updateEvent" :delete-event="deleteEvent"/>
    <Layout v-model="darkMode">
        <div style="height: calc(100vh - 65px); overflow: clip; width: 100vw" class="print-expand">
            <div style="padding: 5px" class="print-hide" :style="{color: darkMode ? 'white' : 'black'}" :class="{inverted: darkMode}">
                Date from: <input type="date" class="bg-gray-50 dark:bg-gray-800" v-model="startDate">
                Date to: <input type="date" class="bg-gray-50 dark:bg-gray-800" v-model="endDate">
                Mode: <select v-model="mode">
                    <option value="any">Any</option>
                    <option value="start">Start</option>
                    <option value="end">End</option>
                </select>
                <v-btn @click="recalculateEvents" :theme="darkMode ? 'dark' : 'light'">Filter</v-btn>
                <v-btn v-if="$page.props.auth.user" style="float: right; margin-right: 10px" :theme="darkMode ? 'dark' : 'light'" @click="editEvent = {}">Add new event</v-btn>
            </div>
            <v-data-table :headers="headers" :items="filteredEvents" class="data-table print-expand" :theme="darkMode ? 'dark' : 'light'">
                <template v-slot:item.image="{ item }">
                    <v-img width="10vw" v-if="item.image" :src="`/images/${item.image.filename}`"/>
                </template>
                <template v-slot:item.edit="{ item }">
                    <v-btn class="print-hide" @click="editEvent = item">Edit</v-btn>
                </template>
            </v-data-table>
        </div>
    </Layout>
</template>

<style scoped>
.data-table {
    overflow: auto;
    height: calc(100% - 50px);
}
:deep(.v-data-table__td) {
    min-width: 10vw;
}
.inverted ::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
option {
    color: black;
}
</style>
