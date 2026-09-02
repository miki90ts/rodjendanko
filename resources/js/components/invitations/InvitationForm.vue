<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    ImagePlus,
    LocateFixed,
    MailPlus,
    MapPin,
    Send,
    Sparkles,
    Trash2,
    Users,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import LocalizedDatePicker from '@/components/LocalizedDatePicker.vue';
import LocalizedDateTimePicker from '@/components/LocalizedDateTimePicker.vue';
import type {
    BirthdayInvitation,
    InvitationGuest,
    InvitationTemplateOption,
} from '@/types';

const props = defineProps<{
    invitation?: BirthdayInvitation;
    templates: InvitationTemplateOption[];
    timezones: string[];
    defaults: { host_name: string; timezone: string };
}>();

const toDateTimeInput = (
    value: string | null | undefined,
    timezone: string,
): string => {
    if (!value) {
        return '';
    }

    const parts = Object.fromEntries(
        new Intl.DateTimeFormat('en-CA', {
            timeZone: timezone,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hourCycle: 'h23',
        })
            .formatToParts(new Date(value))
            .map((part) => [part.type, part.value]),
    );

    return `${parts.year}-${parts.month}-${parts.day}T${parts.hour}:${parts.minute}`;
};

const form = useForm({
    title: props.invitation?.title ?? 'Dođite da zajedno proslavimo!',
    honoree_name: props.invitation?.honoree_name ?? '',
    celebrant_age: props.invitation?.celebrant_age ?? null,
    starts_at: toDateTimeInput(
        props.invitation?.starts_at,
        props.invitation?.timezone ?? props.defaults.timezone,
    ),
    ends_at: toDateTimeInput(
        props.invitation?.ends_at,
        props.invitation?.timezone ?? props.defaults.timezone,
    ),
    timezone: props.invitation?.timezone ?? props.defaults.timezone,
    venue_name: props.invitation?.venue_name ?? '',
    address: props.invitation?.address ?? '',
    city: props.invitation?.city ?? '',
    latitude: props.invitation?.latitude ?? null,
    longitude: props.invitation?.longitude ?? null,
    description:
        props.invitation?.description ??
        'Čeka nas divan dan ispunjen osmesima, muzikom i lepim uspomenama. Radujemo se da ga podelimo sa vama!',
    dress_code: props.invitation?.dress_code ?? '',
    gift_note: props.invitation?.gift_note ?? '',
    host_name: props.invitation?.host_name ?? props.defaults.host_name,
    host_phone: props.invitation?.host_phone ?? '',
    reply_deadline: props.invitation?.reply_deadline?.slice(0, 10) ?? '',
    template: props.invitation?.template ?? 'cheerful',
    background_image: null as File | null,
    allow_plus_one: props.invitation?.allow_plus_one ?? false,
    max_guests: props.invitation?.max_guests ?? null,
    is_published: props.invitation?.is_published ?? true,
    send_now: !props.invitation,
    guests:
        props.invitation?.guests?.map((guest) => ({
            name: guest.name ?? '',
            email: guest.email,
        })) ?? ([] as Pick<InvitationGuest, 'name' | 'email'>[]),
});

const guestInput = ref('');
const customImagePreview = ref<string | null>(null);
const locationMessage = ref('');

watch(
    () => form.is_published,
    (isPublished) => {
        if (!isPublished) {
            form.send_now = false;
        }
    },
);

const selectedTemplate = computed(() =>
    props.templates.find((template) => template.value === form.template),
);

const backgroundPreview = computed(
    () =>
        customImagePreview.value ??
        props.invitation?.background_url ??
        selectedTemplate.value?.background_url,
);

const addGuests = (): void => {
    const entries = guestInput.value
        .split(/[\n,;]+/)
        .map((entry) => entry.trim())
        .filter(Boolean);

    entries.forEach((entry) => {
        const namedMatch = entry.match(/^(.*?)\s*<([^>]+)>$/);
        const email = (namedMatch?.[2] ?? entry).trim().toLowerCase();
        const name = namedMatch?.[1]?.trim() ?? '';

        if (
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) &&
            !form.guests.some((guest) => guest.email === email)
        ) {
            form.guests.push({ name, email });
        }
    });

    guestInput.value = '';
};

const removeGuest = (index: number): void => {
    form.guests.splice(index, 1);
};

const chooseBackground = (event: Event): void => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.background_image = file;

    if (customImagePreview.value) {
        URL.revokeObjectURL(customImagePreview.value);
    }

    customImagePreview.value = file ? URL.createObjectURL(file) : null;
};

