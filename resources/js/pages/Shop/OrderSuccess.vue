<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    order: {
        id: number;
        first_name: string;
        total: string | number;
        items: { product: { name: string }; quantity: number; price: string | number }[];
    };
}>();
</script>

<template>
    <Head title="Tellimus kinnitatud" />
    <AppLayout :breadcrumbs="[{ title: 'Pood', href: '/shop' }, { title: 'Tellimus kinnitatud', href: '#' }]">
        <div class="mx-auto max-w-lg px-4 py-12 text-center">
            <div class="rounded-2xl border border-green-200 bg-green-50 p-8 shadow-sm dark:border-green-800 dark:bg-green-950">
                <span class="text-6xl">✅</span>
                <h1 class="mt-4 text-2xl font-bold text-green-700 dark:text-green-400">
                    Makse õnnestus!
                </h1>
                <p class="mt-2 text-sm text-green-600 dark:text-green-500">
                    Tere, {{ order.first_name }}! Sinu tellimus #{{ order.id }} on kinnitatud.
                </p>

                <div class="mt-6 space-y-2 text-left">
                    <div v-for="item in order.items" :key="item.product.name"
                        class="flex justify-between text-sm">
                        <span>{{ item.product.name }} × {{ item.quantity }}</span>
                        <span>{{ (item.price * item.quantity).toFixed(2) }} €</span>
                    </div>
                    <div class="flex justify-between border-t border-green-200 pt-2 font-bold dark:border-green-800">
                        <span>Kokku</span>
                        <span>{{ order.total.toFixed(2) }} €</span>
                    </div>
                </div>

                <a :href="route('shop')"
                    class="mt-6 inline-block rounded-xl bg-primary px-6 py-2.5 text-sm font-medium
                           text-primary-foreground transition hover:bg-primary/90">
                    Tagasi poodi
                </a>
            </div>
        </div>
    </AppLayout>
</template>