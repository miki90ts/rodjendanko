<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CakeSlice, ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed, ref } from 'vue';
import type { BirthdayContact } from '@/types';

const props = defineProps<{ contacts: BirthdayContact[] }>();

const today = new Date();
today.setHours(12, 0, 0, 0);

const visibleMonth = ref(
    new Date(today.getFullYear(), today.getMonth(), 1, 12),
);

const weekdays = ['Pon', 'Uto', 'Sre', 'Čet', 'Pet', 'Sub', 'Ned'];

const monthLabel = computed(() =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        month: 'long',
        year: 'numeric',
    }).format(visibleMonth.value),
);

const dateKey = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const contactsByDate = computed(() => {
    const grouped = new Map<string, BirthdayContact[]>();

    props.contacts.forEach((contact) => {
        const contacts = grouped.get(contact.next_birthday) ?? [];
        contacts.push(contact);
        grouped.set(contact.next_birthday, contacts);
    });

    return grouped;
});

const calendarDays = computed(() => {
    const year = visibleMonth.value.getFullYear();
    const month = visibleMonth.value.getMonth();
    const firstDay = new Date(year, month, 1, 12);
    const mondayOffset = (firstDay.getDay() + 6) % 7;
    const lastDay = new Date(year, month + 1, 0, 12);
    const requiredCells = mondayOffset + lastDay.getDate();
    const cellCount = requiredCells > 35 ? 42 : 35;

    return Array.from({ length: cellCount }, (_, index) => {
        const date = new Date(year, month, index - mondayOffset + 1, 12);
        const key = dateKey(date);

        return {
            date,
            key,
            contacts: contactsByDate.value.get(key) ?? [],
            isCurrentMonth: date.getMonth() === month,
            isToday: key === dateKey(today),
        };
    });
});

const changeMonth = (offset: number): void => {
    visibleMonth.value = new Date(
        visibleMonth.value.getFullYear(),
        visibleMonth.value.getMonth() + offset,
        1,
        12,
    );
};

const showCurrentMonth = (): void => {
    visibleMonth.value = new Date(today.getFullYear(), today.getMonth(), 1, 12);
};
</script>

<template>
    <section
        class="celebration-card rounded-3xl border bg-white shadow-sm dark:bg-slate-950"
    >
        <div
            class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6 dark:border-slate-800"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[.18em] text-violet-600 uppercase"
                >
                    Mesečni kalendar
                </p>
                <h2 class="mt-1 text-2xl font-black capitalize">
                    {{ monthLabel }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="btn-secondary px-3"
                    type="button"
                    @click="changeMonth(-1)"
                >
                    <ChevronLeft class="size-4" />
                    <span class="sr-only">Prethodni mesec</span>
                </button>
                <button
                    class="btn-secondary"
                    type="button"
                    @click="showCurrentMonth"
                >
                    Danas
                </button>
                <button
                    class="btn-secondary px-3"
                    type="button"
                    @click="changeMonth(1)"
                >
                    <ChevronRight class="size-4" />
                    <span class="sr-only">Sledeći mesec</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto p-3 sm:p-5">
            <div class="min-w-[760px]">
                <div class="grid grid-cols-7">
                    <div
                        v-for="weekday in weekdays"
                        :key="weekday"
                        class="px-2 py-2 text-center text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >
                        {{ weekday }}
                    </div>
                </div>
                <div
                    class="grid grid-cols-7 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-800"
                >
                    <div
                        v-for="day in calendarDays"
                        :key="day.key"
                        class="relative min-h-32 border-r border-b border-slate-200 bg-white p-2.5 last:border-r-0 dark:border-slate-800 dark:bg-slate-950"
                        :class="{
                            'bg-slate-50/70 text-slate-300 dark:bg-slate-950/40 dark:text-slate-700':
                                !day.isCurrentMonth,
                            'bg-gradient-to-br from-rose-50 to-violet-50 dark:from-rose-950/30 dark:to-violet-950/30':
                                day.contacts.length && day.isCurrentMonth,
                        }"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="grid size-7 place-items-center rounded-full text-xs font-bold"
                                :class="
                                    day.isToday
                                        ? 'bg-violet-600 text-white shadow-sm'
                                        : 'text-slate-500 dark:text-slate-400'
                                "
                            >
                                {{ day.date.getDate() }}
                            </span>
                            <CakeSlice
                                v-if="day.contacts.length"
                                class="size-4 text-rose-500"
                            />
                        </div>
                        <div class="mt-2 space-y-1.5">
                            <Link
                                v-for="contact in day.contacts.slice(0, 3)"
                                :key="contact.id"
                                :href="`/birthdays/${contact.id}`"
                                class="block truncate rounded-lg bg-white px-2 py-1.5 text-xs font-semibold text-violet-700 shadow-sm ring-1 ring-violet-100 transition hover:bg-violet-600 hover:text-white dark:bg-slate-900 dark:text-violet-200 dark:ring-violet-900"
                            >
                                🎂 {{ contact.display_name }}
                            </Link>
                            <p
                                v-if="day.contacts.length > 3"
                                class="px-2 text-[11px] font-semibold text-slate-400"
                            >
                                + još {{ day.contacts.length - 3 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
