<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Car {
    id: number;
    title: string;
    description: string;
    image: string | null;
    year: number;
    model: string;
    engine: string;
    mileage: number;
    price: number;
    color: string | null;
    user_id: number;
    user: { id: number; name: string };
}


const selectedCar = ref<Car | null>(null);

const props = defineProps<{
    cars: Car[];
    apiKey?: string | null;
    apiKeys?: { id: number; name: string; key: string; last_used_at: string | null }[];
}>();

const showApiKeys    = ref(false);
const copiedKey      = ref<number | null>(null);
const showExternalApi  = ref(false);
const extUrl           = ref('');
const extKey           = ref('');
const extItems         = ref<Record<string, any>[]>([]);
const extRaw           = ref<string | null>(null);
const extError         = ref<string | null>(null);
const extLoading       = ref(false);
const extSelectedItem  = ref<Record<string, any> | null>(null);

function extractItems(data: any): Record<string, any>[] {
    if (Array.isArray(data)) return data;
    for (const val of Object.values(data)) {
        if (Array.isArray(val) && val.length > 0 && typeof val[0] === 'object') return val;
    }
    return [];
}

function itemImage(item: Record<string, any>): string | null {
    return item.image ?? item.img ?? item.photo ?? item.thumbnail ?? item.picture ?? item.avatar ?? null;
}

function itemTitle(item: Record<string, any>): string {
    return item.title ?? item.name ?? item.label ?? item.make ?? item.brand ?? Object.values(item)[0] ?? '';
}

function itemSubtitle(item: Record<string, any>): string {
    return item.subtitle ?? item.description ?? item.model ?? item.category ?? item.type ?? '';
}

function itemPrice(item: Record<string, any>): string | null {
    const p = item.price ?? item.cost ?? item.amount ?? item.value;
    return p != null ? String(p) : null;
}

async function fetchExternal() {
    extItems.value  = [];
    extRaw.value    = null;
    extError.value  = null;
    extLoading.value = true;
    try {
        const url = new URL(extUrl.value);
        if (extKey.value) url.searchParams.set('api_key', extKey.value);
        const res  = await fetch(url.toString());
        const text = await res.text();
        try {
            const parsed = JSON.parse(text);
            const items  = extractItems(parsed);
            if (items.length > 0) {
                extItems.value = items;
            } else {
                extRaw.value = JSON.stringify(parsed, null, 2);
            }
        } catch {
            extRaw.value = text;
        }
    } catch (e: any) {
        extError.value = e.message ?? 'Viga päringu tegemisel';
    } finally {
        extLoading.value = false;
    }
}
const apiKeyForm    = useForm({ name: '' });

function submitApiKey() {
    apiKeyForm.post(route('api-keys.store'), {
        preserveScroll: true,
        onSuccess: () => apiKeyForm.reset(),
    });
}

function deleteApiKey(id: number) {
    if (!confirm('Kustuta API võti?')) return;
    router.delete(route('api-keys.destroy', { id }), { preserveScroll: true });
}

function copyKey(key: string, id: number) {
    navigator.clipboard.writeText(key);
    copiedKey.value = id;
    setTimeout(() => { copiedKey.value = null; }, 2000);
}

function openApi() {
    if (!props.apiKey) {
        showApiKeys.value = true;
        return;
    }
    window.open(`/api/volkswagens?api_key=${props.apiKey}`, '_blank');
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Volkswagen', href: '/volkswagens' },
];

const page     = usePage();
const authUser = (page.props.auth as any).user;

const search      = ref('');
const modelFilter = ref('');
const sortBy      = ref('id');
const sortOrder   = ref('desc');

const models = computed(() => [...new Set(props.cars.map(c => c.model))].sort());

const filtered = computed(() => {
    let list = [...props.cars];

    if (search.value)
        list = list.filter(c =>
            c.title.toLowerCase().includes(search.value.toLowerCase()) ||
            c.model.toLowerCase().includes(search.value.toLowerCase()) ||
            c.engine.toLowerCase().includes(search.value.toLowerCase())
        );

    if (modelFilter.value)
        list = list.filter(c => c.model === modelFilter.value);

    list.sort((a, b) => {
        const av = (a as any)[sortBy.value];
        const bv = (b as any)[sortBy.value];
        const cmp = typeof av === 'string' ? av.localeCompare(bv) : av - bv;
        const ordered = sortOrder.value === 'asc' ? cmp : -cmp;
        return ordered !== 0 ? ordered : a.id - b.id;
    });

    return list;
});

