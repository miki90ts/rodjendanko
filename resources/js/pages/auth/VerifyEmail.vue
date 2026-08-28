<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Potvrda email adrese',
        description:
            'Potvrdi email adresu klikom na link koji smo ti upravo poslali.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Potvrda email adrese" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        Novi link za potvrdu je poslat na email adresu unetu pri registraciji.
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            Ponovo pošalji email za potvrdu
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            Odjavi se
        </TextLink>
    </Form>
</template>
