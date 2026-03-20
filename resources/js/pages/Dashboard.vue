<script setup lang="ts">
import WeatherCard from '@/components/weather/WeatherCard.vue';
import WeatherError from '@/components/weather/WeatherError.vue';
import WeatherForecast from '@/components/weather/WeatherForecast.vue';
import WeatherSearch from '@/components/weather/WeatherSearch.vue';
import type { ForecastData, WeatherData } from '@/composables/useWeather';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    weather?: WeatherData | null;
    forecast?: ForecastData | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const weatherOk    = computed(() => props.weather && (props.weather.cod === 200 || props.weather.cod === '200'));
const forecastList = computed(() => props.forecast?.list ?? []);
const lat          = computed(() => (props.weather as any)?.coord?.lat as number | undefined);
const lon          = computed(() => (props.weather as any)?.coord?.lon as number | undefined);
</script>

<template>
    <Head title="Ilm" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">

            <div>
                <h1 class="text-2xl font-bold tracking-tight">Ilmaennustus</h1>
                <p class="text-sm text-muted-foreground">Reaalajas ilmaandmed · vahemälu 1h serveris, 10min brauseris</p>
            </div>

            <WeatherSearch />

            <template v-if="weatherOk && weather">
                <WeatherCard :weather="weather" />
                <WeatherForecast v-if="forecastList.length" :forecast="forecastList" />
            </template>

            <WeatherError v-else-if="weather && !weatherOk" :message="weather.message" />

            <div v-else class="grid gap-4 md:grid-cols-3">
                <div v-for="i in 3" :key="i" class="aspect-video animate-pulse rounded-xl bg-muted" />
            </div>

        </div>
    </AppLayout>
</template>