<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BirthdayForm from '@/components/birthdays/BirthdayForm.vue';
import type { BirthdayDefaults, BirthdayMessage, EmailTemplate } from '@/types';
defineProps<{
    defaults: BirthdayDefaults;
    messages: BirthdayMessage[];
    templates: EmailTemplate[];
}>();
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Rođendani', href: '/birthdays' },
            { title: 'Dodaj', href: '/birthdays/create' },
        ],
    },
});
</script>
<template>
    <Head title="Dodaj rođendan" />
    <main class="min-h-full bg-transparent p-4 sm:p-8">
        <header class="page-header-card mx-auto mb-7 max-w-7xl">
            <p class="text-sm font-semibold text-violet-600">Novi kontakt</p>
            <h1 class="text-3xl font-bold">Dodaj rođendan</h1>
            <p class="mt-2 text-slate-500">
                Prvo osnovni podaci, a automatizaciju možeš fino podesiti ispod.
            </p>
        </header>
        <BirthdayForm
            :defaults="defaults"
            :messages="messages"
            :templates="templates"
        />
    </main>
</template>
