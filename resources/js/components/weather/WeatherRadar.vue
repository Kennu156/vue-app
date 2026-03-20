<!--<script setup lang="ts">
defineProps<{ lat?: number; lon?: number; cityName?: string }>();
</script>

<template>
    <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">Sademete radar</h3>
        <div class="overflow-hidden rounded-xl border border-border shadow-sm">
            <iframe
                :src="`https://embed.windy.com/embed2.html?lat=${lat ?? 58.25}&lon=${lon ?? 22.49}&detailLat=${lat ?? 58.25}&detailLon=${lon ?? 22.49}&width=650&height=450&pane=wind&overlay=rain&product=ecmwf&level=surface&marker=true&message=true`"
                width="100%" height="400" frameborder="0"
                :title="`Ilmakaart: ${cityName ?? 'Kaart'}`"
                class="block" loading="lazy" />
        </div>
        <p class="mt-1 text-right text-xs text-muted-foreground">
            Allikas: <a href="https://www.windy.com" target="_blank" rel="noopener" class="underline underline-offset-2">Windy.com</a>
        </p>
    </div>
</template> -->