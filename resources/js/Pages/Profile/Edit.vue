<script setup lang="ts">
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/Layout.vue";
import GetApiToken from "@/Pages/Profile/Partials/GetApiToken.vue";
import {ref} from "vue";
import {loadDarkMode} from "@/Helpers/DarkMode";

const darkMode = ref(loadDarkMode())

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Profile" />

    <Layout v-model="darkMode">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Profile</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-gray-50 shadow dark:bg-gray-800 dark:shadow-white sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-gray-50 shadow dark:bg-gray-800 dark:shadow-white sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-gray-50 shadow dark:bg-gray-800 dark:shadow-white sm:rounded-lg">
                    <GetApiToken class="max-w-xl" :dark-mode="darkMode"/>
                </div>

                <div class="p-4 sm:p-8 bg-gray-50 shadow dark:bg-gray-800 dark:shadow-white sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" :dark-mode="darkMode"/>
                </div>
            </div>
        </div>
    </Layout>
</template>
