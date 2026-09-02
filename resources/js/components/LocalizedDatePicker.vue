<script setup lang="ts">
import { CalendarDays } from '@lucide/vue';
import { computed, useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

withDefaults(
    defineProps<{
        placeholder?: string;
        disabled?: boolean;
        required?: boolean;
        min?: string;
        max?: string;
    }>(),
    {
        placeholder: 'dd.mm.gggg.',
        disabled: false,
        required: false,
        min: undefined,
        max: undefined,
    },
);

const model = defineModel<string>({ default: '' });
const picker = useTemplateRef<HTMLInputElement>('picker');
const formatted = computed(() => {
    const match = model.value?.match(/^(\d{4})-(\d{2})-(\d{2})/);

    return match ? `${match[3]}.${match[2]}.${match[1]}.` : '';
});

const openPicker = (): void => {
    if (!picker.value) return;

    if ('showPicker' in picker.value) picker.value.showPicker();
    else picker.value.click();
};
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="field flex w-full cursor-pointer items-center gap-3 text-left disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="disabled"
            @click="openPicker"
        >
            <CalendarDays class="size-4 shrink-0 text-violet-500" />
            <span :class="formatted ? '' : 'text-slate-400'">
                {{ formatted || placeholder }}
            </span>
        </button>
        <input
            ref="picker"
            v-bind="$attrs"
            v-model="model"
            lang="sr-Latn-RS"
            type="date"
            :disabled="disabled"
            :required="required"
            :min="min"
            :max="max"
            class="pointer-events-none absolute inset-0 size-full opacity-0"
            tabindex="-1"
        />
    </div>
</template>
