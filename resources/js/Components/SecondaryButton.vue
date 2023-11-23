<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
        darkMode?: boolean
    }>(),
    {
        type: 'button',
    }
);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        :class="{'!text-white': darkMode, 'bg-gray-700': darkMode, 'hover:bg-gray-600': darkMode, 'text-gray-700': !darkMode, 'bg-white': !darkMode, 'hover:!bg-gray-50': !darkMode}"
    >
        <slot />
    </button>
</template>
