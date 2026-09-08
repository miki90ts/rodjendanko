<script setup lang="ts">
import { AlertTriangle, Trash2 } from '@lucide/vue';
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

withDefaults(
    defineProps<{
        title?: string;
        description: string;
        confirmLabel?: string;
        processing?: boolean;
    }>(),
    {
        title: 'Potvrda brisanja',
        confirmLabel: 'Obriši',
        processing: false,
    },
);

const open = defineModel<boolean>({ required: true });
const emit = defineEmits<{ confirm: [] }>();
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden border-rose-100 p-0 sm:max-w-md dark:border-rose-900/50"
        >
            <div
                class="relative bg-gradient-to-br from-rose-50 via-white to-amber-50 px-6 pt-7 pb-6 dark:from-rose-950/40 dark:via-slate-950 dark:to-amber-950/20"
            >
                <div
                    class="mb-5 grid size-14 place-items-center rounded-2xl bg-rose-100 text-rose-600 shadow-sm ring-4 ring-white dark:bg-rose-950 dark:text-rose-300 dark:ring-slate-950"
                >
                    <AlertTriangle class="size-7" />
                </div>
                <DialogHeader class="text-left">
                    <DialogTitle class="text-xl font-black">{{
                        title
                    }}</DialogTitle>
                    <DialogDescription class="mt-2 leading-6">
                        {{ description }}
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
                    variant="destructive"
                    :disabled="processing"
                    class="cursor-pointer gap-2"
                    @click="emit('confirm')"
                >
                    <Trash2 class="size-4" />
                    {{ processing ? 'Brisanje…' : confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
