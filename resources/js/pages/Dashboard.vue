<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import WeatherCard from '@/components/weather/WeatherCard.vue';
import WeatherError from '@/components/weather/WeatherError.vue';
import WeatherForecast from '@/components/weather/WeatherForecast.vue';
import WeatherSearch from '@/components/weather/WeatherSearch.vue';
import type { ForecastData, WeatherData } from '@/composables/useWeather';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    weather?: WeatherData | null;
    forecast?: ForecastData | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const weatherOk    = computed(() => props.weather && (props.weather.cod === 200 || props.weather.cod === '200'));
const forecastList = computed(() => props.forecast?.list ?? []);
const lat          = computed(() => (props.weather as any)?.coord?.lat as number | undefined);
const lon          = computed(() => (props.weather as any)?.coord?.lon as number | undefined);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-root">

            <!-- Page heading -->
            <div class="page-heading">
                <div>
                    <h1 class="page-title">Ilmaennustus</h1>
                    <p class="page-sub">Reaalajas ilmaandmed · cache 1h serveris</p>
                </div>
            </div>

            <!-- Search -->
            <WeatherSearch />

            <!-- Weather content -->
            <template v-if="weatherOk && weather">
                <WeatherCard :weather="weather" />
                <WeatherForecast v-if="forecastList.length" :forecast="forecastList" />
                <WeatherRadar :lat="lat" :lon="lon" :city-name="weather.name" />
            </template>

            <WeatherError v-else-if="weather && !weatherOk" :message="weather.message" />

            <!-- Skeleton -->
            <div v-else class="skeleton-grid">
                <div v-for="i in 3" :key="i" class="skeleton" />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-root {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    padding: 1.25rem;
    min-height: 100%;
}

@media (min-width: 768px) {
    .page-root { padding: 1.5rem 2rem; }
}

.page-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.75rem;
    letter-spacing: 0.06em;
    color: rgb(232 230 224);
    margin: 0 0 0.2rem;
}

.page-sub {
    font-size: 0.75rem;
    color: rgb(100 98 92);
    letter-spacing: 0.03em;
    margin: 0;
}

.skeleton-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

@media (max-width: 640px) {
    .skeleton-grid { grid-template-columns: 1fr; }
}

.skeleton {
    aspect-ratio: 16/9;
    border-radius: 12px;
    background: rgb(18 18 21);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.7; }
}
</style>
