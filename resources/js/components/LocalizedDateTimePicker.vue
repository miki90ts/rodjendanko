<script setup lang="ts">
import { computed } from 'vue';
import LocalizedDatePicker from '@/components/LocalizedDatePicker.vue';
import LocalizedTimePicker from '@/components/LocalizedTimePicker.vue';

const props = withDefaults(
    defineProps<{
        required?: boolean;
        datePlaceholder?: string;
        timePlaceholder?: string;
    }>(),
    {
        required: false,
        datePlaceholder: 'Izaberi datum',
        timePlaceholder: 'Izaberi vreme',
    },
);

const model = defineModel<string>({ default: '' });
const date = computed({
    get: () => model.value?.slice(0, 10) ?? '',
    set: (value: string) => {
        model.value = value ? `${value}T${time.value || '12:00'}` : '';
    },
});
const time = computed({
    get: () => model.value?.slice(11, 16) ?? '',
    set: (value: string) => {
        if (date.value) model.value = `${date.value}T${value}`;
    },
});
</script>

<template>
    <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_9rem]">
        <LocalizedDatePicker
            v-model="date"
            :required="required"
            :placeholder="props.datePlaceholder"
        />
        <LocalizedTimePicker
            v-model="time"
            :required="required"
            :placeholder="props.timePlaceholder"
        />
    </div>
</template>
