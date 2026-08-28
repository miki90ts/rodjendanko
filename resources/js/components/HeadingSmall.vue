<script setup lang="ts">
defineProps<{ title: string; description?: string }>();
</script>

<template>
    <header>
        <h2 class="mb-0.5 text-base font-medium">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </header>
</template>
