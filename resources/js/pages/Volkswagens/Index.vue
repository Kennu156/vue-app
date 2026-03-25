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

const props = defineProps<{ cars: Car[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Volkswagen', href: '/volkswagens' },
];

const page     = usePage();
const authUser = (page.props.auth as any).user;

const search      = ref('');
const modelFilter = ref('');
const sortBy      = ref('created_at');
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
        return sortOrder.value === 'asc' ? cmp : -cmp;
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
        ? form.post(route('volkswagens.update', editCar.value.id), opts)
        : form.post(route('volkswagens.store'), opts);
}

function deleteCar(c: Car) {
    if (!confirm(`Kustuta "${c.title}"?`)) return;
    router.delete(route('volkswagens.destroy', c.id), { preserveScroll: true });
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
                    <a href="/api/volkswagens" target="_blank"
                        class="rounded-xl border border-border px-3 py-2 text-xs font-medium transition hover:bg-muted">
                        API
                    </a>
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
                    <option value="created_at">Lisatud</option>
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

            <div v-if="filtered.length === 0"
                class="rounded-2xl border border-border bg-card p-12 text-center text-muted-foreground">
                <p class="mt-3 text-sm">Autosid ei leitud.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="car in filtered" :key="car.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm
                           transition hover:shadow-md">

                    <div class="relative h-48 bg-muted">
                        <img v-if="car.image" :src="car.image" :alt="car.title"
                            class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-5xl">🚗</div>

                        <div class="absolute left-2 top-2 rounded-lg bg-black/70 px-2 py-1 text-xs font-bold text-white">
                            {{ formatPrice(car.price) }}
                        </div>

                        <div v-if="car.color"
                            class="absolute right-2 top-2 rounded-lg bg-black/70 px-2 py-1 text-xs text-white">
                            🎨 {{ car.color }}
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
                            <p class="text-xs text-muted-foreground/60">✍️ {{ car.user.name }}</p>
                            <div v-if="canEdit(car)" class="flex gap-1">
                                <button type="button" @click="openEdit(car)"
                                    class="rounded-lg border border-border px-2 py-1 text-xs transition hover:bg-muted">
                                    ✏️
                                </button>
                                <button type="button" @click="deleteCar(car)"
                                    class="rounded-lg border border-destructive/40 px-2 py-1 text-xs
                                           text-destructive transition hover:bg-destructive/10">
                                    🗑
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 rounded-2xl border border-border bg-card p-5">
                <h2 class="mb-3 font-semibold">📄 API dokumentatsioon</h2>
                <div class="space-y-1.5 font-mono text-xs text-muted-foreground">
                    <p><span class="text-green-500">GET</span> /api/volkswagens</p>
                    <p><span class="text-green-500">GET</span> /api/volkswagens?search=golf</p>
                    <p><span class="text-green-500">GET</span> /api/volkswagens?model=Golf&sort=price&order=asc</p>
                    <p><span class="text-green-500">GET</span> /api/volkswagens?year=2020&limit=5</p>
                    <p><span class="text-green-500">GET</span> /api/volkswagens/models</p>
                    <p><span class="text-green-500">GET</span> /api/volkswagens/{id}</p>
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
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform .25s ease, opacity .2s; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translate(-50%, calc(-50% + 16px)); }
</style>