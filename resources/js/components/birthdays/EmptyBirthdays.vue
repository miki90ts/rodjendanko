<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CakeSlice, Plus } from '@lucide/vue';
</script>

<template>
    <div
        class="celebration-card rounded-3xl border border-dashed border-violet-200 bg-gradient-to-br from-violet-50/80 to-rose-50/80 px-6 py-14 text-center dark:border-violet-900 dark:from-violet-950/30 dark:to-rose-950/30"
    >
        <div
            class="mx-auto grid size-14 place-items-center rounded-2xl bg-white text-violet-600 shadow-sm dark:bg-slate-900"
        >
            <CakeSlice class="size-7" />
        </div>
        <h3 class="mt-5 text-lg font-semibold">Još nema rođendana</h3>
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            Dodaj nekoga do koga ti je stalo, a mi ćemo se pobrinuti da važan
            datum ne prođe neprimećeno.
        </p>
        <Link
            href="/birthdays/create"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-700"
            ><Plus class="size-4" /> Dodaj rođendan</Link
        >
    </div>
</template>