const useCurrentLocation = (): void => {
    if (!navigator.geolocation) {
        locationMessage.value = 'Ovaj pregledač ne podržava lokaciju.';

        return;
    }

    locationMessage.value = 'Tražimo lokaciju…';
    navigator.geolocation.getCurrentPosition(
        ({ coords }) => {
            form.latitude = Number(coords.latitude.toFixed(7));
            form.longitude = Number(coords.longitude.toFixed(7));
            locationMessage.value = 'Koordinate su dodate.';
        },
        () => {
            locationMessage.value =
                'Lokacija nije dostupna. Koordinate možeš uneti ručno.';
        },
    );
};

const submit = (): void => {
    if (props.invitation) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/invitations/${props.invitation.id}`,
            { forceFormData: true, preserveScroll: true },
        );

        return;
    }

    form.post('/invitations', { forceFormData: true, preserveScroll: true });
};
</script>

<template>
    <form class="mx-auto max-w-7xl space-y-6" @submit.prevent="submit">
        <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
            <div class="space-y-6">
                <section
                    class="celebration-card rounded-3xl border bg-white p-6 shadow-sm dark:bg-slate-950"
                >
                    <div class="mb-5 flex items-start gap-3">
                        <div
                            class="grid size-11 place-items-center rounded-2xl bg-rose-100 text-rose-600"
                        >
                            <Sparkles class="size-5" />
                        </div>
                        <div>
                            <h2 class="text-xl font-black">
                                Slavljenik i datum
                            </h2>
                            <p class="text-sm text-slate-500">
                                Osnovne informacije koje gosti prvo vide.
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-semibold"
                                >Naslov pozivnice</span
                            >
                            <input
                                v-model="form.title"
                                class="field"
                                required
                            />
                            <InputError :message="form.errors.title" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Ime slavljenika</span
                            >
                            <input
                                v-model="form.honoree_name"
                                class="field"
                                required
                            />
                            <InputError :message="form.errors.honoree_name" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Puni godina</span
                            >
                            <input
                                v-model="form.celebrant_age"
                                class="field"
                                type="number"
                                min="1"
                                max="150"
                            />
                            <InputError :message="form.errors.celebrant_age" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold">Početak</span>
                            <LocalizedDateTimePicker
                                v-model="form.starts_at"
                                required
                                date-placeholder="Datum početka"
                                time-placeholder="Vreme početka"
                            />
                            <InputError :message="form.errors.starts_at" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Kraj (opciono)</span
                            >
                            <LocalizedDateTimePicker
                                v-model="form.ends_at"
                                date-placeholder="Datum završetka"
                                time-placeholder="Vreme završetka"
                            />
                            <InputError :message="form.errors.ends_at" />
                        </label>
                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-semibold"
                                >Vremenska zona</span
                            >
                            <select v-model="form.timezone" class="field">
                                <option
                                    v-for="timezone in timezones"
                                    :key="timezone"
                                    :value="timezone"
                                >
                                    {{ timezone }}
                                </option>
                            </select>
                            <InputError :message="form.errors.timezone" />
                        </label>
                    </div>
                </section>

                <section
                    class="celebration-card rounded-3xl border bg-white p-6 shadow-sm dark:bg-slate-950"
                >
                    <div class="mb-5 flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div
                                class="grid size-11 place-items-center rounded-2xl bg-violet-100 text-violet-600"
                            >
                                <MapPin class="size-5" />
                            </div>
                            <div>
                                <h2 class="text-xl font-black">
                                    Mesto proslave
                                </h2>
                                <p class="text-sm text-slate-500">
                                    Adresa pravi link do mape, a koordinate
                                    prikazuju mapu na stranici.
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn-secondary shrink-0"
                            @click="useCurrentLocation"
                        >
                            <LocateFixed class="size-4" /> Moja lokacija
                        </button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-semibold"
                                >Naziv mesta</span
                            >
                            <input
                                v-model="form.venue_name"
                                class="field"
                                placeholder="Igraonica Zvezdica"
                                required
                            />
                            <InputError :message="form.errors.venue_name" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Ulica i broj</span
                            >
                            <input
                                v-model="form.address"
                                class="field"
                                placeholder="Knez Mihailova 10"
                                required
                            />
                            <InputError :message="form.errors.address" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Mesto / grad</span
                            >
                            <input
                                v-model="form.city"
                                class="field"
                                placeholder="Beograd"
                                required
                            />
                            <InputError :message="form.errors.city" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Geografska širina</span
                            >
                            <input
                                v-model="form.latitude"
                                class="field"
                                type="number"
                                step="0.0000001"
                                placeholder="44.8176"
                            />
                            <InputError :message="form.errors.latitude" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Geografska dužina</span
                            >
                            <input
                                v-model="form.longitude"
                                class="field"
                                type="number"
                                step="0.0000001"
                                placeholder="20.4633"
                            />
                            <InputError :message="form.errors.longitude" />
                        </label>
                    </div>
                    <p
                        v-if="locationMessage"
                        class="mt-3 text-xs font-semibold text-violet-600"
                    >
                        {{ locationMessage }}
                    </p>
                </section>

                <section
                    class="celebration-card rounded-3xl border bg-white p-6 shadow-sm dark:bg-slate-950"
                >
                    <h2 class="text-xl font-black">
                        Tekst i dodatne informacije
                    </h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-semibold"
                                >Poruka gostima</span
                            >
                            <textarea
                                v-model="form.description"
                                rows="5"
                                class="field h-auto py-3"
                            ></textarea>
                            <InputError :message="form.errors.description" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Dress code (opciono)</span
                            >
                            <input
                                v-model="form.dress_code"
                                class="field"
                                placeholder="Veselo i šareno"
                            />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Napomena za poklone</span
                            >
                            <input
                                v-model="form.gift_note"
                                class="field"
                                placeholder="Najvažnije je da dođete"
                            />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Ime domaćina</span
                            >
                            <input
                                v-model="form.host_name"
                                class="field"
                                required
                            />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Telefon domaćina</span
                            >
                            <input v-model="form.host_phone" class="field" />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Rok za odgovor</span
                            >
                            <LocalizedDatePicker
                                v-model="form.reply_deadline"
                                :max="form.starts_at.slice(0, 10) || undefined"
                                placeholder="Izaberi krajnji rok"
                            />
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold"
                                >Maksimalno gostiju</span
                            >
                            <input
                                v-model="form.max_guests"
                                class="field"
                                type="number"
                                min="1"
                            />
                        </label>
                    </div>
                    <label
                        class="mt-5 flex items-center gap-3 rounded-2xl bg-violet-50 p-4 dark:bg-violet-950/40"
                    >
                        <input
                            v-model="form.allow_plus_one"
                            type="checkbox"
                            class="size-4 accent-violet-600"
                        />
                        <span
                            ><strong class="block text-sm"
                                >Dozvoli pratnju</strong
                            ><span class="text-xs text-slate-500"
                                >Gost može potvrditi dolazak za dve osobe.</span
                            ></span
                        >
                    </label>
                </section>

                <section
                    class="celebration-card rounded-3xl border bg-white p-6 shadow-sm dark:bg-slate-950"
                >
                    <div class="mb-5 flex items-start gap-3">
                        <div
                            class="grid size-11 place-items-center rounded-2xl bg-emerald-100 text-emerald-600"
                        >
                            <Users class="size-5" />
                        </div>
                        <div>
                            <h2 class="text-xl font-black">Lista gostiju</h2>
                            <p class="text-sm text-slate-500">
                                Dodaj do 300 adresa. Možeš koristiti format „Ana
                                &lt;ana@email.com&gt;“.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <textarea
                            v-model="guestInput"
                            rows="3"
                            class="field h-auto flex-1 py-3"
                            placeholder="ana@email.com, marko@email.com&#10;Petar <petar@email.com>"
                            @keydown.ctrl.enter.prevent="addGuests"
                        ></textarea>
                        <button
                            type="button"
                            class="btn-secondary h-auto min-h-11"
                            @click="addGuests"
                        >
                            <MailPlus class="size-4" /> Dodaj emailove
                        </button>
                    </div>
                    <InputError :message="form.errors.guests" class="mt-2" />
                    <div
                        v-if="form.guests.length"
                        class="mt-5 overflow-hidden rounded-2xl border"
                    >
                        <div
                            v-for="(guest, index) in form.guests"
                            :key="guest.email"
                            class="grid gap-2 border-b p-3 last:border-0 sm:grid-cols-[1fr_1.4fr_auto] sm:items-center"
                        >
                            <input
                                v-model="guest.name"
                                class="field"
                                placeholder="Ime (opciono)"
                            />
                            <p class="truncate px-2 text-sm font-semibold">
                                {{ guest.email }}
                            </p>
                            <button
                                type="button"
                                class="grid size-10 cursor-pointer place-items-center rounded-xl text-rose-500 hover:bg-rose-50"
                                title="Ukloni gosta"
                                @click="removeGuest(index)"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>
                    <p
                        v-else
                        class="mt-5 rounded-2xl border border-dashed py-8 text-center text-sm text-slate-500"
                    >
                        Još nisi dodao/la goste.
                    </p>
                </section>
            </div>

            <div class="space-y-6 xl:sticky xl:top-24 xl:self-start">
                <section
                    class="celebration-card rounded-3xl border bg-white p-6 shadow-sm dark:bg-slate-950"
                >
                    <h2 class="text-xl font-black">Izgled pozivnice</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Izaberi temu prema uzrastu i stilu proslave.
                    </p>
                    <div
                        class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2"
                    >
                        <button
                            v-for="template in templates"
                            :key="template.value"
                            type="button"
                            class="overflow-hidden rounded-2xl border-2 text-left transition"
                            :class="
                                form.template === template.value
                                    ? 'border-violet-500 ring-4 ring-violet-100'
                                    : 'border-slate-200 hover:border-violet-300'
                            "
                            @click="form.template = template.value"
                        >
                            <div
                                class="h-24 bg-cover bg-center"
                                :style="{
                                    backgroundImage: `url(${template.background_url})`,
                                }"
                            ></div>
                            <div class="p-3">
                                <p class="text-sm font-bold">
                                    {{ template.label }}
                                </p>
                                <p
                                    class="mt-1 text-xs leading-5 text-slate-500"
                                >
                                    {{ template.description }}
                                </p>
                            </div>
                        </button>
                    </div>
                    <label
                        class="mt-5 block cursor-pointer rounded-2xl border border-dashed border-violet-300 bg-violet-50/50 p-4 text-center hover:bg-violet-50 dark:bg-violet-950/20"
                    >
                        <ImagePlus class="mx-auto size-5 text-violet-600" />
                        <span class="mt-2 block text-sm font-bold"
                            >Dodaj svoju pozadinsku sliku</span
                        >
                        <span class="text-xs text-slate-500"
                            >JPG, PNG ili WebP do 8 MB</span
                        >
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                            @change="chooseBackground"
                        />
                    </label>
                    <InputError
                        :message="form.errors.background_image"
                        class="mt-2"
                    />
                </section>

                <section
                    class="celebration-card overflow-hidden rounded-3xl border bg-white shadow-xl dark:bg-slate-950"
                >
                    <div
                        class="min-h-72 bg-cover bg-center p-6"
                        :style="{
                            backgroundImage: `url(${backgroundPreview})`,
                        }"
                    >
                        <div
                            class="mx-auto max-w-md rounded-3xl bg-white/90 p-6 text-center shadow-xl backdrop-blur-sm"
                        >
                            <p
                                class="text-xs font-bold tracking-[.2em] text-violet-600 uppercase"
                            >
                                Pozivnica
                            </p>
                            <h3 class="mt-3 text-3xl font-black text-slate-900">
                                {{ form.title || 'Rođendanska proslava' }}
                            </h3>
                            <p class="mt-3 text-slate-600">
                                Slavimo
                                <strong>{{
                                    form.honoree_name || 'poseban dan'
                                }}</strong
                                ><span v-if="form.celebrant_age">
                                    i {{ form.celebrant_age }}. rođendan</span
                                >!
                            </p>
                            <div
                                class="mt-5 rounded-2xl bg-white/90 p-4 text-sm text-slate-600"
                            >
                                <p>
                                    📅
                                    {{
                                        form.starts_at
                                            ? new Date(
                                                  form.starts_at,
                                              ).toLocaleString('sr-Latn-RS', {
                                                  dateStyle: 'medium',
                                                  timeStyle: 'short',
                                              })
                                            : 'Datum i vreme'
                                    }}
                                </p>
                                <p class="mt-2">
                                    📍 {{ form.venue_name || 'Mesto proslave' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    class="celebration-card rounded-3xl border bg-white p-5 shadow-sm dark:bg-slate-950"
                >
                    <label class="flex items-center gap-3"
                        ><input
                            v-model="form.is_published"
                            type="checkbox"
                            class="size-4 accent-violet-600"
                        /><span
                            ><strong class="block text-sm"
                                >Javna stranica je aktivna</strong
                            ><span class="text-xs text-slate-500"
                                >Ko ima privatni link može otvoriti
                                pozivnicu.</span
                            ></span
                        ></label
                    >
                    <label class="mt-4 flex items-center gap-3"
                        ><input
                            v-model="form.send_now"
                            type="checkbox"
                            :disabled="!form.is_published"
                            class="size-4 accent-violet-600"
                        /><span
                            ><strong class="block text-sm"
                                >Pošalji emailove odmah</strong
                            ><span class="text-xs text-slate-500"
                                >Emailovi se stavljaju u red nakon
                                čuvanja.</span
                            ></span
                        ></label
                    >
                    <button
                        class="btn-primary mt-5 h-12 w-full"
                        :disabled="form.processing || !form.guests.length"
                    >
                        <Send class="size-4" />
                        {{
                            form.processing
                                ? 'Čuvanje…'
                                : invitation
                                  ? 'Sačuvaj pozivnicu'
                                  : 'Kreiraj pozivnicu'
                        }}
                    </button>
                    <div
                        v-if="form.progress"
                        class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"
                    >
                        <div
                            class="h-full bg-violet-600"
                            :style="{ width: `${form.progress.percentage}%` }"
                        ></div>
                    </div>
                </section>
            </div>
        </div>
    </form>
</template>
