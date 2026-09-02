export type InvitationTemplate = 'kids' | 'teen' | 'cheerful' | 'elegant';
export type RsvpStatus = 'pending' | 'attending' | 'not_attending';

export interface InvitationGuest {
    id?: number;
    token?: string;
    name: string | null;
    email: string;
    status?: RsvpStatus;
    party_size?: number;
    message?: string | null;
    responded_at?: string | null;
    sent_at?: string | null;
}

export interface BirthdayInvitation {
    id: number;
    public_token: string;
    title: string;
    honoree_name: string;
    celebrant_age: number | null;
    starts_at: string;
    ends_at: string | null;
    timezone: string;
    venue_name: string;
    address: string;
    city: string;
    latitude: number | null;
    longitude: number | null;
    description: string | null;
    dress_code: string | null;
    gift_note: string | null;
    host_name: string;
    host_phone: string | null;
    reply_deadline: string | null;
    template: InvitationTemplate;
    background_image_path?: string | null;
    background_url: string;
    allow_plus_one: boolean;
    max_guests: number | null;
    is_published?: boolean;
    public_url?: string;
    guests?: InvitationGuest[];
    guests_count?: number;
    attending_count?: number;
    pending_count?: number;
}

export interface InvitationTemplateOption {
    value: InvitationTemplate;
    label: string;
    description: string;
    background_url: string;
}
