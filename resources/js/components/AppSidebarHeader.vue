<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();
</script>

<template>
    <header class="sidebar-header">
        <div class="header-left">
            <SidebarTrigger class="trigger-btn" />
            <div v-if="breadcrumbs?.length" class="breadcrumb-wrap">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
        <div class="header-right">
            <span class="header-badge">🟡 LIVE</span>
        </div>
    </header>
</template>

<style scoped>
.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 56px;
    padding: 0 1.25rem;
    border-bottom: 1px solid rgb(255 255 255 / 0.06);
    background: rgb(12 12 14);
    flex-shrink: 0;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.trigger-btn {
    color: rgb(100 98 92) !important;
    transition: color 0.15s;
}
.trigger-btn:hover { color: rgb(212 168 67) !important; }

.breadcrumb-wrap {
    font-size: 0.75rem;
    color: rgb(100 98 92);
}

.header-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-badge {
    font-size: 0.6rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: rgb(80 78 72);
    border: 1px solid rgb(255 255 255 / 0.06);
    padding: 0.2rem 0.5rem;
    border-radius: 3px;
}
</style>
