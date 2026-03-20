<script setup lang="ts">
import type { WeatherData } from '@/composables/useWeather';
import { useWeather } from '@/composables/useWeather';
import { computed } from 'vue';

const props = defineProps<{ weather: WeatherData }>();
const { getCondition, windDirection, formatTime } = useWeather();

const condition   = computed(() => getCondition(props.weather.weather[0]?.main ?? ''));
const temp        = computed(() => Math.round(props.weather.main.temp));
const feelsLike   = computed(() => Math.round(props.weather.main.feels_like));
const description = computed(() => {
    const d = props.weather.weather[0]?.description ?? '';
    return d.charAt(0).toUpperCase() + d.slice(1);
});
</script>

<template>
    <div class="weather-card relative overflow-hidden rounded-2xl p-6 text-white shadow-2xl"
         :class="`bg-gradient-to-br ${condition.bg}`">

        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full opacity-10"
             :style="{ background: condition.accent }" />
        <div class="pointer-events-none absolute -bottom-8 -left-8 h-36 w-36 rounded-full opacity-10"
             :style="{ background: condition.accent }" />

        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-widest opacity-70">{{ weather.sys.country }}</p>
                <h2 class="mt-0.5 text-3xl font-bold tracking-tight">{{ weather.name }}</h2>
                <p class="mt-1 text-sm opacity-80">{{ description }}</p>
            </div>
            <span class="text-6xl leading-none" role="img" :aria-label="condition.label">{{ condition.emoji }}</span>
        </div>

        <div class="relative mt-6 flex items-end gap-4">
            <span class="text-7xl font-extrabold leading-none tracking-tight">
                {{ temp }}<sup class="text-3xl">°C</sup>
            </span>
            <div class="mb-2 text-sm opacity-75">
                <p>Tundub nagu {{ feelsLike }}°C</p>
                <p>↑ {{ Math.round(weather.main.temp_max) }}° &nbsp;↓ {{ Math.round(weather.main.temp_min) }}°</p>
            </div>
        </div>

        <div class="relative mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div v-for="(item, i) in [
                { label: 'Niiskus',   value: weather.main.humidity + '%' },
                { label: 'Tuul',      value: weather.wind.speed + ' m/s', sub: windDirection(weather.wind.deg) },
                { label: 'Rõhk',      value: weather.main.pressure + '',  sub: 'hPa' },
                { label: 'Pilvisus',  value: weather.clouds.all + '%' },
            ]" :key="i"
                class="rounded-xl px-3 py-2 text-center backdrop-blur-sm"
                style="background: rgba(255,255,255,.12)">
                <p class="text-xs opacity-70">{{ item.label }}</p>
                <p class="text-lg font-bold">{{ item.value }}</p>
                <p v-if="item.sub" class="text-xs opacity-60">{{ item.sub }}</p>
            </div>
        </div>

        <div class="relative mt-4 flex gap-4 text-xs opacity-70">
            <span>🌅 {{ formatTime(weather.sys.sunrise) }}</span>
            <span>🌇 {{ formatTime(weather.sys.sunset) }}</span>
        </div>

        <p class="relative mt-3 text-xs opacity-50">
            Uuendatud: {{ new Date(weather.dt * 1000).toLocaleString('et-EE') }}
        </p>
    </div>
</template>

<style scoped>
.weather-card { transition: box-shadow .3s ease; }
.weather-card:hover { box-shadow: 0 32px 64px -12px rgba(0,0,0,.4); }
</style>