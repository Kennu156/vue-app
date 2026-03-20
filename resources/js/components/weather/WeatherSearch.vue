<script setup lang="ts">
import { useWeather } from '@/composables/useWeather';
import { ref } from 'vue';

const { isSearching, searchError, searchCity } = useWeather();
const input = ref('');

const quickCities = ['Tallinn', 'Tartu', 'Pärnu', 'Narva', 'Helsinki', 'Stockholm', 'Riga', 'London', 'Berlin', 'Tokyo'];

function onSubmit() {
    if (input.value.trim()) searchCity(input.value.trim());
}
</script>

<template>
    <div class="space-y-3">
        <form @submit.prevent="onSubmit" class="flex gap-2">
            <div class="relative flex-1">
                <input v-model="input" type="text"
                    placeholder="Otsi linna... (nt. Tallinn, London, Tokyo)"
                    class="w-full rounded-xl border border-border bg-background py-2.5 pl-9 pr-4 text-sm shadow-sm
                           transition focus:outline-none focus:ring-2 focus:ring-primary/50"
                    :disabled="isSearching" />
            </div>
            <button type="submit" :disabled="isSearching || !input.trim()"
                class="rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm
                       transition hover:bg-primary/90 disabled:opacity-50">
                <span v-if="isSearching">⏳</span>
                <span v-else>Otsi</span>
            </button>
        </form>

        <p v-if="searchError" class="text-xs text-red-500">{{ searchError }}</p>

        <div class="flex flex-wrap gap-1.5">
            <button v-for="city in quickCities" :key="city"
                @click="input = city; searchCity(city)"
                :disabled="isSearching"
                class="rounded-full border border-border bg-muted px-3 py-1 text-xs font-medium
                       transition hover:bg-accent hover:text-accent-foreground disabled:opacity-50">
                {{ city }}
            </button>
        </div>
    </div>
</template>