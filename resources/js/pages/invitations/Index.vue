<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, CheckCircle2, Clock3, Plus, Users } from '@lucide/vue';
import PaginationNav from '@/components/PaginationNav.vue';
import type { BirthdayInvitation, Paginated } from '@/types';

defineProps<{ invitations: Paginated<BirthdayInvitation> }>();

const formatDate = (value: string, timezone: string): string =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        dateStyle: 'long',
        timeStyle: 'short',
        timeZone: timezone,
    }).format(new Date(value));

defineOptions({
    layout: { breadcrumbs: [{ title: 'Pozivnice', href: '/invitations' }] },
});
</script>

<template>
    <Head title="Rođendanske pozivnice" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header
                class="page-header-card flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p class="text-sm font-semibold text-violet-600">
                        Organizacija proslava
                    </p>
                    <h1 class="text-3xl font-black">Moje pozivnice</h1>
                    <p class="mt-2 text-slate-500">
                        Kreiraj pozivnicu, pošalji je gostima i prati potvrde
                        dolaska.
                    </p>
                </div>
                <Link href="/invitations/create" class="btn-primary"
                    ><Plus class="size-4" /> Nova pozivnica</Link
                >
            </header>

            <div
                v-if="invitations.data.length"
                class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"
            >
                <Link
                    v-for="invitation in invitations.data"
                    :key="invitation.id"
                    :href="`/invitations/${invitation.id}`"
                    class="celebration-card group overflow-hidden rounded-3xl border bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl dark:bg-slate-950"
                >
                    <div
                        class="h-44 bg-cover bg-center"
                        :style="{
                            backgroundImage: `linear-gradient(180deg, transparent, rgba(15,23,42,.58)), url(${invitation.background_url || (invitation.template === 'elegant' ? '/images/invitations/elegant-party.png' : '/images/invitations/kids-party.png')})`,
                        }"
                    >
                        <div class="flex h-full items-end p-5">
                            <div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold text-violet-700"
                                        >{{
                                            invitation.is_published
                                                ? 'Aktivna'
                                                : 'Skica'
                                        }}</span
                                    >
                                    <span
                                        class="rounded-full bg-slate-950/70 px-2.5 py-1 text-xs font-bold text-white"
                                    >
                                        Poslato
                                        {{
                                            invitation.send_batches_count ?? 0
                                        }}/3
                                    </span>
                                </div>
                                <h2 class="mt-3 text-2xl font-black text-white">
                                    {{ invitation.title }}
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <p
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <CalendarDays class="size-4 text-violet-600" />
                            {{
                                formatDate(
                                    invitation.starts_at,
                                    invitation.timezone,
                                )
                            }}
                        </p>
                        <p class="mt-2 truncate text-sm text-slate-500">
                            📍 {{ invitation.venue_name }},
                            {{ invitation.city }}
                        </p>
                        <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                            <div
                                class="rounded-xl bg-slate-50 p-2 dark:bg-slate-900"
                            >
                                <Users class="mx-auto size-4 text-violet-600" />
                                <p class="mt-1 text-lg font-black">
                                    {{ invitation.guests_count }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    gostiju
                                </p>
                            </div>
                            <div
                                class="rounded-xl bg-emerald-50 p-2 dark:bg-emerald-950/30"
                            >
                                <CheckCircle2
                                    class="mx-auto size-4 text-emerald-600"
                                />
                                <p class="mt-1 text-lg font-black">
                                    {{ invitation.attending_count }}
                                </p>
                                <p class="text-[10px] text-slate-400">dolazi</p>
                            </div>
                            <div
                                class="rounded-xl bg-amber-50 p-2 dark:bg-amber-950/30"
                            >
                                <Clock3 class="mx-auto size-4 text-amber-600" />
                                <p class="mt-1 text-lg font-black">
                                    {{ invitation.pending_count }}
                                </p>
                                <p class="text-[10px] text-slate-400">čeka</p>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
            <div
                v-else
                class="celebration-card rounded-3xl border border-dashed bg-white px-6 py-20 text-center dark:bg-slate-950"
            >
                <div class="text-5xl">💌</div>
                <h2 class="mt-5 text-2xl font-black">
                    Prva pozivnica čeka na tebe
                </h2>
                <p class="mx-auto mt-2 max-w-md text-slate-500">
                    Dodaj proslavu, izaberi dizajn i pozovi sve drage ljude na
                    jednom mestu.
                </p>
                <Link href="/invitations/create" class="btn-primary mt-6"
                    ><Plus class="size-4" /> Kreiraj pozivnicu</Link
                >
            </div>
            <PaginationNav :paginator="invitations" />
        </div>
    </main>
</template>
