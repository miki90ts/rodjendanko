<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Check,
    Copy,
    ExternalLink,
    Mail,
    MapPin,
    Pencil,
    Send,
    Trash2,
    Users,
    XCircle,
} from '@lucide/vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import DeleteConfirmationModal from '@/components/DeleteConfirmationModal.vue';
import ResendInvitationModal from '@/components/invitations/ResendInvitationModal.vue';
import type { BirthdayInvitation, RsvpStatus } from '@/types';

const props = defineProps<{ invitation: BirthdayInvitation }>();
const page = usePage();
const copied = ref(false);
const deleteModalOpen = ref(false);
const deleting = ref(false);
const sendModalOpen = ref(false);
const sending = ref(false);
const sendAttempts = computed(() => props.invitation.send_batches_count ?? 0);
const maxSendAttempts = computed(() => props.invitation.max_send_attempts ?? 3);
const sendLimitReached = computed(
    () => sendAttempts.value >= maxSendAttempts.value,
);
const latestBatch = computed(() => props.invitation.send_batches?.[0] ?? null);
const sendError = computed(
    () => (page.props.errors as Record<string, string | undefined>).send,
);

const statusConfig: Record<RsvpStatus, { label: string; class: string }> = {
    pending: {
        label: 'Čeka odgovor',
        class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
    },
    attending: {
        label: 'Dolazi',
        class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
    },
    not_attending: {
        label: 'Ne dolazi',
        class: 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
    },
};

const attendingPeople = computed(() =>
    (props.invitation.guests ?? [])
        .filter((guest) => guest.status === 'attending')
        .reduce((total, guest) => total + (guest.party_size ?? 1), 0),
);

const formatDate = (value: string): string =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        dateStyle: 'full',
        timeStyle: 'short',
        timeZone: props.invitation.timezone,
    }).format(new Date(value));

const copyPublicLink = async (): Promise<void> => {
    const url = props.invitation.public_url ?? '';

    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(url);
        } else {
            const input = document.createElement('textarea');
            input.value = url;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
        }

        copied.value = true;
        window.setTimeout(() => (copied.value = false), 2000);
    } catch {
        window.prompt('Kopiraj link:', url);
    }
};

const sendInvitations = (): void => {
    sending.value = true;
    router.post(
        `/invitations/${props.invitation.id}/send`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => (sendModalOpen.value = false),
            onFinish: () => (sending.value = false),
        },
    );
};

const requestSend = (): void => {
    if (sendLimitReached.value) {
        return;
    }

    if (sendAttempts.value > 0) {
        sendModalOpen.value = true;
    } else {
        sendInvitations();
    }
};

const batchStatusLabel = (status: string): string =>
    ({
        pending: 'Slanje je u toku',
        completed: 'Uspešno završeno',
        partial: 'Delimično poslato',
        failed: 'Slanje nije uspelo',
    })[status] ?? 'Nepoznat status';

const formatShortDate = (value: string): string =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: props.invitation.timezone,
    }).format(new Date(value));

let statusPoll: ReturnType<typeof window.setInterval> | null = null;

onMounted(() => {
    statusPoll = window.setInterval(() => {
        if (latestBatch.value?.status === 'pending') {
            router.reload({ only: ['invitation'] });
        }
    }, 2500);
});

onUnmounted(() => {
    if (statusPoll !== null) {
        window.clearInterval(statusPoll);
    }
});

