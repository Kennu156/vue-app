<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface CartItem {
    product: { id: number; name: string; image: string; price: number };
    quantity: number;
    subtotal: number;
}

const props = defineProps<{ items: CartItem[]; total: number }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pood', href: '/shop' },
    { title: 'Ostukorv', href: '/cart' },
];

const updateForm = useForm({ product_id: 0, quantity: 0 });
const removeForm = useForm({ product_id: 0 });
const clearForm  = useForm({});

function updateQty(productId: number, qty: number) {
    updateForm.product_id = productId;
    updateForm.quantity   = qty;
    updateForm.post(route('cart.update'), { preserveScroll: true });
}

function remove(productId: number) {
    removeForm.product_id = productId;
    removeForm.post(route('cart.remove'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Ostukorv" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl px-4 py-6">
            <h1 class="mb-6 text-2xl font-bold tracking-tight">Ostukorv</h1>

            <div v-if="items.length === 0"
                class="rounded-2xl border border-border bg-card p-12 text-center text-muted-foreground">
                <span class="text-4xl">🛒</span>
                <p class="mt-3 text-sm">Ostukorv on tühi.</p>
                <a :href="route('shop')"
                    class="mt-4 inline-block rounded-xl bg-primary px-5 py-2 text-sm font-medium
                           text-primary-foreground transition hover:bg-primary/90">
                    Mine poodi
                </a>
            </div>

            <div v-else class="space-y-4">
                <div v-for="item in items" :key="item.product.id"
                    class="flex items-center gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm">
                    <img :src="item.product.image" :alt="item.product.name"
                        class="h-16 w-16 rounded-xl object-cover shrink-0" />

                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">{{ item.product.name }}</p>
                        <p class="text-sm text-muted-foreground">{{ item.product.price.toFixed(2) }} € / tk</p>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button"
                            @click="updateQty(item.product.id, item.quantity - 1)"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-border
                                   text-sm transition hover:bg-muted">−</button>
                        <span class="w-8 text-center text-sm font-medium">{{ item.quantity }}</span>
                        <button type="button"
                            @click="updateQty(item.product.id, item.quantity + 1)"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-border
                                   text-sm transition hover:bg-muted">+</button>
                    </div>

                    <p class="w-20 text-right font-semibold">{{ item.subtotal.toFixed(2) }} €</p>

                    <button type="button" @click="remove(item.product.id)"
                        class="text-destructive transition hover:opacity-70">🗑</button>
                </div>

                <div class="rounded-2xl border border-border bg-card p-5">
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span>Kokku:</span>
                        <span>{{ total.toFixed(2) }} €</span>
                    </div>
                    <div class="mt-4 flex gap-3">
                        <a :href="route('checkout')"
                            class="flex-1 rounded-xl bg-primary py-2.5 text-center text-sm font-medium
                                   text-primary-foreground transition hover:bg-primary/90">
                            Maksma →
                        </a>
                        <button type="button"
                            @click="clearForm.post(route('cart.clear'))"
                            class="rounded-xl border border-border px-4 py-2.5 text-sm transition hover:bg-muted">
                            Tühjenda
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>