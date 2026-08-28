<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import {
    BellRing,
    CakeSlice,
    CalendarHeart,
    Check,
    Eye,
    Gift,
    Heart,
    PartyPopper,
    Send,
    Sparkles,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';
import { store as loginStore } from '@/routes/login';
import { request as resetPassword } from '@/routes/password';
import { store as registerStore } from '@/routes/register';

type AuthTab = 'login' | 'register';

const activeTab = ref<AuthTab>('login');
const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth.user));

const benefits = [
    { icon: BellRing, text: 'Pametni podsetnici baš kada ti odgovara' },
    { icon: Send, text: 'Automatske čestitke koje zvuče lično' },
    { icon: Sparkles, text: 'Stotine veselih poruka na srpskom' },
];

const confetti = [
    ['8%', '12%', '#f97316', '14deg'],
    ['18%', '78%', '#8b5cf6', '-22deg'],
    ['33%', '8%', '#ec4899', '38deg'],
    ['48%', '88%', '#06b6d4', '12deg'],
    ['64%', '5%', '#facc15', '-18deg'],
    ['79%', '91%', '#fb7185', '32deg'],
    ['91%', '18%', '#a78bfa', '-35deg'],
];
</script>

<template>
    <Head title="Rođendanko — važni ljudi, nezaboravljeni datumi" />

    <main
        class="relative min-h-screen overflow-hidden bg-[#fffaf4] text-slate-800 dark:bg-slate-950 dark:text-white"
    >
        <div
            class="absolute -top-32 -left-28 size-[420px] rounded-full bg-rose-200/50 blur-3xl dark:bg-rose-900/20"
        />
        <div
            class="absolute top-1/3 -right-32 size-[460px] rounded-full bg-violet-200/50 blur-3xl dark:bg-violet-900/20"
        />
        <div
            class="absolute bottom-0 left-1/3 size-80 rounded-full bg-amber-100/70 blur-3xl dark:bg-amber-900/10"
        />
        <span
            v-for="([top, left, color, rotate], index) in confetti"
            :key="index"
            class="absolute hidden h-3 w-2 rounded-sm opacity-70 lg:block"
            :style="{
                top,
                left,
                backgroundColor: color,
                transform: `rotate(${rotate})`,
            }"
        />

        <nav
            class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-5 py-6 sm:px-8"
        >
            <Link href="/" class="flex items-center gap-3">
                <span
                    class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-white shadow-lg shadow-violet-200 dark:shadow-none"
                >
                    <AppLogoIcon class="size-6 fill-current" />
                </span>
                <span>
                    <span class="block text-lg font-black tracking-tight"
                        >Rođendanko</span
                    >
                    <span
                        class="block text-[10px] font-semibold tracking-[.2em] text-violet-500 uppercase"
                        >Slavimo važne ljude</span
                    >
                </span>
            </Link>

            <Link
                v-if="isAuthenticated"
                :href="dashboard()"
                class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-violet-200 transition hover:-translate-y-0.5 hover:bg-violet-700 dark:shadow-none"
            >
                Otvori moj pregled <PartyPopper class="size-4" />
            </Link>
            <a
                v-else
                href="#pristup"
                class="rounded-xl border border-violet-200 bg-white/70 px-4 py-2 text-sm font-bold text-violet-700 backdrop-blur hover:bg-white dark:border-violet-800 dark:bg-slate-900/70 dark:text-violet-200"
            >
                Prijavi se
            </a>
        </nav>

        <section
            class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 px-5 pt-6 pb-16 sm:px-8 lg:grid-cols-[1.12fr_.88fr] lg:gap-16 lg:pt-12 lg:pb-24"
        >
            <div class="max-w-2xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white/70 px-4 py-2 text-xs font-bold text-rose-600 shadow-sm backdrop-blur dark:border-rose-900 dark:bg-slate-900/70 dark:text-rose-300"
                >
                    <Sparkles class="size-4" /> Više nijedan važan datum ne
                    prolazi neprimećeno
                </div>
                <h1
                    class="mt-7 text-5xl leading-[1.02] font-black tracking-[-.045em] text-slate-950 sm:text-6xl lg:text-7xl dark:text-white"
                >
                    Vi pamti ljude.<br />
                    <span
                        class="bg-gradient-to-r from-violet-600 via-fuchsia-500 to-rose-500 bg-clip-text text-transparent"
                        >Mi pamtimo datume.</span
                    >
                </h1>
                <p
                    class="mt-6 max-w-xl text-lg leading-8 text-slate-600 dark:text-slate-300"
                >
                    Dodaj ljude koje voliš, podesi podsetnike i prepusti
                    Rođendanku da svaka čestitka stigne na vreme — lična, topla
                    i baš po tvojoj meri.
                </p>

                <div class="mt-8 space-y-3">
                    <div
                        v-for="benefit in benefits"
                        :key="benefit.text"
                        class="flex items-center gap-3 text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-white text-violet-600 shadow-sm ring-1 ring-slate-100 dark:bg-slate-900 dark:ring-slate-800"
                        >
                            <component :is="benefit.icon" class="size-4" />
                        </span>
                        {{ benefit.text }}
                    </div>
                </div>

                <div
                    class="relative mt-10 max-w-lg rounded-[2rem] border border-white/80 bg-white/70 p-4 shadow-xl shadow-rose-100/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-none"
                >
                    <div class="flex items-center gap-4">
                        <span
                            class="grid size-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-amber-300 to-orange-400 text-2xl shadow-md"
                            >🎂</span
                        >
                        <div class="min-w-0 flex-1">
                            <div
                                class="flex items-center justify-between gap-2"
                            >
                                <p class="font-bold">Markov rođendan</p>
                                <span
                                    class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-600"
                                    >za 3 dana</span
                                >
                            </div>
                            <p class="mt-1 text-sm text-slate-500">
                                Čestitka je spremna · šalje se u 09:00
                            </p>
                        </div>
                        <span
                            class="hidden size-10 place-items-center rounded-full bg-emerald-100 text-emerald-600 sm:grid"
                            ><Check class="size-5"
                        /></span>
                    </div>
                </div>
            </div>

            <div
                id="pristup"
                class="relative mx-auto w-full max-w-md scroll-mt-6"
            >
                <div
                    class="absolute -top-7 -right-5 rotate-12 rounded-2xl bg-amber-300 p-3 text-amber-900 shadow-lg"
                >
                    <Gift class="size-6" />
                </div>
                <div
                    class="absolute -bottom-6 -left-5 -rotate-12 rounded-2xl bg-rose-400 p-3 text-white shadow-lg"
                >
                    <Heart class="size-6 fill-current" />
                </div>

                <div
                    class="overflow-hidden rounded-[2rem] border border-white bg-white/90 p-2 shadow-2xl shadow-violet-200/50 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90 dark:shadow-none"
                >
                    <template v-if="isAuthenticated">
                        <div class="p-8 text-center sm:p-10">
                            <div
                                class="mx-auto grid size-16 place-items-center rounded-3xl bg-violet-100 text-violet-600 dark:bg-violet-950"
                            >
                                <CalendarHeart class="size-8" />
                            </div>
                            <h2 class="mt-5 text-2xl font-black">
                                Sve je spremno za slavlje!
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Već si prijavljen/a. Tvoj sledeći važan datum te
                                čeka na pregledu.
                            </p>
                            <Link
                                :href="dashboard()"
                                class="mt-7 inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-fuchsia-500 font-bold text-white shadow-lg shadow-violet-200 dark:shadow-none"
                            >
                                Otvori pregled <CakeSlice class="size-5" />
                            </Link>
                        </div>
                    </template>

                    <template v-else>
                        <div
                            class="grid grid-cols-2 gap-1 rounded-[1.55rem] bg-violet-50 p-1.5 dark:bg-slate-800"
                        >
                            <button
                                type="button"
                                class="rounded-2xl px-4 py-3 text-sm font-bold transition"
                                :class="
                                    activeTab === 'login'
                                        ? 'bg-white text-violet-700 shadow-sm dark:bg-slate-950 dark:text-violet-300'
                                        : 'text-slate-500 hover:text-violet-600'
                                "
                                @click="activeTab = 'login'"
                            >
                                Prijava
                            </button>
                            <button
                                type="button"
                                class="rounded-2xl px-4 py-3 text-sm font-bold transition"
                                :class="
                                    activeTab === 'register'
                                        ? 'bg-white text-violet-700 shadow-sm dark:bg-slate-950 dark:text-violet-300'
                                        : 'text-slate-500 hover:text-violet-600'
                                "
                                @click="activeTab = 'register'"
                            >
                                Novi nalog
                            </button>
                        </div>

                        <div class="p-5 sm:p-7">
                            <div class="mb-6">
                                <p
                                    class="text-xs font-bold tracking-[.18em] text-violet-500 uppercase"
                                >
                                    {{
                                        activeTab === 'login'
                                            ? 'Dobro došao/la nazad'
                                            : 'Počnimo zajedno'
                                    }}
                                </p>
                                <h2 class="mt-1 text-2xl font-black">
                                    {{
                                        activeTab === 'login'
                                            ? 'Prijavi se na nalog'
                                            : 'Napravi svoj nalog'
                                    }}
                                </h2>
                            </div>

                            <Form
                                v-if="activeTab === 'login'"
                                v-bind="loginStore.form()"
                                :reset-on-success="['password']"
                                v-slot="{ errors, processing }"
                                class="space-y-4"
                            >
                                <label class="block space-y-2">
                                    <span class="text-sm font-bold"
                                        >Email adresa</span
                                    >
                                    <input
                                        name="email"
                                        type="email"
                                        required
                                        autocomplete="email"
                                        class="welcome-field"
                                        placeholder="ime@primer.rs"
                                    />
                                    <InputError :message="errors.email" />
                                </label>
                                <label class="block space-y-2">
                                    <span class="text-sm font-bold"
                                        >Lozinka</span
                                    >
                                    <span class="relative block">
                                        <input
                                            name="password"
                                            :type="
                                                showLoginPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            required
                                            autocomplete="current-password"
                                            class="welcome-field pr-11"
                                            placeholder="Tvoja lozinka"
                                        />
                                        <button
                                            type="button"
                                            class="absolute top-1/2 right-3 -translate-y-1/2 text-slate-400 hover:text-violet-600"
                                            @click="
                                                showLoginPassword =
                                                    !showLoginPassword
                                            "
                                        >
                                            <Eye class="size-4" />
                                        </button>
                                    </span>
                                    <InputError :message="errors.password" />
                                </label>
                                <div
                                    class="flex items-center justify-between text-xs"
                                >
                                    <label
                                        class="flex items-center gap-2 font-medium"
                                    >
                                        <input
                                            name="remember"
                                            type="checkbox"
                                            value="1"
                                            class="accent-violet-600"
                                        />
                                        Zapamti me
                                    </label>
                                    <Link
                                        :href="resetPassword()"
                                        class="font-bold text-violet-600 hover:text-violet-700"
                                        >Zaboravljena lozinka?</Link
                                    >
                                </div>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="welcome-submit"
                                >
                                    <span
                                        v-if="processing"
                                        class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                    />
                                    Prijavi se
                                </button>
                            </Form>

                            <Form
                                v-else
                                v-bind="registerStore.form()"
                                :reset-on-success="[
                                    'password',
                                    'password_confirmation',
                                ]"
                                v-slot="{ errors, processing }"
                                class="space-y-4"
                            >
                                <label class="block space-y-2">
                                    <span class="text-sm font-bold"
                                        >Ime i prezime</span
                                    >
                                    <input
                                        name="name"
                                        required
                                        autocomplete="name"
                                        class="welcome-field"
                                        placeholder="Milan Petrović"
                                    />
                                    <InputError :message="errors.name" />
                                </label>
                                <label class="block space-y-2">
                                    <span class="text-sm font-bold"
                                        >Email adresa</span
                                    >
                                    <input
                                        name="email"
                                        type="email"
                                        required
                                        autocomplete="email"
                                        class="welcome-field"
                                        placeholder="ime@primer.rs"
                                    />
                                    <InputError :message="errors.email" />
                                </label>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="block space-y-2">
                                        <span class="text-sm font-bold"
                                            >Lozinka</span
                                        >
                                        <input
                                            name="password"
                                            :type="
                                                showRegisterPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            required
                                            autocomplete="new-password"
                                            class="welcome-field"
                                            placeholder="Najmanje 8 znakova"
                                        />
                                        <InputError
                                            :message="errors.password"
                                        />
                                    </label>
                                    <label class="block space-y-2">
                                        <span class="text-sm font-bold"
                                            >Ponovi lozinku</span
                                        >
                                        <input
                                            name="password_confirmation"
                                            :type="
                                                showRegisterPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            required
                                            autocomplete="new-password"
                                            class="welcome-field"
                                            placeholder="Ponovi lozinku"
                                        />
                                    </label>
                                </div>
                                <label
                                    class="flex items-start gap-2 text-xs leading-5 text-slate-500"
                                >
                                    <input
                                        type="checkbox"
                                        required
                                        class="mt-1 accent-violet-600"
                                    />
                                    Prihvatam da Rođendanko čuva podatke koje
                                    unesem radi podsetnika i slanja čestitki.
                                </label>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="welcome-submit"
                                >
                                    <span
                                        v-if="processing"
                                        class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                    />
                                    Napravi nalog
                                </button>
                            </Form>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <footer
            class="relative z-10 border-t border-violet-100/80 bg-white/40 px-5 py-5 text-center text-xs text-slate-500 backdrop-blur dark:border-slate-800 dark:bg-slate-950/40"
        >
            Napravljeno sa pažnjom za ljude koje ne želimo da zaboravimo. 🎈
        </footer>
    </main>
</template>
