import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export interface WeatherData {
    name: string;
    sys: { country: string; sunrise: number; sunset: number };
    main: {
        temp: number;
        feels_like: number;
        temp_min: number;
        temp_max: number;
        humidity: number;
        pressure: number;
    };
    weather: { id: number; main: string; description: string; icon: string }[];
    wind: { speed: number; deg: number };
    visibility: number;
    clouds: { all: number };
    dt: number;
    cod: number | string;
    message?: string;
}

export interface ForecastItem {
    dt: number;
    main: { temp: number; temp_min: number; temp_max: number; humidity: number };
    weather: { id: number; main: string; description: string; icon: string }[];
    wind: { speed: number };
    dt_txt: string;
}

export interface ForecastData {
    list: ForecastItem[];
    city: { name: string; country: string };
}

const CACHE_TTL_MS = 10 * 60 * 1000;
interface CacheEntry<T> { data: T; timestamp: number; }
const localCache = new Map<string, CacheEntry<unknown>>();

function getCached<T>(key: string): T | null {
    const entry = localCache.get(key) as CacheEntry<T> | undefined;
    if (!entry) return null;
    if (Date.now() - entry.timestamp > CACHE_TTL_MS) { localCache.delete(key); return null; }
    return entry.data;
}
function setCache<T>(key: string, data: T): void {
    localCache.set(key, { data, timestamp: Date.now() });
}

const CONDITION_MAP: Record<string, { emoji: string; label: string; bg: string; accent: string }> = {
    Thunderstorm: { emoji: '⛈️',  label: 'Äike',       bg: 'from-slate-800 to-slate-900', accent: '#f59e0b' },
    Drizzle:      { emoji: '🌦️',  label: 'Vihmasadu',  bg: 'from-sky-700 to-slate-700',   accent: '#7dd3fc' },
    Rain:         { emoji: '🌧️',  label: 'Vihm',       bg: 'from-blue-800 to-slate-800',  accent: '#60a5fa' },
    Snow:         { emoji: '❄️',  label: 'Lumi',       bg: 'from-slate-400 to-slate-600', accent: '#e2e8f0' },
    Mist:         { emoji: '🌫️',  label: 'Udu',        bg: 'from-gray-500 to-gray-700',   accent: '#d1d5db' },
    Fog:          { emoji: '🌁',  label: 'Tihe udu',   bg: 'from-gray-500 to-gray-700',   accent: '#d1d5db' },
    Haze:         { emoji: '🌁',  label: 'Vine',       bg: 'from-amber-600 to-gray-700',  accent: '#fcd34d' },
    Dust:         { emoji: '🌪️',  label: 'Tolm',       bg: 'from-yellow-700 to-gray-700', accent: '#fbbf24' },
    Tornado:      { emoji: '🌪️',  label: 'Tornado',    bg: 'from-gray-800 to-gray-950',   accent: '#a78bfa' },
    Clear:        { emoji: '☀️',  label: 'Selge',      bg: 'from-amber-400 to-orange-500',accent: '#fef08a' },
    Clouds:       { emoji: '☁️',  label: 'Pilves',     bg: 'from-slate-500 to-slate-700', accent: '#cbd5e1' },
};

export function useWeather() {
    const isSearching = ref(false);
    const searchError = ref<string | null>(null);

    function searchCity(city: string) {
        if (!city.trim()) return;
        const key = `city-${city.trim().toLowerCase()}`;
        const cached = getCached<boolean>(key);
        isSearching.value = true;
        searchError.value = null;

        router.get('/dashboard', { city: city.trim() }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => { setCache(key, true); isSearching.value = false; },
            onError:   () => { searchError.value = 'Linna ei leitud. Proovi uuesti.'; isSearching.value = false; },
        });
        void cached;
    }

    function getCondition(main: string) {
        return CONDITION_MAP[main] ?? { emoji: '🌡️', label: main, bg: 'from-sky-700 to-indigo-800', accent: '#818cf8' };
    }

    function windDirection(deg: number): string {
        const dirs = ['P', 'KP', 'K', 'LK', 'L', 'LL', 'Lä', 'PL'];
        return dirs[Math.round(deg / 45) % 8];
    }

    function formatTime(unix: number): string {
        return new Date(unix * 1000).toLocaleTimeString('et-EE', { hour: '2-digit', minute: '2-digit' });
    }

    function formatDay(dtTxt: string): string {
        return new Date(dtTxt).toLocaleDateString('et-EE', { weekday: 'short', month: 'short', day: 'numeric' });
    }

    function groupForecastByDay(list: ForecastItem[]): ForecastItem[] {
        const seen = new Set<string>();
        return list.filter(item => {
            const day = item.dt_txt.split(' ')[0];
            if (seen.has(day)) return false;
            seen.add(day);
            return true;
        });
    }

    return { isSearching, searchError, searchCity, getCondition, windDirection, formatTime, formatDay, groupForecastByDay };
}