<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{ order: { id: number } }>();
</script>

<template>
    <Head title="Makse ebaõnnestus" />
    <AppLayout :breadcrumbs="[{ title: 'Pood', href: '/shop' }, { title: 'Makse ebaõnnestus', href: '#' }]">
        <div class="mx-auto max-w-lg px-4 py-12 text-center">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-8 shadow-sm dark:border-red-800 dark:bg-red-950">
                <span class="text-6xl">❌</span>
                <h1 class="mt-4 text-2xl font-bold text-red-700 dark:text-red-400">
                    Makse ebaõnnestus
                </h1>
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                    Midagi läks valesti. Tooted on ostukorvis alles.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <a :href="route('checkout')"
                        class="rounded-xl bg-primary px-5 py-2.5 text-sm font-medium
                               text-primary-foreground transition hover:bg-primary/90">
                        Proovi uuesti
                    </a>
                    <a :href="route('cart')"
                        class="rounded-xl border border-border px-5 py-2.5 text-sm transition hover:bg-muted">
                        Ostukorv
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>