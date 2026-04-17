<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface Shark {
    id: number;
    title: string;
    image: string | null;
    description: string | null;
    max_length: number | null;
    habitat: string | null;
    danger_level: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: { id: number; name: string };
}

const API_URL  = 'https://hajusrakendused-main-pm8ido.laravel.cloud/api/sharks';
const API_KEY  = 'VyoBhOoGuiueem1rhoHjFIZUCLi9eLbBh1BdfDN5';
const IMG_BASE = 'https://hajusrakendused-main-pm8ido.laravel.cloud';

const sharks   = ref<Shark[]>([]);
const loading  = ref(true);
const error    = ref<string | null>(null);
const selected = ref<Shark | null>(null);
const search   = ref('');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Haid', href: '/sharks' },
];

async function fetchSharks() {
    loading.value = true;
    error.value   = null;
    try {
        const res  = await fetch(`${API_URL}?api_key=${API_KEY}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        sharks.value = Array.isArray(data) ? data
            : Array.isArray(data.data)   ? data.data
            : Array.isArray(data.sharks) ? data.sharks
            : (Object.values(data).find(v => Array.isArray(v)) as Shark[]) ?? [];
    } catch (e: any) {
        error.value = e.message ?? 'Andmete laadimine ebaõnnestus';
    } finally {
        loading.value = false;
    }
}

function imageUrl(s: Shark): string | null {
    if (!s.image) return null;
    return s.image.startsWith('http') ? s.image : `${IMG_BASE}${s.image}`;
}

function dangerClass(level: string | null): string {
    if (!level) return 'bg-black/70';
    const l = level.toLowerCase();
    if (l.includes('kõrge') || l.includes('high'))   return 'bg-red-600/90';
    if (l.includes('kesk')  || l.includes('medium')) return 'bg-amber-500/90';
    return 'bg-green-600/90';
}

function formatDate(d: string): string {
    return new Date(d).toLocaleDateString('et-EE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

const filtered = computed(() => {
    if (!search.value) return sharks.value;
    const q = search.value.toLowerCase();
    return sharks.value.filter(s =>
        s.title.toLowerCase().includes(q) ||
        (s.habitat      ?? '').toLowerCase().includes(q) ||
        (s.danger_level ?? '').toLowerCase().includes(q) ||
        (s.description  ?? '').toLowerCase().includes(q)
    );
});

onMounted(fetchSharks);
</script>

<template>
    <Head title="Haid" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 md:px-6">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">🦈 Haid</h1>
                    <p class="text-sm text-muted-foreground">
                        <template v-if="!loading">{{ filtered.length }} tulemust</template>
                        <template v-else>Laadin...</template>
                    </p>
                </div>
                <button type="button" @click="fetchSharks"
                    class="rounded-xl border border-border px-3 py-2 text-xs font-medium transition hover:bg-muted">
                    🔄 Uuenda
                </button>
            </div>

            <div class="mb-5">
                <input v-model="search" type="text" placeholder="🔍 Otsi nime, elupaiga järgi..."
                    class="w-full max-w-sm rounded-xl border border-border bg-background px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary/50" />
            </div>

            <div v-if="error"
                class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700
                       dark:border-red-800 dark:bg-red-950 dark:text-red-400">
                ⚠️ {{ error }}
                <button type="button" @click="fetchSharks" class="ml-2 underline">Proovi uuesti</button>
            </div>

            <div v-else-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="i in 6" :key="i"
                    class="h-64 animate-pulse rounded-2xl border border-border bg-muted" />
            </div>

            <div v-else-if="filtered.length === 0"
                class="rounded-2xl border border-border bg-card p-12 text-center text-muted-foreground">
                <p class="text-4xl">🦈</p>
                <p class="mt-3 text-sm">Tulemusi ei leitud.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="shark in filtered" :key="shark.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm
                           cursor-pointer transition hover:shadow-md hover:border-primary/40"
                    @click="selected = shark">

                    <div class="relative h-48 bg-muted">
                        <img v-if="imageUrl(shark)"
                            :src="imageUrl(shark)!"
                            :alt="shark.title"
                            class="h-full w-full object-cover"
                            @error="($event.target as HTMLImageElement).style.display='none'" />
                        <div v-else class="flex h-full items-center justify-center text-5xl">🦈</div>

                        <div v-if="shark.danger_level"
                            :class="['absolute left-2 top-2 rounded-lg px-2 py-1 text-xs font-medium text-white', dangerClass(shark.danger_level)]">
                            ⚠️ {{ shark.danger_level }}
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <h2 class="font-semibold leading-tight">{{ shark.title }}</h2>

                        <div class="mt-2 space-y-1 text-xs text-muted-foreground">
                            <div v-if="shark.max_length">📏 Max pikkus: {{ shark.max_length }} m</div>
                            <div v-if="shark.habitat">🌊 {{ shark.habitat }}</div>
                        </div>

                        <p v-if="shark.description"
                            class="mt-2 flex-1 text-xs text-muted-foreground line-clamp-2">
                            {{ shark.description }}
                        </p>

                        <div class="mt-3 flex items-center justify-between text-xs text-muted-foreground/60">
                            <span v-if="shark.user">👤 {{ shark.user.name }}</span>
                            <span>{{ formatDate(shark.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <Transition name="fade">
            <div v-if="selected" @click="selected = null"
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" />
        </Transition>

        <Transition name="slide">
            <div v-if="selected"
                class="fixed inset-x-4 top-1/2 z-50 -translate-y-1/2 w-full max-w-lg
                       rounded-2xl border border-border bg-background shadow-2xl overflow-hidden
                       sm:left-1/2 sm:-translate-x-1/2">

                <div class="relative h-56 bg-muted">
                    <img v-if="imageUrl(selected)"
                        :src="imageUrl(selected)!"
                        :alt="selected.title"
                        class="h-full w-full object-cover"
                        @error="($event.target as HTMLImageElement).style.display='none'" />
                    <div v-else class="flex h-full items-center justify-center text-6xl">🦈</div>

                    <button type="button" @click="selected = null"
                        class="absolute right-3 top-3 rounded-full bg-black/50 p-1.5 text-white
                               transition hover:bg-black/70">✕</button>

                    <div v-if="selected.danger_level"
                        :class="['absolute left-3 bottom-3 rounded-xl px-3 py-1.5', dangerClass(selected.danger_level)]">
                        <span class="text-sm font-bold text-white">⚠️ {{ selected.danger_level }}</span>
                    </div>
                </div>

                <div class="p-5">
                    <h2 class="text-xl font-bold">{{ selected.title }}</h2>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <div v-if="selected.max_length" class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Max pikkus</p>
                            <p class="font-medium text-sm">{{ selected.max_length }} m</p>
                        </div>
                        <div v-if="selected.habitat" class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Elupaik</p>
                            <p class="font-medium text-sm">{{ selected.habitat }}</p>
                        </div>
                        <div v-if="selected.user" class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Lisaja</p>
                            <p class="font-medium text-sm">{{ selected.user.name }}</p>
                        </div>
                        <div class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Lisatud</p>
                            <p class="font-medium text-sm">{{ formatDate(selected.created_at) }}</p>
                        </div>
                    </div>

                    <p v-if="selected.description"
                        class="mt-4 text-sm text-muted-foreground leading-relaxed">
                        {{ selected.description }}
                    </p>
                </div>
            </div>
        </Transition>

    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform .25s ease, opacity .2s; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translate(-50%, calc(-50% + 16px)); }
</style>