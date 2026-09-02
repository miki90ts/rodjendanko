<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import InvitationForm from '@/components/invitations/InvitationForm.vue';
import type { InvitationTemplateOption } from '@/types';

defineProps<{
    templates: InvitationTemplateOption[];
    timezones: string[];
    defaults: { host_name: string; timezone: string };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pozivnice', href: '/invitations' },
            { title: 'Nova pozivnica', href: '/invitations/create' },
        ],
    },
});
</script>

<template>
    <Head title="Nova rođendanska pozivnica" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <header class="page-header-card mx-auto mb-7 max-w-7xl">
            <p class="text-sm font-semibold text-violet-600">Nova proslava</p>
            <h1 class="text-3xl font-black">Kreiraj rođendansku pozivnicu</h1>
            <p class="mt-2 max-w-3xl text-slate-500">
                Unesi detalje, dodaj goste, izaberi izgled i pošalji
                personalizovane pozivnice sa privatnim RSVP linkom.
            </p>
        </header>
        <InvitationForm
            :templates="templates"
            :timezones="timezones"
            :defaults="defaults"
        />
    </main>
</template>
