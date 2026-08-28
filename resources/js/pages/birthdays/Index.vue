<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarDays,
    LayoutGrid,
    Plus,
    Search,
    SlidersHorizontal,
} from '@lucide/vue';
import { onMounted, reactive, ref, watch } from 'vue';
import BirthdayCalendar from '@/components/birthdays/BirthdayCalendar.vue';
import BirthdayCard from '@/components/birthdays/BirthdayCard.vue';
import EmptyBirthdays from '@/components/birthdays/EmptyBirthdays.vue';
import PaginationNav from '@/components/PaginationNav.vue';
import { relationshipOptions } from '@/lib/birthdayOptions';
import type { BirthdayContact, Paginated } from '@/types';

type ViewMode = 'cards' | 'calendar';

const props = defineProps<{
    contacts: Paginated<BirthdayContact>;
    calendarContacts: BirthdayContact[];
    filters: {
        search?: string;
        relationship?: string;
        auto_send?: boolean;
        date_filter?: string;
    };
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    relationship: props.filters.relationship ?? '',
    date_filter: props.filters.date_filter ?? '',
    auto_send: Boolean(props.filters.auto_send),
});

const viewMode = ref<ViewMode>('cards');

onMounted(() => {
    const savedView = window.localStorage.getItem('birthday-view');

    if (savedView === 'cards' || savedView === 'calendar') {
        viewMode.value = savedView;
    }
});

watch(viewMode, (value) => window.localStorage.setItem('birthday-view', value));

let timeout: ReturnType<typeof setTimeout>;
watch(filters, () => {
    clearTimeout(timeout);
    timeout = setTimeout(
        () =>
            router.get('/birthdays', filters, {
                preserveState: true,
                replace: true,
            }),
        250,
    );
});

defineOptions({
    layout: { breadcrumbs: [{ title: 'Rođendani', href: '/birthdays' }] },
});
</script>

<template>
    <Head title="Rođendani" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header
                class="page-header-card flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p class="text-sm font-semibold text-violet-600">Ljudi</p>
                    <h1 class="text-3xl font-bold">Rođendani</h1>
                    <p class="mt-1 text-slate-500">
                        {{ contacts.total }} važnih datuma na jednom mestu.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="flex rounded-xl border border-violet-100 bg-violet-50 p-1 dark:border-violet-900 dark:bg-violet-950/50"
                        aria-label="Način prikaza"
                    >
                        <button
                            type="button"
                            class="inline-flex h-9 items-center gap-2 rounded-lg px-3 text-sm font-semibold transition"
                            :class="
                                viewMode === 'cards'
                                    ? 'bg-white text-violet-700 shadow-sm dark:bg-slate-900 dark:text-violet-200'
                                    : 'text-slate-500 hover:text-violet-700'
                            "
                            @click="viewMode = 'cards'"
                        >
                            <LayoutGrid class="size-4" /> Kartice
                        </button>
                        <button
                            type="button"
                            class="inline-flex h-9 items-center gap-2 rounded-lg px-3 text-sm font-semibold transition"
                            :class="
                                viewMode === 'calendar'
                                    ? 'bg-white text-violet-700 shadow-sm dark:bg-slate-900 dark:text-violet-200'
                                    : 'text-slate-500 hover:text-violet-700'
                            "
                            @click="viewMode = 'calendar'"
                        >
                            <CalendarDays class="size-4" /> Kalendar
                        </button>
                    </div>
                    <Link href="/birthdays/create" class="btn-primary">
                        <Plus class="size-4" /> Dodaj rođendan
                    </Link>
                </div>
            </header>

            <div
                class="celebration-card grid gap-3 rounded-2xl border bg-white p-4 shadow-sm md:grid-cols-[1fr_180px_180px_auto] dark:bg-slate-950"
            >
                <label class="relative">
                    <Search
                        class="absolute top-3 left-3 size-4 text-slate-400"
                    />
                    <input
                        v-model="filters.search"
                        class="field pl-10"
                        placeholder="Pretraži ime, nadimak ili email…"
                    />
                </label>
                <label class="relative">
                    <SlidersHorizontal
                        class="absolute top-3 left-3 size-4 text-slate-400"
                    />
                    <select v-model="filters.relationship" class="field pl-10">
                        <option value="">Svi odnosi</option>
                        <option
                            v-for="option in relationshipOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </label>
                <select v-model="filters.date_filter" class="field">
                    <option value="">Svi datumi</option>
                    <option value="upcoming">Narednih 30 dana</option>
                    <option value="this_month">Ovog meseca</option>
                </select>
                <label
                    class="flex items-center gap-2 rounded-xl bg-slate-50 px-4 text-sm font-medium dark:bg-slate-900"
                >
                    <input
                        v-model="filters.auto_send"
                        type="checkbox"
                        class="accent-violet-600"
                    />
                    Automatsko slanje
                </label>
            </div>

            <EmptyBirthdays
                v-if="
                    !calendarContacts.length &&
                    !filters.search &&
                    !filters.relationship &&
                    !filters.auto_send &&
                    !filters.date_filter
                "
            />

            <template v-else-if="calendarContacts.length">
                <template v-if="viewMode === 'cards'">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <BirthdayCard
                            v-for="contact in contacts.data"
                            :key="contact.id"
                            :contact="contact"
                        />
                    </div>
                    <PaginationNav :paginator="contacts" />
                </template>
                <BirthdayCalendar v-else :contacts="calendarContacts" />
            </template>

            <div
                v-else
                class="celebration-card rounded-2xl border bg-white py-16 text-center text-slate-500 dark:bg-slate-950"
            >
                Nema rezultata za izabrane filtere.
            </div>
        </div>
    </main>
</template>
