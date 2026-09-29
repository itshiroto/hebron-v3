import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { GeneralSettings, ServiceTime } from '@/types/site';
import { storageUrl } from '@/lib/site';

const DEFAULT_ADDRESS =
    'Kompleks Pasar Modern Paramount Blok L39, Jl. Boulevard Raya Gading Serpong, Curug Sangereng, Kelapa Dua, Kab. Tangerang, Banten 15810';

export function useSiteSettings() {
    const page = usePage<{ generalSettings: GeneralSettings | null; services: ServiceTime[] }>();
    const settings = computed<GeneralSettings>(() => page.props.generalSettings ?? {});

    return {
        churchName: computed(() => settings.value.church_name || 'Hebron Family Church'),
        logo: computed(() => storageUrl(settings.value.church_logo) ?? '/hebron.svg'),
        address: computed(() => settings.value.church_address || DEFAULT_ADDRESS),
        socials: computed(() => settings.value.social_media ?? []),
        mapsUrl: computed(() => settings.value.maps_url || 'https://maps.google.com/?q=GPdI+Hebron+Gading+Serpong'),
        livestreamUrl: computed(() => settings.value.livestream_url || null),
        services: computed<ServiceTime[]>(() => page.props.services ?? []),
    };
}
