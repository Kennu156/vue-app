<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{ items: NavItem[] }>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-2">
        <SidebarGroupLabel class="nav-label">Navigatsioon</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="page.url.startsWith(item.href)"
                    class="nav-btn"
                >
                    <Link :href="item.href" class="nav-link">
                        <component :is="item.icon" class="nav-icon" />
                        <span class="nav-text">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

<style scoped>
.nav-label {
    font-size: 0.6rem !important;
    letter-spacing: 0.2em !important;
    text-transform: uppercase !important;
    color: rgb(60 58 52) !important;
    margin-bottom: 0.25rem;
    padding-left: 0.5rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    text-decoration: none;
    width: 100%;
}

.nav-icon {
    width: 15px;
    height: 15px;
    flex-shrink: 0;
    opacity: 0.6;
}

.nav-text {
    font-size: 0.8rem;
    letter-spacing: 0.02em;
}
</style>
