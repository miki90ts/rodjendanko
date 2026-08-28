<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Copy, Pencil, Plus, Search, Trash2, X } from '@lucide/vue';
import { reactive, ref, watch } from 'vue';
import PaginationNav from '@/components/PaginationNav.vue';
import {
    messageCategoryOptions,
    messageToneOptions,
    optionLabel,
} from '@/lib/birthdayOptions';
import type { BirthdayMessage, Paginated } from '@/types';
const props = defineProps<{
    messages: Paginated<BirthdayMessage>;
    filters: {
        search?: string;
        category?: string;
        tone?: string;
        source?: string;
    };
}>();
const filters = reactive({
    search: props.filters.search ?? '',
    category: props.filters.category ?? '',
    tone: props.filters.tone ?? '',
    source: props.filters.source ?? '',
});
const showCreate = ref(false);
const editingId = ref<number | null>(null);
const copiedMessageId = ref<number | null>(null);
const copyErrorMessageId = ref<number | null>(null);
const form = useForm({
    name: '',
    category: 'universal',
    tone: 'warm',
    body: 'Srećan rođendan, {{first_name}}! ',
});
let timeout: ReturnType<typeof setTimeout>;
let copyFeedbackTimeout: ReturnType<typeof setTimeout>;
watch(filters, () => {
    clearTimeout(timeout);
    timeout = setTimeout(
        () =>
            router.get('/messages', filters, {
                preserveState: true,
                replace: true,
            }),
        250,
    );
});
function create(): void {
    const options = {
        onSuccess: () => {
            form.reset();
            showCreate.value = false;
            editingId.value = null;
        },
    };

    if (editingId.value) {
        form.put(`/messages/${editingId.value}`, options);
    } else {
        form.post('/messages', options);
    }
}
function edit(message: BirthdayMessage): void {
    editingId.value = message.id;
    form.name = message.name ?? '';
    form.category = message.category;
    form.tone = message.tone;
    form.body = message.body;
    showCreate.value = true;
}
function openCreate(): void {
    editingId.value = null;
    form.reset();
    showCreate.value = true;
}
function remove(message: BirthdayMessage): void {
    if (confirm('Obriši ovu poruku?')) {
        router.delete(`/messages/${message.id}`);
    }
}
function copyWithFallback(text: string): boolean {
    const input = document.createElement('textarea');
    input.value = text;
    input.setAttribute('readonly', '');
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.select();

    try {
        return document.execCommand('copy');
    } finally {
        input.remove();
    }
}

