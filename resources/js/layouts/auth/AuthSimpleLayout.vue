<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-gradient-to-br from-rose-50 via-white to-violet-100 p-6 md:p-10 dark:from-slate-950 dark:via-slate-950 dark:to-violet-950"
    >
        <div
            class="absolute -top-20 -left-20 size-72 rounded-full bg-rose-200/50 blur-3xl dark:bg-rose-900/20"
        />
        <div
            class="absolute -right-20 -bottom-20 size-80 rounded-full bg-violet-200/60 blur-3xl dark:bg-violet-900/20"
        />
        <div
            class="relative w-full max-w-md rounded-[2rem] border border-white/80 bg-white/85 p-7 shadow-2xl shadow-violet-200/40 backdrop-blur sm:p-9 dark:border-slate-800 dark:bg-slate-900/85 dark:shadow-none"
        >
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-white shadow-lg"
                        >
                            <AppLogoIcon class="size-7 fill-current" />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-black">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
