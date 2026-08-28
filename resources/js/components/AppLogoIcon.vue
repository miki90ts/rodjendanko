<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M11 2h2v4h-2V2Zm-5.5 8A2.5 2.5 0 0 1 8 7.5h8a2.5 2.5 0 0 1 2.5 2.5v1.25c-.8.04-1.53.35-2.1.85-.66.58-1.5.58-2.16 0a3.4 3.4 0 0 0-4.48 0c-.66.58-1.5.58-2.16 0a3.44 3.44 0 0 0-2.1-.85V10ZM4 13.25c.84-.04 1.57.22 2.18.75a3.4 3.4 0 0 0 4.48 0c.66-.58 1.5-.58 2.16 0a3.4 3.4 0 0 0 4.48 0c.75-.66 1.7-.8 2.7-.58V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6.75ZM12 0c1.1 1.2 1.65 2.17 1.65 2.9A1.65 1.65 0 1 1 10.35 3C10.35 2.27 10.9 1.27 12 0Z"
        />
    </svg>
</template>
