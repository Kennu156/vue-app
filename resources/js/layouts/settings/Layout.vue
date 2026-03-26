<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentPath = computed(() => page.url);

const sidebarNavItems = [
    { title: 'Profiil', href: '/settings/profile' },
    { title: 'Parool', href: '/settings/password' },
    { title: 'Välimus', href: '/settings/appearance' },
];
</script>

<template>
    <div class="settings-root">
        <div class="settings-header">
            <Heading title="Seaded" description="Halda oma konto seadeid" />
        </div>

        <Separator class="settings-sep" />

        <div class="settings-body">
            <nav class="settings-nav">
                <Link
                    v-for="item in sidebarNavItems"
                    :key="item.href"
                    :href="item.href"
                    :class="['settings-nav-link', currentPath === item.href ? 'active' : '']"
                >
                    {{ item.title }}
                </Link>
            </nav>

            <div class="settings-content">
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.settings-root {
    padding: 1.5rem;
    max-width: 900px;
    width: 100%;
}

.settings-header {
    margin-bottom: 1.5rem;
}

.settings-sep {
    background: rgb(255 255 255 / 0.06) !important;
    margin-bottom: 1.5rem;
}

.settings-body {
    display: flex;
    gap: 3rem;
    flex-wrap: wrap;
}

.settings-nav {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    min-width: 160px;
    flex-shrink: 0;
}

.settings-nav-link {
    display: block;
    font-size: 0.8rem;
    letter-spacing: 0.03em;
    color: rgb(100 98 92);
    text-decoration: none;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: all 0.15s;
    border-left: 2px solid transparent;
}

.settings-nav-link:hover {
    color: rgb(232 230 224);
    background: rgb(22 22 26);
}

.settings-nav-link.active {
    color: #d4a843;
    background: rgb(22 22 26);
    border-left-color: #d4a843;
}

.settings-content {
    flex: 1;
    min-width: 0;
}

@media (max-width: 640px) {
    .settings-body { flex-direction: column; gap: 1.5rem; }
    .settings-nav { flex-direction: row; flex-wrap: wrap; min-width: unset; }
}
</style>
