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

            <div class="auth-card">
                <div class="auth-card-header">
                    <h1 class="auth-title">{{ title }}</h1>
                    <p v-if="description" class="auth-desc">{{ description }}</p>
                </div>
                <div class="auth-card-body">
                    <slot />
                </div>
            </div>

            <p class="auth-footer">
                <Link :href="route('home')" class="auth-footer-link">← Tagasi avalehele</Link>
            </p>
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
    max-width: 400px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.auth-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    justify-content: center;
}

.logo-hex {
    font-size: 1.4rem;
    color: #d4a843;
}

.logo-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.2rem;
    letter-spacing: 0.15em;
    color: rgb(232 230 224);
}

.auth-card {
    background: rgb(15 15 18);
    border: 1px solid rgb(255 255 255 / 0.07);
    border-radius: 12px;
    overflow: hidden;
}

.auth-card-header {
    padding: 2rem 2rem 1.25rem;
    border-bottom: 1px solid rgb(255 255 255 / 0.05);
    text-align: center;
}

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
    line-height: 1.5;
}

.auth-card-body {
    padding: 1.75rem 2rem 2rem;
}

.auth-footer {
    text-align: center;
    margin: 0;
}

.auth-footer-link {
    font-size: 0.75rem;
    color: rgb(80 78 72);
    text-decoration: none;
    letter-spacing: 0.03em;
    transition: color 0.15s;
}
.auth-footer-link:hover { color: #d4a843; }

:deep(input) {
    background: rgb(10 10 12) !important;
    border: 1px solid rgb(255 255 255 / 0.08) !important;
    border-radius: 8px !important;
    color: rgb(232 230 224) !important;
    font-size: 0.875rem !important;
    padding: 0.625rem 0.875rem !important;
    width: 100%;
    transition: border-color 0.15s, box-shadow 0.15s;
}

:deep(input:focus) {
    border-color: rgba(212, 168, 67, 0.4) !important;
    box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.08) !important;
    outline: none !important;
}

:deep(input::placeholder) {
    color: rgb(70 68 62) !important;
}

:deep(label) {
    font-size: 0.75rem !important;
    letter-spacing: 0.05em !important;
    color: rgb(140 138 132) !important;
    margin-bottom: 0.35rem !important;
    display: block;
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
    padding: 0.75rem 1.5rem !important;
    width: 100%;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s !important;
}

:deep(button[type="submit"]:hover) {
    background: #e0b84e !important;
    transform: translateY(-1px) !important;
}

:deep(a) {
    color: #d4a843;
    text-decoration: none;
    font-size: 0.8rem;
    transition: opacity 0.15s;
}
:deep(a:hover) { opacity: 0.8; }

:deep([data-checkbox]) {
    accent-color: #d4a843;
}
</style>
