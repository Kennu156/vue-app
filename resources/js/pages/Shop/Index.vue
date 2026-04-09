<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string;
    category: string;
}

const selectedProduct = ref<Product | null>(null);

const props = defineProps<{ products: Product[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pood', href: '/shop' },
];

const quantities = ref<Record<number, number>>({});
const added      = ref<Record<number, boolean>>({});

function getQty(id: number) {
    return quantities.value[id] ?? 1;
}

function setQty(id: number, val: number) {
    quantities.value[id] = Math.max(1, Math.min(99, val));
}

const form = useForm({ product_id: 0, quantity: 1 });

function addToCart(product: Product) {
    form.product_id = product.id;
    form.quantity   = getQty(product.id);
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            added.value[product.id] = true;
            setTimeout(() => { added.value[product.id] = false; }, 2000);
        },
    });
}

const categories = ['kõik', ...new Set(props.products.map(p => p.category))];
const activeCategory = ref('kõik');

const filtered = () => activeCategory.value === 'kõik'
    ? props.products
    : props.products.filter(p => p.category === activeCategory.value);
</script>

<template>
    <Head title="Pood" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 md:px-6">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Pood</h1>
                    <p class="text-sm text-muted-foreground">{{ products.length }} toodet</p>
                </div>
                <a :href="route('cart')"
                    class="rounded-xl border border-border bg-card px-4 py-2 text-sm font-medium
                           shadow-sm transition hover:bg-muted">
                    🛒 Ostukorv
                </a>
            </div>

            <div class="mb-6 flex flex-wrap gap-2">
                <button v-for="cat in categories" :key="cat"
                    type="button"
                    @click="activeCategory = cat"
                    :class="[
                        'rounded-full px-4 py-1.5 text-sm font-medium transition',
                        activeCategory === cat
                            ? 'bg-primary text-primary-foreground'
                            : 'border border-border bg-card hover:bg-muted',
                    ]">
                    {{ cat }}
                </button>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="product in filtered()" :key="product.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm
                           transition hover:shadow-md cursor-pointer"
                    @click="selectedProduct = product">

                    <img :src="product.image" :alt="product.name"
                        class="h-48 w-full object-cover" />

                    <div class="flex flex-1 flex-col p-4">
                        <span class="mb-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            {{ product.category }}
                        </span>
                        <h2 class="text-base font-semibold">{{ product.name }}</h2>
                        <p class="mt-1 flex-1 text-sm text-muted-foreground line-clamp-2">
                            {{ product.description }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold">{{ product.price.toFixed(2) }} €</span>

                            <div class="flex items-center gap-1">
                                <button type="button"
                                    @click.stop="setQty(product.id, getQty(product.id) - 1)"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-border
                                           text-sm transition hover:bg-muted">−</button>
                                <span class="w-6 text-center text-sm font-medium">{{ getQty(product.id) }}</span>
                                <button type="button"
                                    @click.stop="setQty(product.id, getQty(product.id) + 1)"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-border
                                           text-sm transition hover:bg-muted">+</button>
                            </div>
                        </div>

                        <button type="button"
                            @click.stop="addToCart(product)"
                            :disabled="form.processing"
                            :class="[
                                'mt-3 w-full rounded-xl py-2 text-sm font-medium transition',
                                added[product.id]
                                    ? 'bg-green-500 text-white'
                                    : 'bg-primary text-primary-foreground hover:bg-primary/90',
                            ]">
                            {{ added[product.id] ? '✓ Lisatud!' : '🛒 Lisa korvi' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <Transition name="fade">
        <div v-if="selectedProduct"
            @click="selectedProduct = null"
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" />
    </Transition>
    
    <Transition name="slide">
        <div v-if="selectedProduct"
            class="fixed inset-x-4 top-1/2 z-50 -translate-y-1/2 w-full max-w-lg
                   rounded-2xl border border-border bg-background shadow-2xl overflow-hidden
                   sm:left-1/2 sm:-translate-x-1/2">
    
            <div class="relative h-56 bg-muted">
                <img v-if="selectedProduct.image"
                    :src="selectedProduct.image"
                    :alt="selectedProduct.name"
                    class="h-full w-full object-cover" />
                <div v-else class="flex h-full items-center justify-center text-5xl">🛍️</div>
                <button type="button" @click="selectedProduct = null"
                    class="absolute right-3 top-3 rounded-full bg-black/50 p-1.5 text-white
                           transition hover:bg-black/70">✕</button>
                <span class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs
                             font-medium text-primary-foreground">
                    {{ selectedProduct.category }}
                </span>
            </div>
        
            <div class="p-5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-xl font-bold">{{ selectedProduct.name }}</h2>
                    <span class="shrink-0 text-2xl font-bold text-primary">
                        {{ selectedProduct.price.toFixed(2) }} €
                    </span>
                </div>
            
                <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                    {{ selectedProduct.description }}
                </p>
            
                <div class="mt-5 flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <button type="button"
                            @click.stop="setQty(selectedProduct.id, getQty(selectedProduct.id) - 1)"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-border
                                   text-lg transition hover:bg-muted">−</button>
                        <span class="w-8 text-center font-medium">{{ getQty(selectedProduct.id) }}</span>
                        <button type="button"
                            @click.stop="setQty(selectedProduct.id, getQty(selectedProduct.id) + 1)"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-border
                                   text-lg transition hover:bg-muted">+</button>
                    </div>
                
                    <button type="button"
                        @click.stop="addToCart(selectedProduct); selectedProduct = null"
                        :disabled="form.processing"
                        class="flex-1 rounded-xl bg-primary py-2.5 text-sm font-medium
                               text-primary-foreground transition hover:bg-primary/90 disabled:opacity-50">
                        🛒 Lisa korvi
                    </button>
                </div>
            </div>
        </div>
    </Transition>
    </AppLayout>
    
</template>