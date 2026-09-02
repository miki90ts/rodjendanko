<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import {
    CalendarDays,
    CheckCircle2,
    Clock3,
    Heart,
    MapPin,
    Navigation,
    PartyPopper,
    Share2,
    Users,
    XCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import type { BirthdayInvitation, InvitationGuest, RsvpStatus } from '@/types';

const props = defineProps<{
    invitation: BirthdayInvitation;
    guest: Pick<
        InvitationGuest,
        'token' | 'name' | 'email' | 'status' | 'party_size' | 'message'
    > | null;
    initialResponse: RsvpStatus | null;
}>();

const currentStatus =
    props.initialResponse ??
    (props.guest?.status === 'attending' ||
    props.guest?.status === 'not_attending'
        ? props.guest.status
        : 'attending');

const form = useForm({
    status: currentStatus,
    party_size: props.guest?.party_size ?? 1,
    message: props.guest?.message ?? '',
});

const shared = ref(false);
const startsAt = computed(() => new Date(props.invitation.starts_at));
const daysUntil = computed(() =>
    Math.max(
        0,
        Math.ceil((startsAt.value.getTime() - Date.now()) / 86_400_000),
    ),
);
const isElegant = computed(() => props.invitation.template === 'elegant');
const rsvpClosed = computed(() => {
    if (!props.invitation.reply_deadline) {
        return false;
    }

    const deadline = new Date(
        `${props.invitation.reply_deadline.slice(0, 10)}T23:59:59`,
    );

    return Date.now() > deadline.getTime();
});

const dateLabel = computed(() =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: props.invitation.timezone,
    }).format(startsAt.value),
);
const timeLabel = computed(() =>
    new Intl.DateTimeFormat('sr-Latn-RS', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: props.invitation.timezone,
    }).format(startsAt.value),
);

const addressQuery = computed(() =>
    encodeURIComponent(
        [
            props.invitation.venue_name,
            props.invitation.address,
            props.invitation.city,
        ]
            .filter(Boolean)
            .join(', '),
    ),
);
const mapLink = computed(
    () =>
        `https://www.google.com/maps/search/?api=1&query=${addressQuery.value}`,
);
const mapEmbed = computed(() => {
    if (
        props.invitation.latitude == null ||
        props.invitation.longitude == null
    ) {
        return null;
    }

    const latitude = Number(props.invitation.latitude);
    const longitude = Number(props.invitation.longitude);
    const delta = 0.008;
    const bbox = [
        longitude - delta,
        latitude - delta,
        longitude + delta,
        latitude + delta,
    ].join(',');

    return `https://www.openstreetmap.org/export/embed.html?bbox=${encodeURIComponent(bbox)}&layer=mapnik&marker=${latitude},${longitude}`;
});

const submitResponse = (): void => {
    if (!props.guest?.token) {
        return;
    }

    form.post(
        `/pozivnica/${props.invitation.public_token}/odgovor/${props.guest.token}`,
        { preserveScroll: true },
    );
};

const shareInvitation = async (): Promise<void> => {
    const shareData = {
        title: props.invitation.title,
        text: `Pozivnica za rođendan: ${props.invitation.title}`,
        url: window.location.href,
    };

    try {
        if (navigator.share) {
            await navigator.share(shareData);
        } else if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(window.location.href);
        }

        shared.value = true;
        window.setTimeout(() => (shared.value = false), 2000);
    } catch {
        // Korisnik može zatvoriti sistemski prozor za deljenje bez posledica.
    }
};
</script>

