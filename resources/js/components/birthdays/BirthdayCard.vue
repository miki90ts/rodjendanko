<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CalendarDays, Mail, Sparkles } from '@lucide/vue';
import type { BirthdayContact } from '@/types';

defineProps<{ contact: BirthdayContact; compact?: boolean }>();

const initials = (contact: BirthdayContact): string =>
    `${contact.first_name[0] ?? ''}${contact.last_name?.[0] ?? ''}`.toUpperCase();

const dateLabel = (value: string): string =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        day: 'numeric',
        month: 'long',
    }).format(new Date(`${value}T12:00:00`));
</script>

<template>
    <Link
        :href="`/birthdays/${contact.id}`"
        class="celebration-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-950"
    >
        <div
            class="grid size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-100 to-rose-100 text-sm font-bold text-violet-700 dark:from-violet-950 dark:to-rose-950 dark:text-violet-200"
        >
            {{ initials(contact) }}
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <h3
                    class="truncate font-semibold text-slate-900 dark:text-white"
                >
                    {{ contact.display_name }}
                </h3>
                <span
                    v-if="contact.days_until === 0"
                    class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700"
                    >Danas!</span
                >
            </div>
            <div
                class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500"
            >
                <span class="inline-flex items-center gap-1"
                    ><CalendarDays class="size-3.5" />{{
                        dateLabel(contact.next_birthday)
                    }}</span
                >
                <span class="font-medium text-violet-600">{{
                    contact.days_until === 0
                        ? 'Slavimo danas'
                        : `za ${contact.days_until} dana`
                }}</span>
            </div>
            <div
                v-if="!compact && contact.settings?.auto_send_enabled"
                class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"
            >
                <Sparkles class="size-3" /> Automatsko slanje ·
                {{ contact.settings.send_time || '09:00' }}
                <Mail class="ml-1 size-3" /> Email poruka
            </div>
        </div>
    </Link>
</template>
