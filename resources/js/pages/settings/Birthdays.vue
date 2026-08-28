<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import EmailTemplateSelector from '@/components/birthdays/EmailTemplateSelector.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    messageCategoryOptions,
    messageModeOptions,
} from '@/lib/birthdayOptions';
import type { BirthdayDefaults, EmailTemplate } from '@/types';
const props = defineProps<{
    settings: BirthdayDefaults;
    templates: EmailTemplate[];
    timezones: string[];
}>();
const form = useForm({
    ...props.settings,
    default_reminder_time: props.settings.default_reminder_time.slice(0, 5),
    default_send_time: props.settings.default_send_time.slice(0, 5),
});
function submit(): void {
    form.put('/settings/birthdays');
}
</script>
<template>
    <Head title="Podešavanja rođendana" />
    <div class="space-y-8">
        <HeadingSmall
            title="Rođendanska podešavanja"
            description="Podrazumevane vrednosti koje se primenjuju na svaki novi kontakt."
        />
        <form class="space-y-8" @submit.prevent="submit">
            <section class="space-y-4">
                <div>
                    <h3 class="font-semibold">Podsetnici</h3>
                    <p class="text-sm text-slate-500">
                        Kada želiš da te podsetimo emailom.
                    </p>
                </div>
                <label class="flex items-center gap-3"
                    ><input
                        v-model="form.reminders_enabled"
                        type="checkbox"
                        class="accent-violet-600"
                    /><span class="text-sm font-medium"
                        >Podsetnici su uključeni</span
                    ></label
                >
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2"
                        ><span class="text-sm font-medium"
                            >Dani ranije (odvojeni zarezom)</span
                        ><input
                            :value="form.default_reminder_days.join(', ')"
                            class="field"
                            @input="
                                form.default_reminder_days = (
                                    $event.target as HTMLInputElement
                                ).value
                                    .split(',')
                                    .map(Number)
                                    .filter(Number.isFinite)
                            " /></label
                    ><label class="space-y-2"
                        ><span class="text-sm font-medium">Vreme</span
                        ><input
                            v-model="form.default_reminder_time"
                            type="time"
                            class="field"
                    /></label>
                </div>
            </section>
            <section class="space-y-4 border-t pt-8">
                <div>
                    <h3 class="font-semibold">Automatska čestitka</h3>
                    <p class="text-sm text-slate-500">
                        Podrazumevana pravila za nove kontakte.
                    </p>
                </div>
                <label class="flex items-center gap-3"
                    ><input
                        v-model="form.auto_send_default"
                        type="checkbox"
                        class="accent-violet-600"
                    /><span class="text-sm font-medium"
                        >Automatsko slanje uključeno za nove kontakte</span
                    ></label
                >
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-2"
                        ><span class="text-sm font-medium">Vreme slanja</span
                        ><input
                            v-model="form.default_send_time"
                            type="time"
                            class="field" /></label
                    ><label class="space-y-2"
                        ><span class="text-sm font-medium">Način poruke</span
                        ><select
                            v-model="form.default_message_mode"
                            class="field"
                        >
                            <option
                                v-for="mode in messageModeOptions"
                                :key="mode.value"
                                :value="mode.value"
                            >
                                {{ mode.label }}
                            </option>
                        </select></label
                    >
                </div>
                <label
                    v-if="form.default_message_mode === 'random'"
                    class="block space-y-2"
                >
                    <span class="text-sm font-medium"
                        >Podrazumevana kategorija</span
                    >
                    <select
                        v-model="form.default_message_category"
                        class="field"
                    >
                        <option :value="null">Sve kategorije</option>
                        <option
                            v-for="category in messageCategoryOptions"
                            :key="category.value"
                            :value="category.value"
                        >
                            {{ category.label }}
                        </option>
                    </select>
                </label>
                <div>
                    <p class="mb-3 text-sm font-medium">Email šablon</p>
                    <EmailTemplateSelector
                        v-model="form.default_email_template_id"
                        :templates="templates"
                    />
                </div>
            </section>
            <section class="space-y-4 border-t pt-8">
                <div>
                    <h3 class="font-semibold">Vremenska zona i 29. februar</h3>
                    <p class="text-sm text-slate-500">
                        Svi termini se računaju lokalno, a u evidenciji dostave
                        čuvaju u UTC-u.
                    </p>
                </div>
                <label class="block space-y-2"
                    ><span class="text-sm font-medium">Vremenska zona</span
                    ><select v-model="form.timezone" class="field">
                        <option v-for="timezone in timezones" :key="timezone">
                            {{ timezone }}
                        </option>
                    </select></label
                ><label class="block space-y-2"
                    ><span class="text-sm font-medium"
                        >Rođeni 29. februara u neprestupnoj godini</span
                    ><select v-model="form.leap_day_rule" class="field">
                        <option value="february_28">Slavi 28. februara</option>
                        <option value="march_1">Slavi 1. marta</option>
                    </select></label
                >
            </section>
            <button class="btn-primary" :disabled="form.processing">
                Sačuvaj podešavanja
            </button>
        </form>
    </div>
</template>
