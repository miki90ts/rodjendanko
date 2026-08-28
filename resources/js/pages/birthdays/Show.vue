<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    Mail,
    Pencil,
    Phone,
    Trash2,
} from '@lucide/vue';
import {
    messageModeOptions,
    optionLabel,
    relationshipOptions,
} from '@/lib/birthdayOptions';
import type { BirthdayContact } from '@/types';
const props = defineProps<{ contact: BirthdayContact }>();
function destroyContact(): void {
    if (confirm(`Obriši kontakt ${props.contact.display_name}?`)) {
        router.delete(`/birthdays/${props.contact.id}`);
    }
}
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Rođendani', href: '/birthdays' },
            { title: 'Detalji', href: '#' },
        ],
    },
});
</script>
<template>
    <Head :title="contact.display_name" />
    <main class="min-h-full bg-transparent p-4 sm:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <Link
                href="/birthdays"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-violet-600"
                ><ArrowLeft class="size-4" /> Nazad na rođendane</Link
            >
            <section
                class="celebration-card overflow-hidden rounded-3xl border bg-white shadow-sm dark:bg-slate-950"
            >
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                        <div
                            class="grid size-24 place-items-center rounded-3xl border-4 border-white bg-violet-100 text-3xl font-bold text-violet-700 dark:border-slate-950"
                        >
                            {{ contact.first_name[0]
                            }}{{ contact.last_name?.[0] }}
                        </div>
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold">
                                {{ contact.display_name }}
                            </h1>
                            <p class="text-slate-500">
                                {{
                                    contact.relationship
                                        ? optionLabel(
                                              relationshipOptions,
                                              contact.relationship,
                                          )
                                        : 'Bez oznake odnosa'
                                }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="`/birthdays/${contact.id}/edit`"
                                class="btn-secondary"
                                ><Pencil class="size-4" /> Izmeni</Link
                            ><button
                                class="btn-secondary text-rose-600"
                                @click="destroyContact"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div
                            class="celebration-card rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"
                        >
                            <CalendarDays class="size-5 text-violet-600" />
                            <p class="mt-3 text-sm text-slate-500">
                                Sledeći rođendan
                            </p>
                            <p class="font-semibold">
                                za {{ contact.days_until }} dana · puni
                                {{ contact.turning_age }}
                            </p>
                        </div>
                        <div
                            class="celebration-card rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"
                        >
                            <Mail class="size-5 text-violet-600" />
                            <p class="mt-3 text-sm text-slate-500">Email</p>
                            <p class="truncate font-semibold">
                                {{ contact.email || 'Nije unet' }}
                            </p>
                        </div>
                        <div
                            class="celebration-card rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"
                        >
                            <Phone class="size-5 text-violet-600" />
                            <p class="mt-3 text-sm text-slate-500">Telefon</p>
                            <p class="font-semibold">
                                {{ contact.phone || 'Nije unet' }}
                            </p>
                        </div>
                    </div>
                    <div class="celebration-card mt-6 rounded-2xl border p-5">
                        <h2 class="font-semibold">Automatizacija</h2>
                        <p class="mt-2 text-sm text-slate-500">
                            {{
                                contact.settings?.auto_send_enabled
                                    ? `Automatska email poruka u ${contact.settings.send_time || '09:00'} · ${optionLabel(messageModeOptions, contact.settings.message_mode)}`
                                    : 'Automatsko slanje je isključeno'
                            }}
                        </p>
                        <p class="mt-2 text-sm">
                            Podsetnici:
                            {{
                                contact.reminders.length
                                    ? contact.reminders
                                          .map((d) =>
                                              d === 0
                                                  ? 'isti dan'
                                                  : `${d} dana pre`,
                                          )
                                          .join(', ')
                                    : 'nema'
                            }}
                        </p>
                    </div>
                    <p
                        v-if="contact.notes"
                        class="mt-6 text-sm leading-6 whitespace-pre-wrap text-slate-600"
                    >
                        {{ contact.notes }}
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>