const showForm   = ref(false);
const editCar    = ref<Car | null>(null);
const previewUrl = ref<string | null>(null);

const form = useForm({
    title:       '',
    description: '',
    year:        new Date().getFullYear(),
    model:       '',
    engine:      '',
    mileage:     0,
    price:       0,
    color:       '',
    image:       null as File | null,
});

const VW_MODELS = ['Golf','Passat','Polo','Tiguan','Touareg','Arteon','ID.4','ID.3','Caddy','Transporter','Sharan','Touran','Up','Phaeton'];

function openAdd() {
    editCar.value    = null;
    previewUrl.value = null;
    form.reset();
    showForm.value   = true;
}

function openEdit(c: Car) {
    editCar.value        = c;
    form.title           = c.title;
    form.description     = c.description;
    form.year            = c.year;
    form.model           = c.model;
    form.engine          = c.engine;
    form.mileage         = c.mileage;
    form.price           = c.price;
    form.color           = c.color ?? '';
    form.image           = null;
    previewUrl.value     = c.image;
    showForm.value       = true;
}

function onImageChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.image       = file;
    previewUrl.value = URL.createObjectURL(file);
}

function submitForm() {
    const opts = {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => { showForm.value = false; form.reset(); previewUrl.value = null; },
    };
    editCar.value
        ? form.post(route('volkswagens.update', { volkswagen: editCar.value.id }), opts)
        : form.post(route('volkswagens.store'), opts);
}

function deleteCar(c: Car) {
    if (!confirm(`Kustuta "${c.title}"?`)) return;
    selectedCar.value = null;
    router.delete(route('volkswagens.destroy', { volkswagen: c.id }), { preserveScroll: true });
}

function canEdit(c: Car) {
    return authUser.id === c.user_id || authUser.is_admin;
}

function formatPrice(p: number) {
    return new Intl.NumberFormat('et-EE', { style: 'currency', currency: 'EUR' }).format(p);
}

function formatMileage(m: number) {
    return new Intl.NumberFormat('et-EE').format(m) + ' km';
}


</script>

