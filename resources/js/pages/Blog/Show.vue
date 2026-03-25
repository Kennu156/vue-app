<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Comment {
    id: number;
    body: string;
    created_at: string;
    user: { id: number; name: string };
}

interface Post {
    id: number;
    title: string;
    description: string;
    created_at: string;
    updated_at: string;
    user: { id: number; name: string };
    comments: Comment[];
}

const props = defineProps<{ post: Post }>();

const page     = usePage();
const authUser = (page.props.auth as any).user;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Blogi', href: '/blog' },
    { title: props.post.title, href: `/blog/${props.post.id}` },
];

const commentForm = useForm({ body: '' });

function submitComment() {
    commentForm.post(route('comments.store', props.post.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
}

function deleteComment(id: number) {
    if (!confirm('Kustuta kommentaar?')) return;
    router.delete(route('comments.destroy', id), { preserveScroll: true });
}

const editing  = ref(false);
const editForm = useForm({
    title:       props.post.title,
    description: props.post.description,
});

function saveEdit() {
    editForm.put(route('posts.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => { editing.value = false; },
    });
}

function deletePost() {
    if (!confirm(`Kustuta postitus "${props.post.title}"?`)) return;
    router.delete(route('posts.destroy', props.post.id));
}

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('et-EE', {
        day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
}

const canEditPost = authUser.id === props.post.user.id || authUser.is_admin;
</script>

<template>
    <Head :title="post.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl px-4 py-6 space-y-6">

            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">

                <div v-if="editing" class="space-y-4">
                    <input v-model="editForm.title" type="text" required
                        class="w-full rounded-xl border border-border bg-background px-3 py-2 text-lg font-bold
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    <textarea v-model="editForm.description" rows="8" required
                        class="w-full resize-none rounded-xl border border-border bg-background px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    <div class="flex gap-2">
                        <button type="button" @click="saveEdit" :disabled="editForm.processing"
                            class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                                   transition hover:bg-primary/90 disabled:opacity-50">
                            {{ editForm.processing ? '⏳...' : 'Salvesta' }}
                        </button>
                        <button type="button" @click="editing = false"
                            class="rounded-xl border border-border px-4 py-2 text-sm transition hover:bg-muted">
                            Tühista
                        </button>
                    </div>
                </div>

                <div v-else>
                    <div class="flex items-start justify-between gap-3">
                        <h1 class="text-2xl font-bold tracking-tight">{{ post.title }}</h1>
                        <div v-if="canEditPost" class="flex gap-2 shrink-0">
                            <button type="button" @click="editing = true"
                                class="rounded-lg border border-border px-3 py-1.5 text-xs transition hover:bg-muted">
                                Muuda
                            </button>
                            <button type="button" @click="deletePost"
                                class="rounded-lg border border-destructive/40 px-3 py-1.5 text-xs text-destructive
                                       transition hover:bg-destructive/10">
                                🗑 Kustuta
                            </button>
                        </div>
                    </div>

                    <div class="mt-2 flex gap-3 text-xs text-muted-foreground">
                        <span>✍️ {{ post.user.name }}</span>
                        <span>📅 {{ formatDate(post.created_at) }}</span>
                    </div>

                    <div class="mt-4 whitespace-pre-wrap text-sm leading-relaxed">{{ post.description }}</div>
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">
                    Kommentaarid ({{ post.comments.length }})
                </h2>

                <form @submit.prevent="submitComment" class="mb-6">
                    <textarea v-model="commentForm.body" rows="3" required
                        placeholder="Kirjuta kommentaar..."
                        class="w-full resize-none rounded-xl border border-border bg-background px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    <p v-if="commentForm.errors.body" class="mt-1 text-xs text-red-500">
                        {{ commentForm.errors.body }}
                    </p>
                    <button type="submit" :disabled="commentForm.processing"
                        class="mt-2 rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                               transition hover:bg-primary/90 disabled:opacity-50">
                        {{ commentForm.processing ? '⏳...' : 'Lisa kommentaar' }}
                    </button>
                </form>

                <div v-if="post.comments.length === 0"
                    class="py-6 text-center text-sm text-muted-foreground">
                    Kommentaare pole veel. Ole esimene!
                </div>

                <div v-else class="space-y-3">
                    <div v-for="comment in post.comments" :key="comment.id"
                        class="flex gap-3 rounded-xl border border-border bg-background p-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10
                                    text-sm font-bold text-primary">
                            {{ comment.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-medium">{{ comment.user.name }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-muted-foreground">{{ formatDate(comment.created_at) }}</span>
                                    <button
                                        v-if="authUser.id === comment.user.id || authUser.is_admin"
                                        type="button"
                                        @click="deleteComment(comment.id)"
                                        class="text-xs text-destructive transition hover:opacity-70">
                                        🗑
                                    </button>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground">{{ comment.body }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>