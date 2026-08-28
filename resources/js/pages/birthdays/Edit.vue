<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BirthdayForm from '@/components/birthdays/BirthdayForm.vue';
import type {
    BirthdayContact,
    BirthdayDefaults,
    BirthdayMessage,
    EmailTemplate,
} from '@/types';
defineProps<{
    contact: BirthdayContact;
    defaults: BirthdayDefaults;
    messages: BirthdayMessage[];
    templates: EmailTemplate[];
}>();
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Rođendani', href: '/birthdays' },
            { title: 'Izmeni', href: '#' },
        ],
    },
});
</script>
<template>
    <Head :title="`Izmeni ${contact.display_name}`" />
    <main class="min-h-full bg-transparent p-4 sm:p-8">
        <header class="page-header-card mx-auto mb-7 max-w-7xl">
            <p class="text-sm font-semibold text-violet-600">
                Podešavanje kontakta
            </p>
            <h1 class="text-3xl font-bold">{{ contact.display_name }}</h1>
        </header>
        <BirthdayForm
            :contact="contact"
            :defaults="defaults"
            :messages="messages"
            :templates="templates"
        />
    </main>
</template>
