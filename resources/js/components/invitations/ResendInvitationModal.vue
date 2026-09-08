<script setup lang="ts">
import { MailCheck, Send } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

defineProps<{
    previousAttempts: number;
    guestCount: number;
    processing?: boolean;
}>();

const open = defineModel<boolean>({ required: true });
const emit = defineEmits<{ confirm: [] }>();
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden border-violet-100 p-0 sm:max-w-md dark:border-violet-900/50"
        >
            <div
                class="bg-gradient-to-br from-violet-50 via-white to-rose-50 px-6 pt-7 pb-6 dark:from-violet-950/40 dark:via-slate-950 dark:to-rose-950/20"
            >
                <div
                    class="mb-5 grid size-14 place-items-center rounded-2xl bg-violet-100 text-violet-600 shadow-sm ring-4 ring-white dark:bg-violet-950 dark:text-violet-300 dark:ring-slate-950"
                >
                    <MailCheck class="size-7" />
                </div>
                <DialogHeader class="text-left">
                    <DialogTitle class="text-xl font-black">
                        Ponovo poslati pozivnicu?
                    </DialogTitle>
                    <DialogDescription class="mt-2 leading-6">
                        Pozivnica je već slata {{ previousAttempts }}
                        {{ previousAttempts === 1 ? 'put' : 'puta' }}. Novi
                        email biće poslat na {{ guestCount }} adresa i računaće
                        se kao slanje {{ previousAttempts + 1 }} od maksimalno
                        3.
                    </DialogDescription>
                </DialogHeader>
            </div>
            <DialogFooter class="gap-2 border-t bg-white p-5 dark:bg-slate-950">
                <DialogClose as-child>
                    <Button
                        type="button"
                        variant="secondary"
                        :disabled="processing"
                    >
                        Odustani
                    </Button>
                </DialogClose>
                <Button
                    type="button"
                    :disabled="processing"
                    class="cursor-pointer gap-2 bg-violet-600 text-white hover:bg-violet-700"
                    @click="emit('confirm')"
                >
                    <Send class="size-4" />
                    {{ processing ? 'Pokretanje…' : 'Da, pošalji ponovo' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
