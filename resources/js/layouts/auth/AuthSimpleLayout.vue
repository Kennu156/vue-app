<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="auth-root">
        <div class="auth-grid" />
        <div class="auth-wrap">
            <Link :href="route('home')" class="auth-logo">
                <span class="logo-hex">⬡</span>
                <span class="logo-name">PLATVORMI</span>
            </Link>
            <div class="auth-header">
                <h1 class="auth-title">{{ title }}</h1>
                <p v-if="description" class="auth-desc">{{ description }}</p>
            </div>
            <slot />
            <Link :href="route('home')" class="auth-back">← Tagasi avalehele</Link>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap');

.auth-root {
    min-height: 100svh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: rgb(12 12 14);
    font-family: 'DM Sans', sans-serif;
    position: relative;
}

.auth-grid {
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 64px 64px;
}

.auth-wrap {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 360px;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.auth-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.logo-hex { font-size: 1.4rem; color: #d4a843; }

.logo-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.2rem;
    letter-spacing: 0.15em;
    color: rgb(232 230 224);
}

.auth-header { text-align: center; }

.auth-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.75rem;
    letter-spacing: 0.08em;
    color: rgb(232 230 224);
    margin: 0 0 0.35rem;
}

.auth-desc {
    font-size: 0.8rem;
    color: rgb(100 98 92);
    margin: 0;
}

.auth-back {
    text-align: center;
    font-size: 0.75rem;
    color: rgb(80 78 72);
    text-decoration: none;
    transition: color 0.15s;
    display: block;
}
.auth-back:hover { color: #d4a843; }

:deep(input) {
    background: rgb(10 10 12) !important;
    border: 1px solid rgb(255 255 255 / 0.08) !important;
    border-radius: 8px !important;
    color: rgb(232 230 224) !important;
    font-size: 0.875rem !important;
    transition: border-color 0.15s, box-shadow 0.15s;
}
:deep(input:focus) {
    border-color: rgba(212,168,67,0.4) !important;
    box-shadow: 0 0 0 3px rgba(212,168,67,0.08) !important;
    outline: none !important;
}
:deep(input::placeholder) { color: rgb(70 68 62) !important; }
:deep(label) {
    font-size: 0.75rem !important;
    color: rgb(140 138 132) !important;
}
:deep(button[type="submit"]) {
    background: #d4a843 !important;
    color: rgb(12 12 14) !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    cursor: pointer;
    transition: background 0.15s !important;
}
:deep(button[type="submit"]:hover) { background: #e0b84e !important; }
:deep(a) { color: #d4a843; font-size: 0.8rem; }
</style>