<template>
    <Head title="Volkswagen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 md:px-6">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Volkswagen autod</h1>
                    <p class="text-sm text-muted-foreground">{{ filtered.length }} autot</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="showApiKeys = !showApiKeys"
                        class="rounded-xl border border-border px-3 py-2 text-xs font-medium transition hover:bg-muted">
                        🔑 API võtmed
                    </button>
                    <button type="button" @click="openApi"
                        class="rounded-xl border border-border px-3 py-2 text-xs font-medium transition hover:bg-muted">
                        {{ apiKey ? '📄 Testi API' : '📄 API' }}
                    </button>
                   <!-- <button type="button" @click="showExternalApi = !showExternalApi"
                        class="rounded-xl border border-border px-3 py-2 text-xs font-medium transition hover:bg-muted">
                        🌐 Väline API
                    </button>-->
                    <button type="button" @click="openAdd"
                        class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                               shadow-sm transition hover:bg-primary/90">
                        + Lisa auto
                    </button>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-4">
                <input v-model="search" type="text" placeholder="🔍 Otsi nime, mudeli järgi..."
                    class="rounded-xl border border-border bg-background px-3 py-2 text-sm sm:col-span-2
                           focus:outline-none focus:ring-2 focus:ring-primary/50" />

                <select v-model="modelFilter"
                    class="rounded-xl border border-border bg-background px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Kõik mudelid</option>
                    <option v-for="m in models" :key="m" :value="m">{{ m }}</option>
                </select>

                <select v-model="sortBy"
                    class="rounded-xl border border-border bg-background px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="id">Lisatud</option>
                    <option value="title">Nimi</option>
                    <option value="year">Aasta</option>
                    <option value="price">Hind</option>
                    <option value="mileage">Läbisõit</option>
                </select>
            </div>

            <div class="mb-5 flex gap-2">
                <button v-for="o in ['desc','asc']" :key="o" type="button"
                    @click="sortOrder = o"
                    :class="[
                        'rounded-full px-3 py-1 text-xs font-medium transition',
                        sortOrder === o ? 'bg-primary text-primary-foreground' : 'border border-border hover:bg-muted',
                    ]">
                    {{ o === 'desc' ? '↓ Kahanev' : '↑ Kasvav' }}
                </button>
            </div>

            <Transition name="fade">
                <div v-if="showApiKeys"
                    class="mb-6 rounded-2xl border border-border bg-card p-5 shadow-sm">
            
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-semibold">🔑 API võtmed</h2>
                        <button type="button" @click="showApiKeys = false"
                            class="rounded-lg p-1.5 text-muted-foreground transition hover:bg-muted">✕</button>
                    </div>
                
                    <form @submit.prevent="submitApiKey" class="mb-4 flex gap-3">
                        <input v-model="apiKeyForm.name" type="text" required
                            placeholder="Võtme nimi (nt. Minu rakendus)"
                            class="flex-1 rounded-xl border border-border bg-background px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        <button type="submit" :disabled="apiKeyForm.processing"
                            class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                                   transition hover:bg-primary/90 disabled:opacity-50">
                            + Genereeri
                        </button>
                    </form>
                
                    <div v-if="!apiKeys?.length"
                        class="py-4 text-center text-sm text-muted-foreground">
                        Võtmeid pole. Genereeri esimene!
                    </div>
                
                    <ul v-else class="space-y-2">
                        <li v-for="k in apiKeys" :key="k.id"
                            class="flex items-center gap-3 rounded-xl border border-border bg-background p-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">{{ k.name }}</p>
                                <code class="mt-0.5 block truncate font-mono text-xs text-muted-foreground">
                                    {{ k.key }}
                                </code>
                            </div>
                            <button type="button" @click="copyKey(k.key, k.id)"
                                class="shrink-0 rounded-lg border border-border px-2 py-1 text-xs transition hover:bg-muted">
                                {{ copiedKey === k.id ? '✅' : '📋' }}
                            </button>
                            <button type="button" @click="deleteApiKey(k.id)"
                                class="shrink-0 rounded-lg border border-destructive/40 px-2 py-1 text-xs
                                       text-destructive transition hover:bg-destructive/10">
                                🗑
                            </button>
                        </li>
                    </ul>
                
                    <div v-if="apiKey" class="mt-4 rounded-xl bg-muted p-3">
                        <p class="mb-2 text-xs font-medium">Kiirlingid:</p>
                        <div class="space-y-1 font-mono text-xs text-muted-foreground">
                            <a :href="`/api/volkswagens?api_key=${apiKey}`" target="_blank"
                                class="block truncate hover:text-primary">
                                /api/volkswagens?api_key={{ apiKey }}
                            </a>
                            <a :href="`/api/volkswagens/models?api_key=${apiKey}`" target="_blank"
                                class="block truncate hover:text-primary">
                                /api/volkswagens/models?api_key={{ apiKey }}
                            </a>
                        </div>
                    </div>
                </div>
            </Transition>

            <Transition name="fade">
                <div v-if="showExternalApi"
                    class="mb-6 rounded-2xl border border-border bg-card p-5 shadow-sm">

                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-semibold">🌐 Väline API päring</h2>
                        <button type="button" @click="showExternalApi = false"
                            class="rounded-lg p-1.5 text-muted-foreground transition hover:bg-muted">✕</button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-muted-foreground">API URL</label>
                            <input v-model="extUrl" type="url" placeholder="https://api.example.com/data"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-muted-foreground">API võti (valikuline)</label>
                            <input v-model="extKey" type="text" placeholder="api_key väärtus"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        </div>
                        <button type="button" @click="fetchExternal" :disabled="!extUrl || extLoading"
                            class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                                   transition hover:bg-primary/90 disabled:opacity-50">
                            {{ extLoading ? '⏳ Laadin...' : '▶ Päri' }}
                        </button>
                    </div>

                    <div v-if="extError"
                        class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700
                               dark:border-red-800 dark:bg-red-950 dark:text-red-400">
                        ⚠️ {{ extError }}
                    </div>

                    <!-- Cards view -->
                    <div v-if="extItems.length > 0" class="mt-4">
                        <p class="mb-3 text-xs text-muted-foreground">{{ extItems.length }} tulemust</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="(item, i) in extItems" :key="i"
                                class="flex flex-col overflow-hidden rounded-2xl border border-border bg-background shadow-sm
                                       cursor-pointer transition hover:shadow-md"
                                @click="extSelectedItem = extSelectedItem === item ? null : item">

                                <div class="relative h-40 bg-muted">
                                    <img v-if="itemImage(item)"
                                        :src="itemImage(item)!"
                                        :alt="itemTitle(item)"
                                        class="h-full w-full object-cover"
                                        @error="($event.target as HTMLImageElement).style.display='none'" />
                                    <div v-else class="flex h-full items-center justify-center text-4xl">📦</div>
                                    <div v-if="itemPrice(item)"
                                        class="absolute left-2 top-2 rounded-lg bg-black/70 px-2 py-1 text-xs font-bold text-white">
                                        {{ itemPrice(item) }}
                                    </div>
                                </div>

                                <div class="flex flex-1 flex-col p-3">
                                    <p class="font-semibold text-sm leading-tight line-clamp-2">{{ itemTitle(item) }}</p>
                                    <p v-if="itemSubtitle(item)" class="mt-1 text-xs text-muted-foreground line-clamp-2">{{ itemSubtitle(item) }}</p>

                                    <!-- expanded detail rows -->
                                    <div v-if="extSelectedItem === item" class="mt-3 space-y-1 border-t border-border pt-3">
                                        <div v-for="(val, key) in item" :key="key"
                                            class="flex gap-2 text-xs">
                                            <span class="shrink-0 font-medium text-muted-foreground w-24 truncate">{{ key }}</span>
                                            <span class="break-all">{{ val }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Raw JSON fallback -->
                    <pre v-if="extRaw"
                        class="mt-4 max-h-96 overflow-auto rounded-xl bg-muted p-4 text-xs font-mono whitespace-pre-wrap break-all">{{ extRaw }}</pre>
                </div>
            </Transition>

            <div v-if="filtered.length === 0"
                class="rounded-2xl border border-border bg-card p-12 text-center text-muted-foreground">
                <p class="mt-3 text-sm">Autosid ei leitud.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="car in filtered" :key="car.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm
                           transition hover:shadow-md cursor-pointer"
                    @click="selectedCar = car">

                    <div class="relative h-48 bg-muted">
                        <img v-if="car.image"
                            :src="car.image?.startsWith('http') ? car.image : `/storage/${car.image}`"
                            :alt="car.title"
                            class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-5xl">🚗</div>

                        <div class="absolute left-2 top-2 rounded-lg bg-black/70 px-2 py-1 text-xs font-bold text-white">
                            {{ formatPrice(car.price) }}
                        </div>

                        <div v-if="car.color"
                            class="absolute right-2 top-2 rounded-lg bg-black/70 px-2 py-1 text-xs text-white">
                            {{ car.color }}
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-semibold leading-tight">{{ car.title }}</h2>
                            <span class="shrink-0 rounded-full bg-muted px-2 py-0.5 text-xs font-medium">
                                {{ car.year }}
                            </span>
                        </div>

                        <div class="mt-2 grid grid-cols-2 gap-1 text-xs text-muted-foreground">
                            <span>🔧 {{ car.engine }}</span>
                            <span>📍 {{ formatMileage(car.mileage) }}</span>
                            <span>🏷 {{ car.model }}</span>
                        </div>

                        <p class="mt-2 flex-1 text-xs text-muted-foreground line-clamp-2">
                            {{ car.description }}
                        </p>

                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-xs text-muted-foreground/60">👤{{ car.user.name }}</p>
                            <div v-if="canEdit(car)" class="flex gap-1">
                                <button type="button" @click.stop="openEdit(car)"
                                    class="rounded-lg border border-border px-2 py-1 text-xs transition hover:bg-muted">
                                    ✏️
                                </button>
                                <button type="button" @click.stop="deleteCar(car)"
                                    class="rounded-lg border border-destructive/40 px-2 py-1 text-xs
                                           text-destructive transition hover:bg-destructive/10">
                                    🗑
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 rounded-2xl border border-border bg-card p-5 shadow-sm">
                <h2 class="mb-4 font-semibold text-lg">📄 API dokumentatsioon</h2>

                <div v-if="!apiKey"
                    class="mb-4 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50
                           px-4 py-3 text-sm dark:border-amber-800 dark:bg-amber-950">
                    <span>⚠️</span>
                    <span>API kasutamiseks on vaja võtit. </span>
                </div>
            
                <div v-else class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3
                                   dark:border-green-800 dark:bg-green-950">
                    <p class="text-xs font-medium text-green-700 dark:text-green-400">✅ Sinu API võti:</p>
                    <code class="mt-1 block text-xs font-mono text-green-600 dark:text-green-500 break-all">
                        {{ apiKey }}
                    </code>
                </div>
            
                <div class="space-y-4">
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Kõik autod</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Otsimine</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&search=golf
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Filtreeri mudeli järgi</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&model=Golf
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Filtreeri aasta järgi</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&year=2020
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Sorteerimine</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&sort=price&order=asc
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">
                            sort valikud: <code>title</code> · <code>year</code> · <code>price</code> · <code>mileage</code> · <code>created_at</code>
                        </p>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Piira tulemusi</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&limit=5
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Vaikimisi: 20 · Maksimum: 100</p>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Üksik auto</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens/1?api_key={{ apiKey ?? 'SINU_VÕTI' }}
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Kõik mudelid</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens/models?api_key={{ apiKey ?? 'SINU_VÕTI' }}
                        </div>
                    </div>
                
                    <div>
                        <p class="mb-2 text-sm font-semibold">Kõik filtrid korraga</p>
                        <div class="rounded-lg bg-muted px-3 py-2 font-mono text-xs break-all">
                            /api/volkswagens?api_key={{ apiKey ?? 'SINU_VÕTI' }}&model=Golf&sort=price&order=asc&limit=5
                        </div>
                    </div>
                </div>
            </div>

        <Transition name="fade">
            <div v-if="showForm" @click="showForm = false"
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" />
        </Transition>

        <Transition name="slide">
            <div v-if="showForm"
                class="fixed inset-x-4 top-1/2 z-50 max-h-[90vh] w-full max-w-lg -translate-y-1/2
                       overflow-y-auto rounded-2xl border border-border bg-background p-6 shadow-2xl
                       sm:left-1/2 sm:-translate-x-1/2">

                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold">{{ editCar ? 'Muuda autot' : 'Lisa uus auto' }}</h2>
                    <button type="button" @click="showForm = false"
                        class="rounded-lg p-1.5 text-muted-foreground transition hover:bg-muted">✕</button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-3">

                    <div>
                        <label class="mb-1 block text-sm font-medium">Pealkiri * <span class="text-muted-foreground text-xs">(nt. VW Golf MK7 2.0 TDI)</span></label>
                        <input v-model="form.title" type="text" required
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Mudel *</label>
                            <select v-model="form.model" required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50">
                                <option value="">Vali mudel</option>
                                <option v-for="m in VW_MODELS" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <p v-if="form.errors.model" class="mt-1 text-xs text-red-500">{{ form.errors.model }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Aasta *</label>
                            <input v-model="form.year" type="number" min="1938" max="2099" required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.year" class="mt-1 text-xs text-red-500">{{ form.errors.year }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Mootor *</label>
                            <input v-model="form.engine" type="text" required placeholder="nt. 2.0 TDI 150hp"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.engine" class="mt-1 text-xs text-red-500">{{ form.errors.engine }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Värv</label>
                            <input v-model="form.color" type="text" placeholder="nt. Valge"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Läbisõit (km) *</label>
                            <input v-model="form.mileage" type="number" min="0" required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.mileage" class="mt-1 text-xs text-red-500">{{ form.errors.mileage }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Hind (€) *</label>
                            <input v-model="form.price" type="number" min="0" step="0.01" required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.price" class="mt-1 text-xs text-red-500">{{ form.errors.price }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Kirjeldus *</label>
                        <textarea v-model="form.description" rows="3" required
                            placeholder="Kirjelda autot..."
                            class="w-full resize-none rounded-xl border border-border bg-background px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Pilt</label>
                        <input type="file" accept="image/*" @change="onImageChange"
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm" />
                        <img v-if="previewUrl" :src="previewUrl" alt="Eelvaade"
                            class="mt-2 h-32 w-full rounded-xl object-cover" />
                        <p v-if="form.errors.image" class="mt-1 text-xs text-red-500">{{ form.errors.image }}</p>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 rounded-xl bg-primary py-2.5 text-sm font-medium text-primary-foreground
                                   transition hover:bg-primary/90 disabled:opacity-50">
                            {{ form.processing ? '⏳ Salvestan...' : (editCar ? 'Salvesta' : 'Lisa auto') }}
                        </button>
                    </div>
                </form>
            </div>
        </Transition>
    </div>
        <Transition name="fade">
            <div v-if="selectedCar && !showForm"
                @click="selectedCar = null"
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" />
        </Transition>

        <Transition name="slide">
            <div v-if="selectedCar && !showForm"
                class="fixed inset-x-4 top-1/2 z-50 -translate-y-1/2 w-full max-w-lg
                       rounded-2xl border border-border bg-background shadow-2xl overflow-hidden
                       sm:left-1/2 sm:-translate-x-1/2">
        
                <div class="relative h-56 bg-muted">
                    <img v-if="selectedCar.image"
                        :src="selectedCar.image?.startsWith('http') ? selectedCar.image : `/storage/${selectedCar.image}`"
                        :alt="selectedCar.title"
                        class="h-full w-full object-cover" />
                    <div v-else class="flex h-full items-center justify-center text-5xl">🚗</div>
                    <button type="button" @click="selectedCar = null"
                        class="absolute right-3 top-3 rounded-full bg-black/50 p-1.5 text-white
                               transition hover:bg-black/70">✕</button>
                
                    <div class="absolute left-3 bottom-3 rounded-xl bg-black/70 px-3 py-1.5">
                        <span class="text-lg font-bold text-white">{{ formatPrice(selectedCar.price) }}</span>
                    </div>
                
                    <div v-if="selectedCar.color"
                        class="absolute right-3 bottom-3 rounded-lg bg-black/70 px-2 py-1 text-xs text-white">
                        {{ selectedCar.color }}
                    </div>
                </div>
            
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-xl font-bold">{{ selectedCar.title }}</h2>
                        <span class="shrink-0 rounded-full bg-muted px-3 py-1 text-sm font-medium">
                            {{ selectedCar.year }}
                        </span>
                    </div>
                
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Mudel</p>
                            <p class="font-medium text-sm">{{ selectedCar.model }}</p>
                        </div>
                        <div class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Mootor</p>
                            <p class="font-medium text-sm">{{ selectedCar.engine }}</p>
                        </div>
                        <div class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Läbisõit</p>
                            <p class="font-medium text-sm">{{ formatMileage(selectedCar.mileage) }}</p>
                        </div>
                        <div class="rounded-xl bg-muted px-3 py-2">
                            <p class="text-xs text-muted-foreground">Lisaja</p>
                            <p class="font-medium text-sm truncate">{{ selectedCar.user.name }}</p>
                        </div>
                    </div>
                
                    <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                        {{ selectedCar.description }}
                    </p>
                
                    <div v-if="canEdit(selectedCar)" class="mt-4 flex gap-2">
                        <button type="button"
                            @click="openEdit(selectedCar); selectedCar = null"
                            class="flex-1 rounded-xl border border-border py-2.5 text-sm font-medium
                                   transition hover:bg-muted">
                            ✏️ Muuda
                        </button>
                        <button type="button"
                            @click="deleteCar(selectedCar)"
                            class="rounded-xl border border-destructive/40 px-4 py-2.5 text-sm font-medium
                                   text-destructive transition hover:bg-destructive/10">
                            🗑 Kustuta
                        </button>
                    </div>
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