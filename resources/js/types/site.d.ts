export interface GeneralSettings {
    church_name?: string;
    church_subtitle?: string;
    church_logo?: string;
    church_address?: string;
    social_media?: { platform: string; url: string }[];
    maps_url?: string;
    livestream_url?: string;
}

export interface ServiceTime {
    title: string;
    time: string;
    livestream: boolean;
}

export interface DivisionItem {
    id: number;
    title: string;
    slug: string;
    audience: string | null;
    description: string;
    image: string | null;
    time: string | null;
    short_time: string | null;
    link: string | null;
    sort: number;
}

export type DevotionCategory = 'devotion' | 'news' | 'announcement';

export interface DevotionSummary {
    id: number;
    title: string;
    slug: string;
    category: DevotionCategory;
    image: string | null;
    snippet?: string;
    reading_minutes: number;
    created_at: string;
}

export interface DevotionFull extends DevotionSummary {
    content: string;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}
