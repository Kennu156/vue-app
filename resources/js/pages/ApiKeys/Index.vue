<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface ApiKey {
    id: number;
    name: string;
    key: string;
    last_used_at: string | null;
    created_at: string;
}

defineProps<{ apiKeys: ApiKey[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'API võtmed', href: '/api-keys' },
];

const form    = useForm({ name: '' });
const copied  = ref<number | null>(null);

function submit() {
    form.post(route('api-keys.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function deleteKey(id: number) {
    if (!confirm('Kustuta API võti?')) return;
    router.delete(route('api-keys.destroy', id), { preserveScroll: true });
}

function copyKey(key: string, id: number) {
    navigator.clipboard.writeText(key);
    copied.value = id;
    setTimeout(() => { copied.value = null; }, 2000);
}

function formatDate(d: string | null) {
    if (!d) return 'Pole kasutatud';
    return new Date(d).toLocaleDateString('et-EE', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head title="API võtmed" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl px-4 py-6 space-y-6">

            <div>
                <h1 class="text-2xl font-bold tracking-tight">🔑 API võtmed</h1>
                <p class="text-sm text-muted-foreground">
                    Genereeri võtmeid Volkswagen API kasutamiseks
                </p>
            </div>

            <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                <h2 class="mb-4 font-semibold">Loo uus API võti</h2>
                <form @submit.prevent="submit" class="flex gap-3">
                    <input v-model="form.name" type="text" required
                        placeholder="Võtme nimi (nt. Minu rakendus)"
                        class="flex-1 rounded-xl border border-border bg-background px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    <button type="submit" :disabled="form.processing"
                        class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                               transition hover:bg-primary/90 disabled:opacity-50">
                        + Genereeri
                    </button>
                </form>
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <div class="rounded-2xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border px-5 py-3 flex items-center justify-between">
                    <h2 class="font-semibold">Sinu võtmed</h2>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-xs">{{ apiKeys.length }}</span>
                </div>

                <div v-if="apiKeys.length === 0"
                    class="p-10 text-center text-sm text-muted-foreground">
                    <span class="text-3xl">🔑</span>
                    <p class="mt-2">Võtmeid pole. Genereeri esimene!</p>
                </div>

                <ul v-else class="divide-y divide-border">
                    <li v-for="k in apiKeys" :key="k.id" class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-sm">{{ k.name }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <code class="rounded bg-muted px-2 py-0.5 text-xs font-mono truncate max-w-xs">
                                        {{ k.key }}
                                    </code>
                                    <button type="button" @click="copyKey(k.key, k.id)"
                                        class="shrink-0 rounded-lg border border-border px-2 py-0.5 text-xs
                                               transition hover:bg-muted">
                                        {{ copied === k.id ? '✅ Kopeeritud' : '📋 Kopeeri' }}
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Viimati kasutatud: {{ formatDate(k.last_used_at) }}
                                </p>
                            </div>
                            <button type="button" @click="deleteKey(k.id)"
                                class="shrink-0 rounded-xl border border-destructive/40 px-3 py-1.5 text-xs
                                       text-destructive transition hover:bg-destructive/10">
                                🗑 Kustuta
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>