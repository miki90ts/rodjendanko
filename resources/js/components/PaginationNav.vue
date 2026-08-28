<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import type { Paginated } from '@/types';

const props = defineProps<{
    paginator: Pick<
        Paginated<unknown>,
        'from' | 'last_page' | 'links' | 'to' | 'total'
    >;
}>();

const isPrevious = (label: string): boolean =>
    label.includes('Previous') || label.includes('&laquo;');

const isNext = (label: string): boolean =>
    label.includes('Next') || label.includes('&raquo;');

const linkLabel = (label: string): string => {
    if (isPrevious(label)) {
        return 'Prethodna';
    }

    if (isNext(label)) {
        return 'Sledeća';
    }

    return label;
};
</script>

<template>
    <div
        v-if="props.paginator.last_page > 1"
        class="flex flex-col items-center justify-between gap-3 sm:flex-row"
    >
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Prikazano {{ props.paginator.from }}–{{ props.paginator.to }} od
            {{ props.paginator.total }}
        </p>

        <nav
            class="flex flex-wrap justify-center gap-1"
            aria-label="Paginacija"
        >
            <template v-for="link in props.paginator.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="inline-flex min-h-10 min-w-10 items-center justify-center gap-1 rounded-xl border px-3 text-sm font-semibold transition"
                    :class="
                        link.active
                            ? 'border-violet-600 bg-gradient-to-r from-violet-600 to-fuchsia-500 text-white shadow-sm'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-violet-300 hover:text-violet-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300'
                    "
                    :aria-current="link.active ? 'page' : undefined"
                >
                    <ChevronLeft v-if="isPrevious(link.label)" class="size-4" />
                    <span
                        :class="
                            isPrevious(link.label) || isNext(link.label)
                                ? 'hidden sm:inline'
                                : ''
                        "
                    >
                        {{ linkLabel(link.label) }}
                    </span>
                    <ChevronRight v-if="isNext(link.label)" class="size-4" />
                </Link>
                <span
                    v-else
                    class="inline-flex min-h-10 min-w-10 cursor-not-allowed items-center justify-center gap-1 rounded-xl border border-slate-200 bg-white/50 px-3 text-sm text-slate-300 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-600"
                >
                    <ChevronLeft v-if="isPrevious(link.label)" class="size-4" />
                    <span
                        :class="
                            isPrevious(link.label) || isNext(link.label)
                                ? 'hidden sm:inline'
                                : ''
                        "
                    >
                        {{ linkLabel(link.label) }}
                    </span>
                    <ChevronRight v-if="isNext(link.label)" class="size-4" />
                </span>
            </template>
        </nav>
    </div>
</template>
