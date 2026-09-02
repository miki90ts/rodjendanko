<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import {
    ChevronDown,
    Eye,
    LoaderCircle,
    Plus,
    Sparkles,
    Trash2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import EmailTemplateSelector from '@/components/birthdays/EmailTemplateSelector.vue';
import InputError from '@/components/InputError.vue';
import LocalizedDatePicker from '@/components/LocalizedDatePicker.vue';
import LocalizedTimePicker from '@/components/LocalizedTimePicker.vue';
import {
    messageCategoryOptions,
    messageModeOptions,
    messageToneOptions,
    optionLabel,
    relationshipOptions,
} from '@/lib/birthdayOptions';
import type {
    BirthdayContact,
    BirthdayDefaults,
    BirthdayMessage,
    EmailTemplate,
    MessageMode,
} from '@/types';

const props = defineProps<{
    contact?: BirthdayContact;
    defaults: BirthdayDefaults;
    messages: BirthdayMessage[];
    templates: EmailTemplate[];
}>();

const initialSettings = props.contact?.settings;
const form = useForm({
    first_name: props.contact?.first_name ?? '',
    last_name: props.contact?.last_name ?? '',
    nickname: props.contact?.nickname ?? '',
    birth_date: props.contact?.birth_date ?? '',
    email: props.contact?.email ?? '',
    phone: props.contact?.phone ?? '',
    relationship: props.contact?.relationship ?? '',
    notes: props.contact?.notes ?? '',
    is_active: props.contact?.is_active ?? true,
    reminders: props.contact?.reminders ??
        props.defaults.default_reminder_days ?? [3],
    settings: {
        auto_send_enabled:
            initialSettings?.auto_send_enabled ??
            props.defaults.auto_send_default,
        send_time: (
            initialSettings?.send_time ??
            props.defaults.default_send_time ??
            '09:00'
        ).slice(0, 5),
        channel: 'email' as const,
        message_mode: (initialSettings?.message_mode ??
            props.defaults.default_message_mode ??
            'random') as MessageMode,
        message_category:
            initialSettings?.message_category ??
            props.defaults.default_message_category ??
            '',
        message_tone: initialSettings?.message_tone ?? '',
        selected_message_id: initialSettings?.selected_message_id ?? null,
        custom_message: initialSettings?.custom_message ?? '',
        email_template_id:
            initialSettings?.email_template_id ??
            props.defaults.default_email_template_id ??
            props.templates[0]?.id ??
            null,
        email_subject:
            initialSettings?.email_subject ??
            'Srećan rođendan, {{first_name}}! 🎉',
    },
});

const advancedOpen = ref(Boolean(props.contact?.settings?.auto_send_enabled));
const reminderDraft = ref(1);
const previewMessage = ref<{ id: number; rendered: string } | null>(null);
const previewLoading = ref(false);
const emailPreview = ref(false);
const emailPreviewHtml = ref('');
const selectedBody = computed(
    () =>
        props.messages.find(
            (message) => message.id === form.settings.selected_message_id,
        )?.body ?? '',
);
const previewBody = computed(() =>
    form.settings.message_mode === 'custom'
        ? form.settings.custom_message
        : form.settings.message_mode === 'selected'
          ? selectedBody.value
          : (previewMessage.value?.rendered ??
            'Klikni „Prikaži primer” da vidiš kako poruka izgleda.'),
);

function addReminder(): void {
    if (!form.reminders.includes(Number(reminderDraft.value))) {
        form.reminders.push(Number(reminderDraft.value));
    }

    form.reminders.sort((a, b) => b - a);
}
async function toggleEmailPreview(): Promise<void> {
    if (emailPreview.value) {
        emailPreview.value = false;

        return;
    }

    if (!form.settings.email_template_id) {
        return;
    }

    const response = await fetch('/previews/email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'text/html',
            'X-CSRF-TOKEN':
                document.querySelector<HTMLMetaElement>(
                    'meta[name="csrf-token"]',
                )?.content ?? '',
        },
        body: JSON.stringify({
            first_name: form.first_name || 'Marko',
            last_name: form.last_name,
            nickname: form.nickname,
            message: previewBody.value,
            template_id: form.settings.email_template_id,
        }),
    });

    if (response.ok) {
        emailPreviewHtml.value = await response.text();
        emailPreview.value = true;
    }
}
async function randomPreview(): Promise<void> {
    previewLoading.value = true;

    try {
        const response = await fetch('/previews/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector<HTMLMetaElement>(
                        'meta[name="csrf-token"]',
                    )?.content ?? '',
            },
            body: JSON.stringify({
                first_name: form.first_name || 'Marko',
                last_name: form.last_name,
                nickname: form.nickname,
                category: form.settings.message_category || null,
                tone: form.settings.message_tone || null,
                exclude: previewMessage.value?.id,
            }),
        });

        if (response.ok) {
            previewMessage.value = await response.json();
        }
    } finally {
        previewLoading.value = false;
    }
}
function submit(): void {
    if (props.contact) {
        form.put(`/birthdays/${props.contact.id}`);
    } else {
        form.post('/birthdays');
    }
}
</script>

