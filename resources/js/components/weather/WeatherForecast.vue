<script setup lang="ts">
import type { ForecastItem } from '@/composables/useWeather';
import { useWeather } from '@/composables/useWeather';
import { computed } from 'vue';

const props = defineProps<{ forecast: ForecastItem[] }>();
const { getCondition, formatDay, groupForecastByDay } = useWeather();
const days = computed(() => groupForecastByDay(props.forecast).slice(0, 5));
</script>

<template>
    <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">5-päeva prognoos</h3>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-5">
            <div v-for="day in days" :key="day.dt"
                class="flex flex-col items-center gap-1 rounded-xl border border-border bg-card p-3 text-center
                       shadow-sm transition hover:shadow-md">
                <p class="text-xs font-medium text-muted-foreground">{{ formatDay(day.dt_txt) }}</p>
                <span class="text-3xl leading-none">{{ getCondition(day.weather[0]?.main ?? '').emoji }}</span>
                <p class="text-sm font-bold">{{ Math.round(day.main.temp) }}°C</p>
                <p class="text-xs text-muted-foreground">{{ day.weather[0]?.description }}</p>
                <div class="mt-1 flex gap-2 text-xs text-muted-foreground">
                    <span>💨 {{ day.wind.speed }}m/s</span>
                    <span>💧 {{ day.main.humidity }}%</span>
                </div>
            </div>
        </div>
    </div>
</template>