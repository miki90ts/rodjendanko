<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    CalendarHeart,
    CakeSlice,
    CheckCircle2,
    Clock3,
    Plus,
    Send,
} from '@lucide/vue';
import BirthdayCard from '@/components/birthdays/BirthdayCard.vue';
import EmptyBirthdays from '@/components/birthdays/EmptyBirthdays.vue';
import type { BirthdayContact } from '@/types';

defineProps<{
    today: BirthdayContact[];
    upcoming: BirthdayContact[];
    nextBirthday: BirthdayContact | null;
    stats: {
        contacts: number;
        this_month: number;
        scheduled: number;
        sent: number;
    };
}>();
defineOptions({
    layout: { breadcrumbs: [{ title: 'Pregled', href: '/dashboard' }] },
});
const statCards = [
    {
        key: 'contacts' as const,
        label: 'Ukupno kontakata',
        icon: CalendarHeart,
        color: 'text-violet-600 bg-violet-100',
        from: '#7c3aed',
        to: 'rgba(237, 233, 254, 0.95)',
    },
    {
        key: 'this_month' as const,
        label: 'Ovog meseca',
        icon: CakeSlice,
        color: 'text-rose-600 bg-rose-100',
        from: '#e11d48',
        to: 'rgba(255, 228, 230, 0.95)',
    },
    {
        key: 'scheduled' as const,
        label: 'Zakazano',
        icon: Clock3,
        color: 'text-amber-600 bg-amber-100',
        from: '#d97706',
        to: 'rgba(254, 243, 199, 0.95)',
    },
    {
        key: 'sent' as const,
        label: 'Uspešno poslato',
        icon: CheckCircle2,
        color: 'text-emerald-600 bg-emerald-100',
        from: '#059669',
        to: 'rgba(209, 250, 229, 0.95)',
    },
];
</script>

<template>
    <Head title="Pregled" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl space-y-8">
            <header
                class="page-header-card flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p class="text-sm font-semibold text-violet-600">
                        Dobro došao/la nazad
                    </p>
                    <h1
                        class="mt-1 text-3xl font-bold tracking-tight text-slate-950 dark:text-white"
                    >
                        Važni dani, bez zaboravljanja.
                    </h1>
                    <p class="mt-2 text-slate-500">
                        Sve čestitke i podsetnici na jednom mirnom mestu.
                    </p>
                </div>
                <Link href="/birthdays/create" class="btn-primary"
                    ><Plus class="size-4" /> Brzo dodaj</Link
                >
            </header>
            <EmptyBirthdays v-if="stats.contacts === 0" />
            <template v-else>
                <section
                    v-if="today.length"
                    class="relative overflow-hidden rounded-3xl bg-linear-to-br from-violet-600 via-fuchsia-600 to-rose-500 p-6 text-white shadow-xl shadow-violet-200/40 sm:p-8 dark:shadow-none"
                >
                    <div
                        class="absolute -top-16 -right-10 text-[180px] opacity-10"
                    >
                        🎂
                    </div>
                    <p
                        class="text-sm font-semibold tracking-[.22em] text-violet-100 uppercase"
                    >
                        Danas slavimo
                    </p>
                    <h2 class="mt-2 text-3xl font-bold">
                        {{
                            today.length === 1
                                ? today[0].display_name
                                : `${today.length} posebna rođendana`
                        }}
                    </h2>
                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <BirthdayCard
                            v-for="contact in today"
                            :key="contact.id"
                            :contact="contact"
                            compact
                            class="border-white/20 bg-white/95 text-slate-900"
                        />
                    </div>
                </section>
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div
                        v-for="item in statCards"
                        :key="item.key"
                        class="relative flex items-center justify-between overflow-hidden rounded-3xl border border-border/70 bg-[linear-gradient(180deg,rgba(20,184,166,0.08),rgba(255,255,255,0.95))] p-6 shadow-[0_18px_50px_rgba(15,23,42,0.07)] dark:bg-[linear-gradient(180deg,rgba(20,184,166,0.12),rgba(15,23,42,0.88))]"
                    >
                        <div
                            class="absolute inset-x-0 top-0 h-1"
                            :style="{
                                backgroundImage: `linear-gradient(90deg, ${item.from}, ${item.to})`,
                            }"
                        ></div>
                        <div>
                            <p class="text-sm text-slate-500">
                                {{ item.label }}
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ stats[item.key] }}
                            </p>
                        </div>
                        <div
                            class="grid size-11 place-items-center rounded-2xl"
                            :class="item.color"
                        >
                            <component :is="item.icon" class="size-5" />
                        </div>
                    </div>
                </section>
                <section class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
                    <div
                        class="celebration-card rounded-3xl border bg-white p-5 shadow-sm sm:p-6 dark:bg-slate-950"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-semibold tracking-[.2em] text-violet-600 uppercase"
                                >
                                    Kalendar
                                </p>
                                <h2 class="mt-1 text-xl font-bold">
                                    Nadolazeći rođendani
                                </h2>
                            </div>
                            <Link
                                href="/birthdays"
                                class="text-sm font-semibold text-violet-600"
                                >Prikaži sve →</Link
                            >
                        </div>
                        <div class="mt-5 grid gap-3 md:grid-cols-2">
                            <BirthdayCard
                                v-for="contact in upcoming"
                                :key="contact.id"
                                :contact="contact"
                            />
                        </div>
                        <p
                            v-if="!upcoming.length"
                            class="py-12 text-center text-sm text-slate-500"
                        >
                            Nema rođendana u narednih 30 dana.
                        </p>
                    </div>
                    <aside
                        class="celebration-card rounded-3xl bg-white p-6 shadow-sm dark:bg-violet-950"
                    >
                        <p
                            class="text-xs font-semibold tracking-[.2em] text-violet-800 uppercase"
                        >
                            Sledeći rođendan
                        </p>
                        <template v-if="nextBirthday"
                            ><div
                                class="mt-8 text-6xl font-black text-violet-800"
                            >
                                {{ nextBirthday.days_until }}
                            </div>
                            <p class="mt-1 text-sm text-slate-800">
                                dana do proslave
                            </p>
                            <h3 class="mt-7 text-2xl font-bold">
                                {{ nextBirthday.display_name }}
                            </h3>
                            <p class="mt-1 text-slate-400">
                                Puni {{ nextBirthday.turning_age }} godina
                            </p>
                            <div
                                v-if="nextBirthday.settings?.auto_send_enabled"
                                class="mt-6 flex items-center gap-2 rounded-2xl bg-white/10 p-4 text-sm"
                            >
                                <Send class="size-4 text-emerald-300" /><span
                                    >Čestitka ide automatski u
                                    {{
                                        nextBirthday.settings.send_time ||
                                        '09:00'
                                    }}</span
                                >
                            </div></template
                        >
                    </aside>
                </section>
            </template>
        </div>
    </main>
</template>
