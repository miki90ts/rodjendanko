<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Podešavanja izgleda',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Podešavanja izgleda" />

    <h1 class="sr-only">Podešavanja izgleda</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Izgled aplikacije"
            description="Izaberi temu koja ti najviše prija"
        />
        <AppearanceTabs />
    </div>
</template>
