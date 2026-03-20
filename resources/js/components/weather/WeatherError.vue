<script setup lang="ts">
defineProps<{ message?: string }>();
</script>

<template>
    <div class="flex flex-col items-center gap-3 rounded-2xl border border-destructive/30 bg-destructive/10 p-8 text-center">
        <h3 class="text-lg font-semibold text-destructive">Linna ei leitud</h3>
        <p class="max-w-xs text-sm text-muted-foreground">{{ message ?? 'Kontrolli linna nime ja proovi uuesti.' }}</p>
    </div>
</template>