export type MessageMode = 'random' | 'selected' | 'custom';
export type DeliveryStatus = 'pending' | 'processing' | 'sent' | 'failed';

export interface BirthdayAutomation {
    auto_send_enabled: boolean;
    send_time: string | null;
    channel: 'email';
    message_mode: MessageMode;
    message_category: string | null;
    message_tone: string | null;
    selected_message_id: number | null;
    custom_message: string | null;
    email_template_id: number | null;
    email_subject: string | null;
}

export interface BirthdayContact {
    id: number;
    first_name: string;
    last_name: string | null;
    nickname: string | null;
    display_name: string;
    birth_date: string;
    email: string | null;
    phone: string | null;
    relationship: string | null;
    notes: string | null;
    is_active: boolean;
    next_birthday: string;
    days_until: number;
    turning_age: number;
    settings: BirthdayAutomation;
    reminders: number[];
}

export interface BirthdayMessage {
    id: number;
    user_id: number | null;
    name: string | null;
    category: string;
    tone: string;
    body: string;
}

export interface EmailTemplate {
    id: number;
    key: string;
    name: string;
    description: string;
    accent_color: string;
}

export interface BirthdayDefaults {
    timezone: string;
    reminders_enabled: boolean;
    default_reminder_days: number[];
    default_reminder_time: string;
    auto_send_default: boolean;
    default_send_time: string;
    default_channel: 'email';
    default_message_mode: MessageMode;
    default_message_category: string | null;
    default_email_template_id: number | null;
    leap_day_rule: 'february_28' | 'march_1';
}
