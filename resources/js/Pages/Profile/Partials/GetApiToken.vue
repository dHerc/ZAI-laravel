<script setup lang="ts">
import {useForm} from "@inertiajs/vue3";
import {Ref, ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";

const form = useForm({});
const token: Ref<string|null> = ref(null);
const tokenCopied = ref(false);

function getToken(): void {
    axios.get('api/profile/token'). then((resp) => {
        token.value = String(resp.data.token);
    })
}
function copyToken(): void {
    navigator.clipboard.writeText(String(token.value));
    tokenCopied.value = true;
    setTimeout(() => tokenCopied.value = false, 500);
}
</script>

<template>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Get API Token</h2>

        <p class="mt-1 text-sm text-gray-600">
            Get a token allowing you to make api calls from external servers
        </p>
    </header>
    <PrimaryButton @click="getToken()">Get</PrimaryButton>
    <Modal :show="Boolean(token)" @close="token = null">
        <div class="p-8">
            <h2 class="text-lg font-medium text-gray-900">
                Your token is:<br>
            </h2>
            <div>
                <div class="center_container p-6">
                    <p>{{token}}
                        <v-tooltip v-model="tokenCopied" :open-on-hover="false">
                            <template v-slot:activator="{ props }">
                                <button @click="copyToken" v-bind="props">
                                    <v-icon icon="mdi-content-copy"/>
                                </button>
                            </template>
                            <span>Copied</span>
                        </v-tooltip>
                    </p>
                </div>
                <div class="center_container">
                    <PrimaryButton @click="token = null">Close</PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.center_container {
    display: flex;
    width: 100%;
    justify-content: center;
}
</style>
