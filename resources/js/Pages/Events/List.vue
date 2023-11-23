<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Event } from "@/types/Events/common";
import Layout from "@/Layouts/Layout.vue";
import EditPopup from "@/Pages/Events/Partials/EditPopup.vue";
import {CSSProperties, reactive, Ref, ref} from "vue";
import {Category} from "@/types/Categories/common";
import {determineBorderColor, determineTextColor} from "@/Helpers/Colors";
import moment, {Moment} from "moment";
import DurationConstructor = moment.unitOfTime.DurationConstructor;
import {loadDarkMode} from "@/Helpers/DarkMode";

const editEvent: Ref<Partial<Event>|null> = ref(null);

const props = defineProps<{
    events: Event[];
    categories: Category[]
}>();

const dateFormat = 'YYYY-MM-DD'
const darkMode = ref(loadDarkMode())
const scrollPos = ref(0)

const dates = ref(generateWeek(moment().subtract(3, 'days')))
const events = reactive<Event[]>(props.events)
const orderedEvents = reactive<Record<string, (null|Event)[]>>({})
orderEvents()

function orderEvents() {
    for (const key of Object.keys(orderedEvents)) {
        delete orderedEvents[key];
    }
    for(const event of events.sort((event1, event2) => event1.start_date.localeCompare(event2.start_date))) {
        const check = moment(event.start_date);
        const end = moment(event.end_date);
        let firstIndex: null|number = null;
        while(check.isSameOrBefore(end)) {
            if (!orderedEvents[check.format(dateFormat)]) {
                orderedEvents[check.format(dateFormat)] = [];
            }
            while((firstIndex || 0) > orderedEvents[check.format(dateFormat)].length) {
                orderedEvents[check.format(dateFormat)].push(null)
            }
            if (!firstIndex) {
                const firstNull = orderedEvents[check.format(dateFormat)].findIndex((item) => !item)
                firstIndex = firstNull >= 0 ? firstNull : orderedEvents[check.format(dateFormat)].length
            }
            orderedEvents[check.format(dateFormat)][firstIndex] = event
            check.add(1, 'day')
        }
    }
}
function changeStartDate(changeAmount: number, changeUnit: DurationConstructor): void {
    dates.value = generateWeek(moment(dates.value[0]).add(changeAmount, changeUnit));
}
function generateWeek(date: Moment): string[] {
    const dates = [];
    for (let i = 0; i < 7; i++) {
        dates.push(date.format(dateFormat))
        date.add(1, 'day')
    }
    return dates;
}

function determineTileStyle(event: Event, date: string): CSSProperties {
    const startingTile = event.start_date === date
    const endingTile = event.end_date === date
    const eventLength = moment(event.end_date).diff(event.start_date, 'day') + 1
    const timeToDisplayEnd = moment(dates.value[dates.value.length - 1]).diff(event.start_date, 'day') + 1
    const timeFromDisplayStart = moment(event.end_date).diff(dates.value[0], 'day') + 1
    return {
        'background-color': event.category.color,
        'border-color': determineBorderColor(event.category.color, darkMode.value),
        color: determineTextColor(event.category.color, darkMode.value),
        'margin-left': startingTile ? '2px' : '0',
        'margin-right': endingTile ? '2px' : '0',
        'border-left-width': !startingTile ? '0' : undefined,
        'border-right-width': !endingTile ? '0' : undefined,
        'border-radius': `${startingTile ? '5px' : 0} ${endingTile ? '5px' : 0} ${endingTile ? '5px' : 0} ${startingTile ? '5px' : 0}`,
        'max-width': `${14 * Math.min(eventLength, timeToDisplayEnd, timeFromDisplayStart)}vw`,
        'z-index': date === event.start_date || date === dates.value[0] ? '10' : '9'
    }
}
function close() {
    editEvent.value = null
}

function updateEvent(event: Event) {
    const eventIndex = events.findIndex(({id}) => id === event.id)
    if (eventIndex >= 0) {
        events[eventIndex] = event
    } else {
        events.push(event)
    }
    orderEvents()
    close()
}

function deleteEvent(id: number) {
    const eventIndex = events.findIndex(({id: eventId}) => eventId === id)
    if (eventIndex >= 0) {
        events.splice(eventIndex, 1)
    }
    orderEvents()
    close()
}
const minScrollDistance = 200;
function handleHorizontalScroll(event: WheelEvent) {
    scrollPos.value += event.deltaX;
    if (scrollPos.value > minScrollDistance) {
        dates.value = dates.value.map((item) => moment(item).add(1, 'day').format(dateFormat));
        scrollPos.value = 0;
    }
    if (scrollPos.value < -minScrollDistance) {
        dates.value = dates.value.map((item) => moment(item).subtract(1, 'day').format(dateFormat));
        scrollPos.value = 0;
    }
}
</script>