<template>
    <Head :title="invitation.title" />
    <main
        class="min-h-screen px-4 py-6 text-slate-800 sm:px-6 sm:py-10"
        :class="
            isElegant
                ? 'bg-[#f4efe6]'
                : 'bg-gradient-to-br from-rose-50 via-white to-violet-100'
        "
    >
        <div
            class="mx-auto max-w-5xl overflow-hidden rounded-[2.2rem] border border-white/80 bg-white shadow-2xl shadow-violet-200/40"
        >
            <section
                class="relative min-h-[560px] bg-cover bg-center px-5 py-12 sm:px-10 sm:py-16"
                :style="{
                    backgroundImage: `url(${invitation.background_url})`,
                }"
            >
                <div
                    class="mx-auto max-w-2xl rounded-[2rem] border border-white/80 bg-white/90 p-6 text-center shadow-2xl backdrop-blur-md sm:p-10"
                >
                    <div
                        class="mx-auto grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-white shadow-lg"
                    >
                        <PartyPopper class="size-7" />
                    </div>
                    <p
                        class="mt-6 text-xs font-black tracking-[.28em] text-violet-600 uppercase"
                    >
                        Pozivnica za rođendan
                    </p>
                    <h1
                        class="mt-4 font-serif text-4xl leading-tight font-black text-slate-950 sm:text-6xl"
                    >
                        {{ invitation.title }}
                    </h1>
                    <p class="mt-5 text-lg leading-8 text-slate-600">
                        <template v-if="guest?.name"
                            >Dragi/a
                            <strong class="text-rose-500">{{
                                guest.name
                            }}</strong
                            >,
                        </template>
                        dođi da zajedno proslavimo poseban dan za
                        <strong class="text-violet-700">{{
                            invitation.honoree_name
                        }}</strong
                        ><template v-if="invitation.celebrant_age">
                            i {{ invitation.celebrant_age }}. rođendan</template
                        >!
                    </p>
                    <div
                        class="mt-7 inline-flex items-center gap-2 rounded-full bg-rose-50 px-4 py-2 text-sm font-bold text-rose-600"
                    >
                        <Heart class="size-4 fill-current" /> Još
                        {{ daysUntil }} dana do slavlja
                    </div>
                </div>
            </section>

            <section
                class="grid gap-4 px-5 py-8 sm:grid-cols-3 sm:px-10 sm:py-10"
            >
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 text-center shadow-sm"
                >
                    <CalendarDays class="mx-auto size-6 text-violet-600" />
                    <p
                        class="mt-3 text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >
                        Datum
                    </p>
                    <p class="mt-2 font-black capitalize">{{ dateLabel }}</p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 text-center shadow-sm"
                >
                    <Clock3 class="mx-auto size-6 text-rose-500" />
                    <p
                        class="mt-3 text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >
                        Vreme
                    </p>
                    <p class="mt-2 font-black">{{ timeLabel }}</p>
                </div>
                <div
                    class="celebration-card rounded-3xl border bg-white p-5 text-center shadow-sm"
                >
                    <MapPin class="mx-auto size-6 text-amber-500" />
                    <p
                        class="mt-3 text-xs font-bold tracking-wider text-slate-400 uppercase"
                    >
                        Mesto
                    </p>
                    <p class="mt-2 font-black">{{ invitation.venue_name }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ invitation.address }}, {{ invitation.city }}
                    </p>
                </div>
            </section>

            <section
                v-if="
                    invitation.description ||
                    invitation.dress_code ||
                    invitation.gift_note
                "
                class="mx-5 rounded-3xl bg-gradient-to-br from-violet-50 to-rose-50 p-7 text-center sm:mx-10 sm:p-10"
            >
                <p
                    v-if="invitation.description"
                    class="font-serif text-xl leading-9 whitespace-pre-line text-slate-700 sm:text-2xl"
                >
                    {{ invitation.description }}
                </p>
                <div
                    v-if="invitation.dress_code || invitation.gift_note"
                    class="mt-6 flex flex-wrap justify-center gap-3 text-sm"
                >
                    <span
                        v-if="invitation.dress_code"
                        class="rounded-full bg-white px-4 py-2 font-bold shadow-sm"
                        >👗 {{ invitation.dress_code }}</span
                    >
                    <span
                        v-if="invitation.gift_note"
                        class="rounded-full bg-white px-4 py-2 font-bold shadow-sm"
                        >🎁 {{ invitation.gift_note }}</span
                    >
                </div>
            </section>

            <section class="px-5 py-9 sm:px-10">
                <div
                    class="overflow-hidden rounded-3xl border bg-white shadow-sm"
                >
                    <iframe
                        v-if="mapEmbed"
                        :src="mapEmbed"
                        title="Mapa lokacije proslave"
                        class="h-80 w-full border-0"
                        loading="lazy"
                    ></iframe>
                    <div
                        v-else
                        class="grid h-56 place-items-center bg-gradient-to-br from-slate-100 to-violet-100 text-center"
                    >
                        <div>
                            <Navigation
                                class="mx-auto size-8 text-violet-600"
                            />
                            <p class="mt-3 font-bold">
                                Otvori preciznu lokaciju i navigaciju
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="font-black">
                                {{ invitation.venue_name }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ invitation.address }}, {{ invitation.city }}
                            </p>
                        </div>
                        <a
                            :href="mapLink"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn-secondary"
                            ><Navigation class="size-4" /> Otvori mapu</a
                        >
                    </div>
                </div>
            </section>

            <section
                id="rsvp"
                class="scroll-mt-6 bg-slate-950 px-5 py-10 text-white sm:px-10 sm:py-14"
            >
                <div class="mx-auto max-w-2xl text-center">
                    <p
                        class="text-xs font-bold tracking-[.25em] text-violet-300 uppercase"
                    >
                        RSVP
                    </p>
                    <h2 class="mt-3 text-3xl font-black">Da li dolaziš?</h2>
                    <p class="mt-2 text-slate-400">
                        <template v-if="invitation.reply_deadline"
                            >Odgovori do
                            {{
                                new Date(
                                    invitation.reply_deadline,
                                ).toLocaleDateString('sr-Latn-RS')
                            }}.</template
                        ><template v-else
                            >Tvoj odgovor pomaže domaćinu da pripremi
                            slavlje.</template
                        >
                    </p>

                    <div
                        v-if="rsvpClosed"
                        class="mt-7 rounded-2xl border border-amber-400/30 bg-amber-400/10 p-5 text-amber-100"
                    >
                        Rok za potvrdu je istekao. Za promenu odgovora obrati se
                        domaćinu<span v-if="invitation.host_phone">
                            na {{ invitation.host_phone }}</span
                        >.
                    </div>
                    <form
                        v-else-if="guest"
                        class="mt-7 space-y-5"
                        @submit.prevent="submitResponse"
                    >
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button
                                type="button"
                                class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 px-5 py-4 font-black transition"
                                :class="
                                    form.status === 'attending'
                                        ? 'border-emerald-400 bg-emerald-500 text-white'
                                        : 'border-slate-700 bg-slate-900 text-slate-300'
                                "
                                @click="form.status = 'attending'"
                            >
                                <CheckCircle2 class="size-5" /> Dolazim
                            </button>
                            <button
                                type="button"
                                class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 px-5 py-4 font-black transition"
                                :class="
                                    form.status === 'not_attending'
                                        ? 'border-rose-400 bg-rose-500 text-white'
                                        : 'border-slate-700 bg-slate-900 text-slate-300'
                                "
                                @click="form.status = 'not_attending'"
                            >
                                <XCircle class="size-5" /> Ne mogu da dođem
                            </button>
                        </div>
                        <label
                            v-if="
                                form.status === 'attending' &&
                                invitation.allow_plus_one
                            "
                            class="block text-left"
                            ><span class="mb-2 block text-sm font-bold"
                                >Broj osoba</span
                            ><select
                                v-model="form.party_size"
                                class="field border-slate-700 bg-slate-900 text-white"
                            >
                                <option :value="1">Dolazim sam/a</option>
                                <option :value="2">Dolazimo nas dvoje</option>
                            </select></label
                        >
                        <label class="block text-left"
                            ><span class="mb-2 block text-sm font-bold"
                                >Poruka domaćinu (opciono)</span
                            ><textarea
                                v-model="form.message"
                                rows="3"
                                class="field h-auto border-slate-700 bg-slate-900 py-3 text-white"
                                placeholder="Jedva čekamo! 🎉"
                            ></textarea
                            ><InputError
                                :message="form.errors.message"
                                class="mt-2"
                        /></label>
                        <button
                            class="welcome-submit"
                            :disabled="form.processing"
                        >
                            <Users class="size-5" />
                            {{
                                form.processing ? 'Čuvanje…' : 'Sačuvaj odgovor'
                            }}
                        </button>
                        <p
                            v-if="guest.status && guest.status !== 'pending'"
                            class="text-sm text-emerald-300"
                        >
                            Tvoj prethodni odgovor je sačuvan; možeš ga
                            promeniti.
                        </p>
                    </form>
                    <div
                        v-else
                        class="mt-7 rounded-2xl border border-slate-700 bg-slate-900 p-6 text-sm leading-6 text-slate-300"
                    >
                        Otvori personalizovani link iz email pozivnice da
                        potvrdiš dolazak ili kontaktiraj domaćina
                        <strong class="text-white">{{
                            invitation.host_name
                        }}</strong
                        ><template v-if="invitation.host_phone">
                            na {{ invitation.host_phone }}</template
                        >.
                    </div>
                </div>
            </section>

            <footer
                class="flex flex-col items-center justify-between gap-4 bg-white px-6 py-6 text-center text-sm text-slate-500 sm:flex-row sm:px-10 sm:text-left"
            >
                <p>
                    Sa puno radosti,
                    <strong class="text-violet-700">{{
                        invitation.host_name
                    }}</strong>
                    🎈
                </p>
                <button
                    class="inline-flex cursor-pointer items-center gap-2 font-bold text-violet-600"
                    @click="shareInvitation"
                >
                    <CheckCircle2 v-if="shared" class="size-4" /><Share2
                        v-else
                        class="size-4"
                    />
                    {{ shared ? 'Link je kopiran' : 'Podeli pozivnicu' }}
                </button>
            </footer>
        </div>
    </main>
</template>
