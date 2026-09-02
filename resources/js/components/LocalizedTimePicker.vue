<script setup lang="ts">
import { Clock3 } from '@lucide/vue';
import { computed, useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

withDefaults(
    defineProps<{
        placeholder?: string;
        disabled?: boolean;
        required?: boolean;
        step?: number;
    }>(),
    {
        placeholder: 'hh:mm',
        disabled: false,
        required: false,
        step: 300,
    },
);

const model = defineModel<string>({ default: '' });
const picker = useTemplateRef<HTMLInputElement>('picker');
const formatted = computed(() => model.value?.slice(0, 5) ?? '');

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
            <Clock3 class="size-4 shrink-0 text-violet-500" />
            <span :class="formatted ? '' : 'text-slate-400'">
                {{ formatted || placeholder }}
            </span>
        </button>
        <input
            ref="picker"
            v-bind="$attrs"
            v-model="model"
            lang="sr-Latn-RS"
            type="time"
            :disabled="disabled"
            :required="required"
            :step="step"
            class="pointer-events-none absolute inset-0 size-full opacity-0"
            tabindex="-1"
        />
    </div>
</template>