<template>
    <Head title="Events"/>
    <EditPopup v-if="editEvent" :event="editEvent" :categories="categories" :dark-mode="darkMode" :close="close" :update-event="updateEvent" :delete-event="deleteEvent"/>
    <Layout v-model="darkMode">
        <div style="height: calc(100vh - 65px); overflow: clip" class="print-expand">
            <div class="sidebar-container print-hide">
                <div class="sidebar sidebar-left bg-gray-100 border-gray-600 dark:bg-gray-800 dark:border-gray-100 text-gray-800 dark:text-gray-100 w-[1vw] hover:w-[7vw]">
                    <button @click="changeStartDate(-1, 'day')"><v-icon icon="mdi-chevron-left"/>Day</button>
                    <button @click="changeStartDate(-1, 'week')"><v-icon icon="mdi-chevron-left"/>Week</button>
                    <button @click="changeStartDate(-1, 'month')"><v-icon icon="mdi-chevron-left"/>Month</button>
                    <button @click="changeStartDate(-1, 'year')"><v-icon icon="mdi-chevron-left"/>Year</button>
                </div>
            </div>
                <div style="width: 98vw; display: inline-block; vertical-align: top;">
                <div class="date-info-container" :style="{'color': darkMode ? 'white' : 'grey'}">
                    <TransitionGroup name="column">
                        <div v-for="date of dates" class="date-info" :key="date">
                            {{date}}<button v-if="$page.props.auth.user" @click="editEvent = {start_date: date}" class="print-hide"><v-icon icon="mdi-plus"/></button>
                        </div>
                    </TransitionGroup>
                </div>
                <div style="overflow-y: auto; overflow-x: hidden; height: calc(94vh - 66px)" class="dark:[color-scheme:dark] print-expand">
                    <div class="dates-container" @wheel="handleHorizontalScroll">
                        <div v-for="date of dates" :key="date" :style="{'border-color': darkMode ? 'white' : 'grey'}" class="column-container">
                            <TransitionGroup tag="div" name="column" v-if="orderedEvents[date]" class="column">
                                <template v-for="(event, index) of orderedEvents[date]" :key="event?.id || `${date}-${index}`">
                                    <div
                                        v-if="event"
                                        class="event_tile"
                                        :style="determineTileStyle(event, date)"
                                        @click="editEvent = event"
                                    >
                                        <template v-if="date === event.start_date || date === dates[0]">
                                            <div
                                                style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap"
                                                class="pa-2"
                                            >{{event.name}}</div>
                                            <div v-if="event.start_date < dates[0] && event.end_date > dates[dates.length - 1]" class="pa-2" style="white-space: nowrap">
                                                {{event.start_date}} -> {{event.end_date}}
                                            </div>
                                            <template v-else>
                                                <div v-if="event.start_date < dates[0]" class="pa-2">Starts: {{event.start_date}}</div>
                                                <div v-if="event.end_date > dates[dates.length - 1]" class="pa-2">Ends: {{event.end_date}}</div>
                                            </template>
                                        </template>
                                    </div>
                                    <div v-else style="height: 10vh; visibility: hidden"/>
                                </template>
                            </TransitionGroup>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar-container print-hide">
                <div class="sidebar sidebar-right bg-gray-100 border-gray-600 dark:bg-gray-800 dark:border-gray-100 text-gray-800 dark:text-gray-100 w-[1vw] hover:w-[7vw]">
                    <button @click="changeStartDate(1, 'day')">Day<v-icon icon="mdi-chevron-right"/></button>
                    <button @click="changeStartDate(1, 'week')">Week<v-icon icon="mdi-chevron-right"/></button>
                    <button @click="changeStartDate(1, 'month')">Month<v-icon icon="mdi-chevron-right"/></button>
                    <button @click="changeStartDate(1, 'year')">Year<v-icon icon="mdi-chevron-right"/></button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.dates-container {
    display: flex;
    flex-direction: row;
    overscroll-behavior: none;
    min-height: calc(94vh - 66px);
}
.date-info-container {
    border-bottom: 1px solid;
    width: 98vw;
}
.date-info {
    display: inline-block;
    text-align: center;
    width: 14vw;
    padding-top: 2vh;
    padding-bottom: 2vh;
    font-weight: bolder;
    font-size: 2vh;
    height: 6vh;
}
.column-container {
    border-left: 1px black solid;
    border-right: 1px black solid;
    width: 14vw;
}
.column {
    height: 100%;
    min-height: calc(94vh - 66px);
    width: 14vw;
    display: grid;
    grid-gap: 2px;
    align-content: start;
}
.sidebar-container {
    vertical-align: top;
    display: inline-block;
    height: 100%;
    width: 1vw;
    z-index: 1000
}
.sidebar {
    transition: width 1s;
    height: 100%;
    position: relative;
    z-index: 1000;
    border-width: 1px;
    border-style: solid;
    display: grid;
    align-items: center;
    align-content: center;
}
.sidebar button {
    height: 10vh;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    padding-left: 1vw;
}
.sidebar-left {
    border-radius: 0 10px 10px 0;
}
.sidebar-right {
    border-radius: 10px 0 0 10px;
    float: right;
}
.event_tile {
    height: 10vh;
    border-radius: 5px;
    border-width: 2px;
    z-index: 1;
}
.column-move,
.column-enter-active,
.column-leave-active {
    transition: all 0.5s ease;
}
.column-leave-active {
    display: none;
    position: absolute;
}
</style>