const removeInvitation = (): void => {
    deleting.value = true;
    router.delete(`/invitations/${props.invitation.id}`, {
        onFinish: () => (deleting.value = false),
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pozivnice', href: '/invitations' },
            { title: 'Detalji', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="invitation.title" />
    <main class="min-h-full bg-transparent p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header
                class="page-header-card flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between"
            >
                <div>
                    <p class="text-sm font-semibold text-violet-600">
                        Pozivnica za {{ invitation.honoree_name }}
                    </p>
                    <h1 class="text-3xl font-black">{{ invitation.title }}</h1>
                    <p class="mt-2 text-slate-500">
                        {{ formatDate(invitation.starts_at) }} ·
                        {{ invitation.venue_name }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        class="btn-secondary cursor-pointer"
                        @click="copyPublicLink"
                    >
                        <Check v-if="copied" class="size-4" /><Copy
                            v-else
                            class="size-4"
                        />
                        {{ copied ? 'Kopirano' : 'Kopiraj link' }}
                    </button>
                    <a
                        :href="invitation.public_url"
                        target="_blank"
                        class="btn-secondary"
                        ><ExternalLink class="size-4" /> Otvori stranicu</a
                    >
                    <Link
                        :href="`/invitations/${invitation.id}/edit`"
                        class="btn-secondary"
                        ><Pencil class="size-4" /> Izmeni</Link
                    >
                    <button
                        class="btn-primary cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="
                            !invitation.is_published ||
                            sendLimitReached ||
                            sending
                        "
                        :title="
                            invitation.is_published
                                ? sendLimitReached
                                    ? 'Pozivnica je poslata maksimalna 3 puta'
                                    : 'Pošalji pozivnicu svim gostima'
                                : 'Najpre aktiviraj javnu stranicu u izmeni pozivnice'
                        "
                        @click="requestSend"
                    >
                        <Send class="size-4" />
                        {{
                            sending
                                ? 'Pokretanje…'
                                : sendLimitReached
                                  ? 'Limit 3/3'
                                  : sendAttempts
                                    ? 'Pošalji ponovo'
                                    : 'Pošalji svima'
                        }}
                    </button>
                </div>
            </header>

            <div
                v-if="sendError"
                class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300"
            >
                {{ sendError }}
            </div>

            <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-5">
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <Users class="size-5 text-violet-600" />
                    <p class="mt-4 text-sm text-slate-500">Pozvanih gostiju</p>
                    <p class="mt-1 text-3xl font-black">
                        {{ invitation.guests?.length ?? 0 }}
                    </p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <Check class="size-5 text-emerald-600" />
                    <p class="mt-4 text-sm text-slate-500">Potvrđenih osoba</p>
                    <p class="mt-1 text-3xl font-black">
                        {{ attendingPeople }}
                    </p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <Mail class="size-5 text-amber-600" />
                    <p class="mt-4 text-sm text-slate-500">Čeka odgovor</p>
                    <p class="mt-1 text-3xl font-black">
                        {{
                            invitation.guests?.filter(
                                (guest) => guest.status === 'pending',
                            ).length ?? 0
                        }}
                    </p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <XCircle class="size-5 text-rose-600" />
                    <p class="mt-4 text-sm text-slate-500">Ne dolazi</p>
                    <p class="mt-1 text-3xl font-black">
                        {{
                            invitation.guests?.filter(
                                (guest) => guest.status === 'not_attending',
                            ).length ?? 0
                        }}
                    </p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <Send class="size-5 text-blue-600" />
                    <p class="mt-4 text-sm text-slate-500">Slanja pozivnice</p>
                    <p class="mt-1 text-3xl font-black">
                        {{ sendAttempts }}/{{ maxSendAttempts }}
                    </p>
                    <p v-if="latestBatch" class="mt-1 text-xs text-slate-500">
                        {{ batchStatusLabel(latestBatch.status) }}
                    </p>
                </div>
            </section>

            <section
                v-if="invitation.send_batches?.length"
                class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
            >
                <div class="mb-4">
                    <h2 class="text-lg font-black">Istorija slanja</h2>
                    <p class="text-sm text-slate-500">
                        Pozivnica se može poslati najviše tri puta.
                    </p>
                </div>
                <div class="grid gap-3 md:grid-cols-3">
                    <div
                        v-for="batch in invitation.send_batches"
                        :key="batch.id"
                        class="rounded-2xl border bg-slate-50 p-4 dark:bg-slate-900"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <strong>Slanje {{ batch.attempt_number }}/3</strong>
                            <span class="text-xs text-slate-500">
                                {{ formatShortDate(batch.queued_at) }}
                            </span>
                        </div>
                        <p
                            class="mt-2 text-sm text-slate-600 dark:text-slate-300"
                        >
                            {{ batchStatusLabel(batch.status) }} · uspešno
                            {{ batch.successful_count }}/{{
                                batch.recipient_count
                            }}
                            <template v-if="batch.failed_count">
                                · neuspešno {{ batch.failed_count }}
                            </template>
                        </p>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[.85fr_1.15fr]">
                <div
                    class="celebration-card overflow-hidden rounded-3xl border bg-white shadow-sm dark:bg-slate-950"
                >
                    <div
                        class="min-h-80 bg-cover bg-center p-6"
                        :style="{
                            backgroundImage: `url(${invitation.background_url})`,
                        }"
                    >
                        <div
                            class="rounded-3xl bg-white/90 p-6 text-center shadow-xl backdrop-blur"
                        >
                            <p
                                class="text-xs font-bold tracking-[.2em] text-violet-600 uppercase"
                            >
                                Pozivnica
                            </p>
                            <h2 class="mt-3 text-3xl font-black text-slate-900">
                                {{ invitation.title }}
                            </h2>
                            <p class="mt-3 text-slate-600">
                                Slavimo
                                <strong>{{ invitation.honoree_name }}</strong
                                ><span v-if="invitation.celebrant_age">
                                    i {{ invitation.celebrant_age }}.
                                    rođendan</span
                                >!
                            </p>
                            <p
                                class="mt-5 rounded-2xl bg-white/80 p-4 text-sm text-slate-600"
                            >
                                📅 {{ formatDate(invitation.starts_at)
                                }}<br /><span class="mt-2 inline-block"
                                    >📍 {{ invitation.venue_name }},
                                    {{ invitation.city }}</span
                                >
                            </p>
                        </div>
                    </div>
                    <div class="space-y-3 p-5 text-sm">
                        <p>
                            <MapPin
                                class="mr-2 inline size-4 text-violet-600"
                            />{{ invitation.address }}, {{ invitation.city }}
                        </p>
                        <p v-if="invitation.dress_code">
                            👗 Dress code:
                            <strong>{{ invitation.dress_code }}</strong>
                        </p>
                        <p v-if="invitation.gift_note">
                            🎁 {{ invitation.gift_note }}
                        </p>
                    </div>
                </div>

                <div
                    class="celebration-card overflow-hidden rounded-3xl border bg-white shadow-sm dark:bg-slate-950"
                >
                    <div class="flex items-center justify-between border-b p-5">
                        <div>
                            <h2 class="text-xl font-black">Odgovori gostiju</h2>
                            <p class="text-sm text-slate-500">
                                Status, broj osoba i poruke gostiju.
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700"
                            >{{ invitation.guests?.length ?? 0 }} ukupno</span
                        >
                    </div>
                    <div class="divide-y">
                        <div
                            v-for="guest in invitation.guests"
                            :key="guest.email"
                            class="grid gap-3 p-5 md:grid-cols-[1fr_150px_80px] md:items-center"
                        >
                            <div>
                                <p class="font-bold">
                                    {{ guest.name || guest.email }}
                                </p>
                                <p
                                    v-if="guest.name"
                                    class="text-sm text-slate-500"
                                >
                                    {{ guest.email }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">
                                    <template v-if="guest.send_count">
                                        Email poslat {{ guest.send_count }}
                                        {{
                                            guest.send_count === 1
                                                ? 'put'
                                                : 'puta'
                                        }}
                                        <template v-if="guest.sent_at">
                                            ·
                                            {{ formatShortDate(guest.sent_at) }}
                                        </template>
                                    </template>
                                    <template v-else
                                        >Email još nije poslat</template
                                    >
                                </p>
                                <p
                                    v-if="guest.message"
                                    class="mt-2 rounded-xl bg-slate-50 p-3 text-sm text-slate-600 italic dark:bg-slate-900"
                                >
                                    „{{ guest.message }}“
                                </p>
                            </div>
                            <span
                                class="w-fit rounded-full px-2.5 py-1 text-xs font-bold"
                                :class="
                                    statusConfig[guest.status ?? 'pending']
                                        .class
                                "
                                >{{
                                    statusConfig[guest.status ?? 'pending']
                                        .label
                                }}</span
                            >
                            <p class="text-sm font-semibold text-slate-500">
                                {{
                                    guest.status === 'attending'
                                        ? `${guest.party_size ?? 1} osoba`
                                        : '—'
                                }}
                            </p>
                        </div>
                        <p
                            v-if="!invitation.guests?.length"
                            class="p-12 text-center text-slate-500"
                        >
                            Nema dodatih gostiju.
                        </p>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button
                    class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-rose-600 hover:text-rose-700"
                    @click="deleteModalOpen = true"
                >
                    <Trash2 class="size-4" /> Obriši pozivnicu
                </button>
            </div>
            <DeleteConfirmationModal
                v-model="deleteModalOpen"
                title="Obriši pozivnicu?"
                :description="`Pozivnica „${invitation.title}“, lista gostiju i svi odgovori biće trajno obrisani.`"
                confirm-label="Obriši pozivnicu"
                :processing="deleting"
                @confirm="removeInvitation"
            />
            <ResendInvitationModal
                v-model="sendModalOpen"
                :previous-attempts="sendAttempts"
                :guest-count="invitation.guests?.length ?? 0"
                :processing="sending"
                @confirm="sendInvitations"
            />
        </div>
    </main>
</template>