async function copyMessage(message: BirthdayMessage): Promise<void> {
    copiedMessageId.value = null;
    copyErrorMessageId.value = null;
    clearTimeout(copyFeedbackTimeout);

    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(message.body);
        } else if (!copyWithFallback(message.body)) {
            throw new Error('Kopiranje nije dostupno.');
        }

        copiedMessageId.value = message.id;
    } catch {
        if (copyWithFallback(message.body)) {
            copiedMessageId.value = message.id;
        } else {
            copyErrorMessageId.value = message.id;
        }
    }

    copyFeedbackTimeout = setTimeout(() => {
        copiedMessageId.value = null;
        copyErrorMessageId.value = null;
    }, 2200);
}
defineOptions({
    layout: { breadcrumbs: [{ title: 'Poruke', href: '/messages' }] },
});
</script>
<template>
    <Head title="Biblioteka poruka" />
    <main class="min-h-full bg-transparent p-4 sm:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header
                class="page-header-card flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p class="text-sm font-semibold text-violet-600">
                        Inspiracija
                    </p>
                    <h1 class="text-3xl font-bold">Biblioteka poruka</h1>
                    <p class="mt-2 text-slate-500">
                        Sistemske poruke i tvoje lične čestitke.
                    </p>
                </div>
                <button class="btn-primary" @click="openCreate">
                    <Plus class="size-4" /> Nova poruka
                </button>
            </header>
            <div
                class="celebration-card grid gap-3 rounded-2xl border bg-white p-4 shadow-sm md:grid-cols-5 dark:bg-slate-950"
            >
                <label class="relative md:col-span-2"
                    ><Search
                        class="absolute top-3 left-3 size-4 text-slate-400" /><input
                        v-model="filters.search"
                        class="field pl-10"
                        placeholder="Pretraži tekst…" /></label
                ><select v-model="filters.category" class="field">
                    <option value="">Sve kategorije</option>
                    <option
                        v-for="category in messageCategoryOptions"
                        :key="category.value"
                        :value="category.value"
                    >
                        {{ category.label }}
                    </option></select
                ><select v-model="filters.tone" class="field">
                    <option value="">Svi tonovi</option>
                    <option
                        v-for="tone in messageToneOptions"
                        :key="tone.value"
                        :value="tone.value"
                    >
                        {{ tone.label }}
                    </option></select
                ><select v-model="filters.source" class="field">
                    <option value="">Sve poruke</option>
                    <option value="system">Sistemske</option>
                    <option value="custom">Moje poruke</option>
                </select>
            </div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="message in messages.data"
                    :key="message.id"
                    class="celebration-card group flex min-h-56 flex-col rounded-2xl border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-950"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2">
                            <span
                                class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 dark:bg-violet-950"
                                >{{
                                    optionLabel(
                                        messageCategoryOptions,
                                        message.category,
                                    )
                                }}</span
                            ><span
                                class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600 dark:bg-slate-900"
                                >{{
                                    optionLabel(
                                        messageToneOptions,
                                        message.tone,
                                    )
                                }}</span
                            >
                        </div>
                        <span class="text-xs text-slate-400">{{
                            message.user_id ? 'Moja' : 'Sistemska'
                        }}</span>
                    </div>
                    <p
                        class="mt-5 flex-1 text-sm leading-6 text-slate-700 dark:text-slate-300"
                    >
                        {{ message.body }}
                    </p>
                    <div class="mt-5 flex items-center justify-between">
                        <button
                            class="inline-flex cursor-copy items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold text-violet-600 transition hover:bg-violet-50 hover:text-violet-700 dark:hover:bg-violet-950"
                            :title="
                                copiedMessageId === message.id
                                    ? 'Poruka je kopirana'
                                    : 'Kopiraj poruku'
                            "
                            @click="copyMessage(message)"
                        >
                            <Check
                                v-if="copiedMessageId === message.id"
                                class="size-3.5"
                            />
                            <Copy v-else class="size-3.5" />
                            {{
                                copiedMessageId === message.id
                                    ? 'Kopirano'
                                    : copyErrorMessageId === message.id
                                      ? 'Pokušaj ponovo'
                                      : 'Kopiraj'
                            }}
                        </button>
                        <span
                            v-if="copyErrorMessageId === message.id"
                            class="text-xs text-rose-600"
                        >
                            Kopiranje nije uspelo.
                        </span>
                        <button
                            v-if="message.user_id"
                            class="ml-auto cursor-pointer text-slate-400 opacity-0 transition group-hover:opacity-100"
                            @click="edit(message)"
                        >
                            <Pencil class="size-4" />
                        </button>
                        <button
                            v-if="message.user_id"
                            class="cursor-pointer text-rose-500 opacity-0 transition group-hover:opacity-100"
                            @click="remove(message)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </article>
            </div>
            <p
                v-if="!messages.data.length"
                class="celebration-card rounded-2xl border bg-white py-16 text-center text-slate-500"
            >
                Nema poruka za izabrane filtere.
            </p>
            <PaginationNav :paginator="messages" />
        </div>
        <div
            v-if="showCreate"
            class="fixed inset-0 z-50 grid place-items-center bg-slate-950/50 p-4"
            @click.self="showCreate = false"
        >
            <form
                class="celebration-card w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-950"
                @submit.prevent="create"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">
                        {{ editingId ? 'Izmeni poruku' : 'Nova lična poruka' }}
                    </h2>
                    <button type="button" @click="showCreate = false">
                        <X class="size-5" />
                    </button>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2"
                        ><span class="text-sm font-medium">Naziv</span
                        ><input v-model="form.name" class="field" /></label
                    ><label class="space-y-2"
                        ><span class="text-sm font-medium">Kategorija</span
                        ><select v-model="form.category" class="field">
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
                        ><select v-model="form.tone" class="field">
                            <option
                                v-for="tone in messageToneOptions"
                                :key="tone.value"
                                :value="tone.value"
                            >
                                {{ tone.label }}
                            </option>
                        </select></label
                    ><label class="space-y-2 sm:col-span-2"
                        ><span class="text-sm font-medium">Poruka</span
                        ><textarea
                            v-model="form.body"
                            rows="6"
                            class="field h-auto py-3"
                        ></textarea>
                        <p v-pre class="text-xs text-slate-500">
                            Placeholderi: {{ first_name }}, {{ last_name }},
                            {{ nickname }}
                        </p></label
                    >
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button
                        type="button"
                        class="btn-secondary"
                        @click="showCreate = false"
                    >
                        Otkaži</button
                    ><button class="btn-primary" :disabled="form.processing">
                        Sačuvaj poruku
                    </button>
                </div>
            </form>
        </div>
    </main>
</template>
