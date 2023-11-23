<script setup lang="ts">
import {Ref, ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";

const token: Ref<string|null> = ref(null);
const tokenCopied = ref(false);

defineProps<{
  darkMode: boolean
}>()

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
    <section>
      <header>
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-50">Get API Token</h2>

          <p class="mt-1 text-sm text-gray-600 dark:text-gray-200">
              Get a token allowing you to make api calls from external servers
          </p>
      </header>
      <PrimaryButton class="dark:bg-gray-700" @click="getToken()">Get</PrimaryButton>
      <Modal :show="Boolean(token)" @close="token = null">
          <div class="p-8" :class="{'bg-gray-800': darkMode}">
              <h2 class="text-lg font-medium" :class="{'text-gray-900': !darkMode, 'text-gray-50': darkMode}">
                  Your token is:<br>
              </h2>
              <div>
                  <div class="center_container p-6" :class="{'text-gray-50': darkMode}">
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
                      <PrimaryButton :class="{'bg-gray-700': darkMode}" @click="token = null">Close</PrimaryButton>
                  </div>
              </div>
          </div>
      </Modal>
    </section>
</template>

<style scoped>
.center_container {
    display: flex;
    width: 100%;
    justify-content: center;
}
</style>
