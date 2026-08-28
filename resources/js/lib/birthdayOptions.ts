import type { MessageMode } from '@/types';

export type SelectOption<T extends string = string> = {
    value: T;
    label: string;
};

export enum RelationshipType {
    Family = 'family',
    Friend = 'friend',
    Partner = 'partner',
    Colleague = 'colleague',
    Business = 'business',
    Other = 'other',
}

export enum MessageCategory {
    Friendly = 'friendly',
    Family = 'family',
    Partner = 'partner',
    Colleague = 'colleague',
    Business = 'business',
    Funny = 'funny',
    Emotional = 'emotional',
    Short = 'short',
    Universal = 'universal',
}

export enum MessageTone {
    Warm = 'warm',
    Funny = 'funny',
    Formal = 'formal',
    Emotional = 'emotional',
    Short = 'short',
    Casual = 'casual',
}

export const relationshipOptions: SelectOption<RelationshipType>[] = [
    { value: RelationshipType.Family, label: 'Porodica' },
    { value: RelationshipType.Friend, label: 'Prijatelj' },
    { value: RelationshipType.Partner, label: 'Partner' },
    { value: RelationshipType.Colleague, label: 'Kolega' },
    { value: RelationshipType.Business, label: 'Poslovni kontakt' },
    { value: RelationshipType.Other, label: 'Ostalo' },
];

export const messageCategoryOptions: SelectOption<MessageCategory>[] = [
    { value: MessageCategory.Friendly, label: 'Prijateljska' },
    { value: MessageCategory.Family, label: 'Porodična' },
    { value: MessageCategory.Partner, label: 'Za partnera' },
    { value: MessageCategory.Colleague, label: 'Za kolegu' },
    { value: MessageCategory.Business, label: 'Poslovna' },
    { value: MessageCategory.Funny, label: 'Smešna' },
    { value: MessageCategory.Emotional, label: 'Emotivna' },
    { value: MessageCategory.Short, label: 'Kratka' },
    { value: MessageCategory.Universal, label: 'Univerzalna' },
];

export const messageToneOptions: SelectOption<MessageTone>[] = [
    { value: MessageTone.Warm, label: 'Topao' },
    { value: MessageTone.Funny, label: 'Duhovit' },
    { value: MessageTone.Formal, label: 'Formalan' },
    { value: MessageTone.Emotional, label: 'Emotivan' },
    { value: MessageTone.Short, label: 'Kratak' },
    { value: MessageTone.Casual, label: 'Opušten' },
];

export const messageModeOptions: SelectOption<MessageMode>[] = [
    { value: 'random', label: 'Nasumična poruka' },
    { value: 'selected', label: 'Izabrana poruka' },
    { value: 'custom', label: 'Lična poruka' },
];

export const optionLabel = (
    options: SelectOption[],
    value: string | null | undefined,
): string => options.find((option) => option.value === value)?.label ?? '—';
