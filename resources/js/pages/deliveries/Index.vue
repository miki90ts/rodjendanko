<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { CheckCircle2, Clock3, Mail, RefreshCw, XCircle } from '@lucide/vue';
import PaginationNav from '@/components/PaginationNav.vue';
import type { Paginated } from '@/types';
interface Delivery {
    id: number;
    type: 'reminder' | 'greeting';
    status: 'pending' | 'processing' | 'sent' | 'failed';
    scheduled_for: string;
    sent_at: string | null;
    recipient: string;
    rendered_message: string | null;
    error: string | null;
    contact: {
        first_name: string;
        last_name: string | null;
        nickname: string | null;
    };
    email_template: { name: string } | null;
}
defineProps<{ deliveries: Paginated<Delivery> }>();
const statusConfig = {
    pending: {
        label: 'Na čekanju',
        icon: Clock3,
        class: 'bg-amber-50 text-amber-700',
    },
    processing: {
        label: 'Šalje se',
        icon: RefreshCw,
        class: 'bg-blue-50 text-blue-700',
    },
    sent: {
        label: 'Poslato',
        icon: CheckCircle2,
        class: 'bg-emerald-50 text-emerald-700',
    },
    failed: {
        label: 'Neuspešno',
        icon: XCircle,
        class: 'bg-rose-50 text-rose-700',
    },
};
const displayName = (item: Delivery): string =>
    item.contact.nickname ||
    `${item.contact.first_name} ${item.contact.last_name || ''}`.trim();
const formatDate = (value: string): string =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Istorija slanja', href: '/deliveries' }],
    },
});
</script>
<template>
    <Head title="Istorija slanja" />
    <main class="min-h-full bg-transparent p-4 sm:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header class="page-header-card">
                <p class="text-sm font-semibold text-violet-600">
                    Evidencija dostave
                </p>
                <h1 class="text-3xl font-bold">Istorija slanja</h1>
                <p class="mt-2 text-slate-500">
                    Pregled svake čestitke i podsetnika, uključujući greške i
                    ponovno slanje.
                </p>
            </header>
            <div
                class="celebration-card overflow-hidden rounded-2xl border bg-white shadow-sm dark:bg-slate-950"
            >
                <div
                    v-for="delivery in deliveries.data"
                    :key="delivery.id"
                    class="grid gap-4 border-b p-5 last:border-0 md:grid-cols-[1fr_160px_150px_auto] md:items-center"
                >
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <Mail class="size-4 text-violet-600" />
                            <p class="font-semibold">
                                {{ displayName(delivery) }}
                            </p>
                            <span class="text-xs text-slate-400">{{
                                delivery.type === 'greeting'
                                    ? 'Čestitka'
                                    : 'Podsetnik'
                            }}</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-500">
                            {{ delivery.rendered_message }}
                        </p>
                        <p
                            v-if="delivery.error"
                            class="mt-2 text-xs text-rose-600"
                        >
                            {{ delivery.error }}
                        </p>
                    </div>
                    <div class="text-sm">
                        <p class="text-slate-400">Zakazano</p>
                        <p class="mt-1 font-medium">
                            {{ formatDate(delivery.scheduled_for) }}
                        </p>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="statusConfig[delivery.status].class"
                            ><component
                                :is="statusConfig[delivery.status].icon"
                                class="size-3.5"
                            />{{ statusConfig[delivery.status].label }}</span
                        >
                        <p
                            v-if="delivery.email_template"
                            class="mt-2 text-xs text-slate-400"
                        >
                            {{ delivery.email_template.name }}
                        </p>
                    </div>
                    <button
                        v-if="delivery.status === 'failed'"
                        class="btn-secondary"
                        @click="router.post(`/deliveries/${delivery.id}/retry`)"
                    >
                        <RefreshCw class="size-4" /> Pokušaj ponovo
                    </button>
                </div>
                <div
                    v-if="!deliveries.data.length"
                    class="py-16 text-center text-slate-500"
                >
                    Još nema zabeleženih slanja.
                </div>
            </div>
            <PaginationNav :paginator="deliveries" />
        </div>
    </main>
</template>