<template>
    <form class="mx-auto max-w-7xl space-y-6" @submit.prevent="submit">
        <section
            class="celebration-card rounded-2xl border bg-white p-5 shadow-sm sm:p-7 dark:bg-slate-950"
        >
            <div class="mb-6">
                <p
                    class="text-xs font-semibold tracking-[.2em] text-violet-600 uppercase"
                >
                    Osnovni podaci
                </p>
                <h2 class="mt-1 text-xl font-bold">Koga slavimo?</h2>
            </div>
            <div class="grid gap-5 md:grid-cols-2">
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Ime *</span
                    ><input
                        v-model="form.first_name"
                        required
                        class="field"
                        placeholder="Marko" /><InputError
                        :message="form.errors.first_name"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Prezime</span
                    ><input
                        v-model="form.last_name"
                        class="field"
                        placeholder="Marković"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Nadimak</span
                    ><input
                        v-model="form.nickname"
                        class="field"
                        placeholder="Mare"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Datum rođenja *</span
                    ><LocalizedDatePicker
                        v-model="form.birth_date"
                        required
                        placeholder="Izaberi datum rođenja" /><InputError
                        :message="form.errors.birth_date"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Email</span
                    ><input
                        v-model="form.email"
                        type="email"
                        class="field"
                        placeholder="marko@example.com" /><InputError
                        :message="form.errors.email"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Telefon</span
                    ><input
                        v-model="form.phone"
                        class="field"
                        placeholder="+381 64 123 456" /><InputError
                        :message="form.errors.phone"
                /></label>
                <label class="space-y-2"
                    ><span class="text-sm font-medium">Odnos</span
                    ><select v-model="form.relationship" class="field">
                        <option value="">Nije izabrano</option>
                        <option
                            v-for="option in relationshipOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select></label
                >
                <label
                    class="flex items-center gap-3 self-end rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-900"
                    ><input
                        v-model="form.is_active"
                        type="checkbox"
                        class="size-4 accent-violet-600"
                    /><span class="text-sm font-medium"
                        >Aktivan kontakt</span
                    ></label
                >
                <label class="space-y-2 md:col-span-2"
                    ><span class="text-sm font-medium">Beleška</span
                    ><textarea
                        v-model="form.notes"
                        rows="3"
                        class="field h-auto py-3"
                        placeholder="Važne ideje, pokloni, detalji…"
                    ></textarea>
                </label>
            </div>
        </section>

        <section
            class="celebration-card rounded-2xl border bg-white p-5 shadow-sm sm:p-7 dark:bg-slate-950"
        >
            <p
                class="text-xs font-semibold tracking-[.2em] text-violet-600 uppercase"
            >
                Podsetnici
            </p>
            <h2 class="mt-1 text-xl font-bold">Javi mi na vreme</h2>
            <div class="mt-5 flex flex-wrap gap-2">
                <span
                    v-for="days in form.reminders"
                    :key="days"
                    class="inline-flex items-center gap-2 rounded-full bg-violet-50 px-3 py-1.5 text-sm font-medium text-violet-700 dark:bg-violet-950"
                    ><span>{{
                        days === 0 ? 'Na rođendan' : `${days} dana ranije`
                    }}</span
                    ><button
                        type="button"
                        @click="
                            form.reminders = form.reminders.filter(
                                (item) => item !== days,
                            )
                        "
                    >
                        <Trash2 class="size-3.5" /></button
                ></span>
            </div>
            <div class="mt-4 flex max-w-sm gap-2">
                <input
                    v-model.number="reminderDraft"
                    type="number"
                    min="0"
                    max="365"
                    class="field"
                /><button
                    type="button"
                    class="btn-secondary shrink-0"
                    @click="addReminder"
                >
                    <Plus class="size-4" /> Dodaj
                </button>
            </div>
            <InputError :message="form.errors.reminders" />
        </section>

        <section
            class="celebration-card overflow-hidden rounded-2xl border bg-white shadow-sm dark:bg-slate-950"
        >
            <button
                type="button"
                class="flex w-full items-center justify-between p-5 text-left sm:p-7"
                @click="advancedOpen = !advancedOpen"
            >
                <div>
                    <p
                        class="text-xs font-semibold tracking-[.2em] text-violet-600 uppercase"
                    >
                        Automatizacija
                    </p>
                    <h2 class="mt-1 text-xl font-bold">Automatska čestitka</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Poruka, vreme slanja i izgled emaila
                    </p>
                </div>
                <ChevronDown
                    class="size-5 transition"
                    :class="advancedOpen && 'rotate-180'"
                />
            </button>
            <div v-if="advancedOpen" class="space-y-7 border-t p-5 sm:p-7">
                <label
                    class="flex items-center justify-between rounded-2xl bg-gradient-to-r from-violet-50 to-rose-50 p-4 dark:from-violet-950/40 dark:to-rose-950/40"
                    ><div>
                        <p class="font-semibold">
                            Automatski pošalji rođendansku čestitku
                        </p>
                        <p class="text-sm text-slate-500">
                            Email će biti poslat bez ručne akcije
                        </p>
                    </div>
                    <input
                        v-model="form.settings.auto_send_enabled"
                        type="checkbox"
                        class="size-5 accent-violet-600"
                /></label>
                <template v-if="form.settings.auto_send_enabled">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2"
                            ><span class="text-sm font-medium"
                                >Vreme slanja</span
                            ><LocalizedTimePicker
                                v-model="form.settings.send_time"
                                required
                                placeholder="Vreme slanja" /></label
                        ><label class="space-y-2"
                            ><span class="text-sm font-medium">Kanal</span
                            ><select
                                v-model="form.settings.channel"
                                class="field"
                            >
                                <option value="email">Email poruka</option>
                            </select></label
                        >
                    </div>
                    <div>
                        <p class="mb-3 text-sm font-medium">
                            Način izbora poruke
                        </p>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <button
                                v-for="mode in messageModeOptions"
                                :key="mode.value"
                                type="button"
                                @click="form.settings.message_mode = mode.value"
                                class="rounded-xl border px-4 py-3 text-left text-sm font-semibold capitalize"
                                :class="
                                    form.settings.message_mode === mode.value
                                        ? 'border-violet-500 bg-violet-50 text-violet-700 dark:bg-violet-950'
                                        : 'border-slate-200 dark:border-slate-700'
                                "
                            >
                                {{ mode.label }}
                            </button>
                        </div>
                    </div>
                    <div
                        v-if="form.settings.message_mode === 'random'"
                        class="grid gap-4 md:grid-cols-2"
                    >
                        <label class="space-y-2"
                            ><span class="text-sm font-medium">Kategorija</span
                            ><select
                                v-model="form.settings.message_category"
                                class="field"
                            >
                                <option value="">Sve kategorije</option>
                                <option
                                    v-for="category in messageCategoryOptions"
                                    :key="category.value"
                                    :value="category.value"
                                >
                                    {{ category.label }}
                                </option>
                            </select></label
                        ><label class="space-y-2"
                            ><span class="text-sm font-medium">Ton</span
                            ><select
                                v-model="form.settings.message_tone"
                                class="field"
                            >
                                <option value="">Svi tonovi</option>
                                <option
                                    v-for="tone in messageToneOptions"
                                    :key="tone.value"
                                    :value="tone.value"
                                >
                                    {{ tone.label }}
                                </option>
                            </select></label
                        >
                    </div>
                    <label
                        v-if="form.settings.message_mode === 'selected'"
                        class="block space-y-2"
                        ><span class="text-sm font-medium">Izabrana poruka</span
                        ><select
                            v-model="form.settings.selected_message_id"
                            class="field"
                        >
                            <option :value="null">Izaberi poruku</option>
                            <option
                                v-for="message in messages"
                                :key="message.id"
                                :value="message.id"
                            >
                                {{
                                    optionLabel(
                                        messageCategoryOptions,
                                        message.category,
                                    )
                                }}
                                ·
                                {{ message.body.slice(0, 90) }}
                            </option></select
                        ><InputError
                            :message="
                                form.errors['settings.selected_message_id']
                            "
                    /></label>
                    <label
                        v-if="form.settings.message_mode === 'custom'"
                        class="block space-y-2"
                        ><span class="text-sm font-medium">Tvoja poruka</span
                        ><textarea
                            v-model="form.settings.custom_message"
                            rows="4"
                            class="field h-auto py-3"
                            placeholder="Srećan rođendan, {{first_name}}!"
                        ></textarea
                        ><InputError
                            :message="form.errors['settings.custom_message']"
                    /></label>
                    <div
                        class="rounded-2xl border border-violet-100 bg-violet-50/50 p-4 dark:border-violet-900 dark:bg-violet-950/20"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold">
                                Personalizovani pregled
                            </p>
                            <button
                                v-if="form.settings.message_mode === 'random'"
                                type="button"
                                class="btn-secondary"
                                :disabled="previewLoading"
                                @click="randomPreview"
                            >
                                <LoaderCircle
                                    v-if="previewLoading"
                                    class="size-4 animate-spin"
                                /><Sparkles v-else class="size-4" />
                                {{
                                    previewMessage
                                        ? 'Probaj drugu'
                                        : 'Prikaži primer'
                                }}
                            </button>
                        </div>
                        <p
                            class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300"
                        >
                            {{ previewBody }}
                        </p>
                    </div>
                    <div>
                        <p class="mb-3 text-sm font-medium">Email šablon</p>
                        <EmailTemplateSelector
                            v-model="form.settings.email_template_id"
                            :templates="templates"
                        />
                    </div>
                    <label class="block space-y-2"
                        ><span class="text-sm font-medium">Naslov emaila</span
                        ><input
                            v-model="form.settings.email_subject"
                            class="field"
                    /></label>
                    <button
                        type="button"
                        class="btn-secondary"
                        @click="toggleEmailPreview"
                    >
                        <Eye class="size-4" />
                        {{
                            emailPreview
                                ? 'Sakrij pregled'
                                : 'Prikaži izgled emaila'
                        }}
                    </button>
                    <div
                        v-if="emailPreview"
                        class="overflow-hidden rounded-2xl border bg-slate-100 p-3"
                    >
                        <iframe
                            title="Pregled email poruke"
                            class="h-[560px] w-full rounded-xl bg-white"
                            :srcdoc="emailPreviewHtml"
                        />
                    </div>
                </template>
            </div>
        </section>
        <div class="flex items-center justify-end gap-3">
            <Link href="/birthdays" class="btn-secondary">Otkaži</Link
            ><button
                type="submit"
                class="btn-primary"
                :disabled="form.processing"
            >
                <LoaderCircle
                    v-if="form.processing"
                    class="size-4 animate-spin"
                />{{ contact ? 'Sačuvaj izmene' : 'Dodaj rođendan' }}
            </button>
        </div>
    </form>
</template>
