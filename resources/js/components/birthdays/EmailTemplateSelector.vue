<script setup lang="ts">
import type { EmailTemplate } from '@/types';

defineProps<{ templates: EmailTemplate[] }>();
const model = defineModel<number | null>({ required: true });
</script>

<template>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <button
            v-for="template in templates"
            :key="template.id"
            type="button"
            @click="model = template.id"
            class="celebration-card overflow-hidden rounded-xl border-2 text-left transition"
            :class="
                model === template.id
                    ? 'border-violet-500 ring-2 ring-violet-100'
                    : 'border-slate-200 hover:border-violet-200 dark:border-slate-700'
            "
        >
            <div
                class="h-12"
                :style="{ background: template.accent_color }"
            ></div>
            <div class="p-3">
                <p class="text-sm font-semibold">{{ template.name }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    {{ template.description }}
                </p>
            </div>
        </button>
    </div>
</template>
