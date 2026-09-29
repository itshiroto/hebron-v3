import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

export const NAV_ITEMS = [
    { name: 'Tentang Kami', href: '/about' },
    { name: 'Divisi', href: '/division' },
    { name: 'Pelayanan', href: '/ministry' },
    { name: 'Renungan', href: '/devotion' },
    { name: 'Kontak', href: '/contact' },
] as const;

export const CATEGORY_LABELS: Record<string, string> = {
    devotion: 'Renungan',
    news: 'Berita',
    announcement: 'Pengumuman',
};

export const MOTTO = 'United as One Family to Live like Christ';

export function storageUrl(path?: string | null): string | null {
    if (!path) return null;
    if (/^(https?:)?\/\//.test(path) || path.startsWith('/')) return path;
    return `/storage/${path}`;
}

export function formatDate(iso: string): string {
    return format(new Date(iso), 'd MMMM yyyy', { locale: localeId });
}

export function isActive(currentUrl: string, href: string): boolean {
    return currentUrl === href || currentUrl.startsWith(`${href}/`) || currentUrl.startsWith(`${href}?`);
}

export function readingLabel(minutes: number): string {
    return `${minutes} menit baca`;
}
