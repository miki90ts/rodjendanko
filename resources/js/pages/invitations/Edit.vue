<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import InvitationForm from '@/components/invitations/InvitationForm.vue';
import type { BirthdayInvitation, InvitationTemplateOption } from '@/types';

defineProps<{
    invitation: BirthdayInvitation;
    templates: InvitationTemplateOption[];
    timezones: string[];
    defaults: { host_name: string; timezone: string };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pozivnice', href: '/invitations' },
            { title: 'Izmena', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="`Izmeni: ${invitation.title}`" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <header class="page-header-card mx-auto mb-7 max-w-7xl">
            <p class="text-sm font-semibold text-violet-600">
                Podešavanje pozivnice
            </p>
            <h1 class="text-3xl font-black">{{ invitation.title }}</h1>
            <p class="mt-2 text-slate-500">
                Izmeni detalje, listu gostiju ili izgled pozivnice.
            </p>
        </header>
        <InvitationForm
            :invitation="invitation"
            :templates="templates"
            :timezones="timezones"
            :defaults="defaults"
        />
    </main>
</template>
