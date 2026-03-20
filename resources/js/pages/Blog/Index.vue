<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Post {
    id: number;
    title: string;
    description: string;
    comments_count: number;
    created_at: string;
    user: { id: number; name: string };
}

defineProps<{ posts: Post[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Blogi', href: '/blog' },
];

const page = usePage();
const auth = page.props.auth as any;

const showForm = ref(false);

const form = useForm({
    title:       '',
    description: '',
});

function submit() {
    form.post(route('posts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        },
    });
}

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('et-EE', {
        day: '2-digit', month: 'long', year: 'numeric',
    });
}
</script>

<template>
    <Head title="Blogi" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl px-4 py-6">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Blogi</h1>
                    <p class="text-sm text-muted-foreground">{{ posts.length }} postitust</p>
                </div>
                <button type="button" @click="showForm = !showForm"
                    class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                           shadow-sm transition hover:bg-primary/90">
                    + Uus postitus
                </button>
            </div>

            <Transition name="slide">
                <div v-if="showForm"
                    class="mb-6 rounded-2xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold">Uus postitus</h2>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Pealkiri *</label>
                            <input v-model="form.title" type="text" required
                                placeholder="Postituse pealkiri"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Sisu *</label>
                            <textarea v-model="form.description" rows="5" required
                                placeholder="Kirjuta siia..."
                                class="w-full resize-none rounded-xl border border-border bg-background px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary/50" />
                            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" :disabled="form.processing"
                                class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                                       transition hover:bg-primary/90 disabled:opacity-50">
                                {{ form.processing ? '⏳ Salvestan...' : 'Avalda postitus' }}
                            </button>
                            <button type="button" @click="showForm = false; form.reset()"
                                class="rounded-xl border border-border px-4 py-2 text-sm transition hover:bg-muted">
                                Tühista
                            </button>
                        </div>
                    </form>
                </div>
            </Transition>

            <div v-if="posts.length === 0"
                class="rounded-2xl border border-border bg-card p-12 text-center text-muted-foreground">
                <span class="text-4xl">📝</span>
                <p class="mt-3 text-sm">Postitusi pole veel. Ole esimene!</p>
            </div>

            <div v-else class="space-y-4">
                <a v-for="post in posts" :key="post.id"
                    :href="route('posts.show', post.id)"
                    class="block rounded-2xl border border-border bg-card p-5 shadow-sm transition
                           hover:shadow-md hover:border-primary/30">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-lg font-semibold">{{ post.title }}</h2>
                            <p class="mt-1 line-clamp-2 text-sm text-muted-foreground">{{ post.description }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-3 text-xs text-muted-foreground">
                        <span>✍️ {{ post.user.name }}</span>
                        <span>📅 {{ formatDate(post.created_at) }}</span>
                        <span>💬 {{ post.comments_count }} kommentaari</span>
                    </div>
                </a>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all .2s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-8px); }
</style>