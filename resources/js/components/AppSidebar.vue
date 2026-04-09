<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Car, Folder, Key, LayoutGrid, Map, ShoppingBag } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    { title: 'Ilmaennustus',  href: '/dashboard',   icon: LayoutGrid },
    { title: 'Kaart',      href: '/map',         icon: Map },
    { title: 'Blogi',      href: '/blog',        icon: BookOpen },
    { title: 'Pood',       href: '/shop',        icon: ShoppingBag },
    { title: 'Volkswagen', href: '/volkswagens', icon: Car },
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">

        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')" class="sidebar-logo">
                            <div class="logo-icon">⬡</div>
                            <div class="logo-text-wrap">
                                <span class="logo-name">PLATVORMI</span>
                                <span class="logo-sub">v1.0</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <div class="sidebar-divider" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    text-decoration: none;
    padding: 0.25rem 0;
}

.logo-icon {
    font-size: 1.5rem;
    color: #d4a843;
    line-height: 1;
    flex-shrink: 0;
}

.logo-text-wrap {
    display: flex;
    flex-direction: column;
    gap: 0;
    overflow: hidden;
}

.logo-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.1rem;
    letter-spacing: 0.15em;
    color: rgb(232 230 224);
    line-height: 1;
}

.logo-sub {
    font-size: 0.6rem;
    letter-spacing: 0.2em;
    color: rgb(80 78 72);
    text-transform: uppercase;
}

.sidebar-divider {
    height: 1px;
    background: rgb(255 255 255 / 0.06);
    margin: 0.5rem 0;
}
</style>
