<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { Marker } from '@/types/marker';
import { type BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({ iconUrl, iconRetinaUrl, shadowUrl });

const props = defineProps<{ markers: Marker[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Kaart', href: '/map' },
];

const mapEl      = ref<HTMLDivElement | null>(null);
const formOpen   = ref(false);
const editMarker = ref<Marker | null>(null);
const selectedId = ref<number | null>(null);

let map: L.Map | null = null;
const leafletMarkers  = new Map<number, L.Marker>();

const form = useForm({
    name:        '',
    latitude:    0 as number,
    longitude:   0 as number,
    description: '',
});

onMounted(() => {
    if (!mapEl.value) return;

    map = L.map(mapEl.value, {
        center: [58.25, 22.49],
        zoom: 7,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    map.on('click', (e: L.LeafletMouseEvent) => {
        openAddForm(e.latlng.lat, e.latlng.lng);
    });

    renderMarkers(props.markers);

    document.addEventListener('edit-marker', handleEditEvent);
});

onUnmounted(() => {
    map?.remove();
    map = null;
    document.removeEventListener('edit-marker', handleEditEvent);
});

function handleEditEvent(e: Event) {
    const id = (e as CustomEvent).detail;
    const m  = props.markers.find(m => m.id === id);
    if (m) openEditForm(m);
}

watch(() => props.markers, renderMarkers, { deep: true });

function renderMarkers(markers: Marker[]) {
    if (!map) return;
    const newIds = new Set(markers.map(m => m.id));

    for (const [id, lm] of leafletMarkers) {
        if (!newIds.has(id)) { lm.remove(); leafletMarkers.delete(id); }
    }

    for (const m of markers) {
        if (leafletMarkers.has(m.id)) {
            leafletMarkers.get(m.id)!
                .setLatLng([m.latitude, m.longitude])
                .setPopupContent(popupHtml(m));
        } else {
            const lm = L.marker([m.latitude, m.longitude])
                .addTo(map!)
                .bindPopup(popupHtml(m));
            lm.on('click', () => { selectedId.value = m.id; });
            leafletMarkers.set(m.id, lm);
        }
    }
}

function popupHtml(m: Marker): string {
    return `
        <div style="min-width:170px;font-family:sans-serif">
            <strong style="font-size:13px">${esc(m.name)}</strong>
            ${m.description ? `<p style="margin:4px 0 0;font-size:12px;color:#555">${esc(m.description)}</p>` : ''}
            <p style="margin:6px 0 0;font-size:11px;color:#999">${m.latitude.toFixed(5)}, ${m.longitude.toFixed(5)}</p>
            <button onclick="document.dispatchEvent(new CustomEvent('edit-marker', {detail: ${m.id}}))"
                style="margin-top:8px;padding:4px 12px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px">
                Muuda
            </button>
        </div>
    `;
}

function openAddForm(lat: number, lng: number) {
    form.reset();
    form.name        = '';
    form.description = '';
    form.latitude    = parseFloat(lat.toFixed(7));
    form.longitude   = parseFloat(lng.toFixed(7));
    editMarker.value = null;
    nextTick(() => { formOpen.value = true; });
}

function openEditForm(m: Marker) {
    form.name        = m.name;
    form.description = m.description ?? '';
    form.latitude    = m.latitude;
    form.longitude   = m.longitude;
    editMarker.value = m;
    nextTick(() => { formOpen.value = true; });
}

function closeForm() {
    formOpen.value   = false;
    editMarker.value = null;
    form.reset();
}

function submitForm() {
    if (editMarker.value) {
        form.put(route('markers.update', editMarker.value.id), {
            preserveScroll: true,
            onSuccess: closeForm,
        });
    } else {
        form.post(route('markers.store'), {
            preserveScroll: true,
            onSuccess: closeForm,
        });
    }
}

function deleteMarker() {
    if (!editMarker.value) return;
    if (!confirm(`Kustuta "${editMarker.value.name}"?`)) return;
    router.delete(route('markers.destroy', editMarker.value.id), {
        preserveScroll: true,
        onSuccess: closeForm,
    });
}

function flyTo(m: Marker) {
    selectedId.value = m.id;
    map?.flyTo([m.latitude, m.longitude], 14, { duration: 1 });
    leafletMarkers.get(m.id)?.openPopup();
}

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('et-EE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function esc(s: string) {
    return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]!));
}
</script>

<template>
    <Head title="Kaart" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Kaart</h1>
                    <p class="text-sm text-muted-foreground">
                        Klõpsa kaardil, et lisada marker · {{ markers.length }} markerit
                    </p>
                </div>
                <button
                    type="button"
                    @click.prevent.stop="openAddForm(58.25, 22.49)"
                    class="rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90">
                    + Lisa marker
                </button>
            </div>

            <div class="grid flex-1 grid-cols-1 gap-4 lg:grid-cols-[1fr_280px]">

                <div
                    ref="mapEl"
                    style="min-height:500px;height:500px;width:100%;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;"
                />

                <div style="display:flex;flex-direction:column;overflow:hidden;border-radius:12px;border:1px solid #e5e7eb;">
                    <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e5e7eb;padding:12px 16px;">
                        <h3 style="font-weight:600;margin:0">Markerid</h3>
                        <span style="background:#f3f4f6;border-radius:999px;padding:2px 8px;font-size:12px;">
                            {{ markers.length }}
                        </span>
                    </div>

                    <div v-if="markers.length === 0"
                        style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;text-align:center;color:#9ca3af;">
                        <p style="font-size:13px;margin:8px 0 0">Markereid pole.<br>Klõpsa kaardil lisamiseks.</p>
                    </div>

                    <ul v-else style="flex:1;overflow-y:auto;margin:0;padding:0;list-style:none;">
                        <li v-for="m in markers" :key="m.id"
                            @click="flyTo(m)"
                            :style="{
                                display:'flex',alignItems:'flex-start',gap:'12px',
                                padding:'12px 16px',cursor:'pointer',
                                borderBottom:'1px solid #f3f4f6',
                                background: selectedId === m.id ? '#f3f4f6' : 'transparent',
                            }">
                            <div style="min-width:0;flex:1">
                                <p style="margin:0;font-size:14px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ m.name }}</p>
                                <p v-if="m.description" style="margin:2px 0 0;font-size:12px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ m.description }}</p>
                                <p style="margin:2px 0 0;font-size:11px;color:#9ca3af">{{ formatDate(m.added) }}</p>
                            </div>
                            <button
                                type="button"
                                @click.stop="openEditForm(m)"
                                style="padding:4px;border:none;background:transparent;cursor:pointer;border-radius:6px;font-size:14px">
                                ✎
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <Transition name="fade">
    <div v-if="formOpen"
        @click="closeForm"
        style="position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);" />
</Transition>

        <Transition name="slide">
            <div v-if="formOpen"
                style="position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:9999;
               width:calc(100% - 32px);max-width:440px;background:white;border-radius:16px;
               border:1px solid #e5e7eb;padding:24px;box-shadow:0 25px 50px rgba(0,0,0,0.2);">

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h2 style="margin:0;font-size:18px;font-weight:700;">
                        {{ editMarker ? 'Muuda markerit' : 'Lisa uus marker' }}
                    </h2>
                    <button type="button" @click="closeForm"
                        style="border:none;background:transparent;cursor:pointer;font-size:18px;color:#6b7280;padding:4px;">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitForm">
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:4px;">Nimi *</label>
                        <input v-model="form.name" type="text" required placeholder="nt. Tallinna vanalinn"
                            style="width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;font-size:14px;box-sizing:border-box;outline:none;" />
                        <p v-if="form.errors.name" style="margin:4px 0 0;font-size:12px;color:#ef4444;">{{ form.errors.name }}</p>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:4px;">Laiuskraad *</label>
                            <input v-model="form.latitude" type="number" step="0.0000001" required
                                style="width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;font-size:14px;box-sizing:border-box;outline:none;" />
                        </div>
                        <div>
                            <label style="display:block;font-size:14px;font-weight:500;margin-bottom:4px;">Pikkuskraad *</label>
                            <input v-model="form.longitude" type="number" step="0.0000001" required
                                style="width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;font-size:14px;box-sizing:border-box;outline:none;" />
                        </div>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:14px;font-weight:500;margin-bottom:4px;">Kirjeldus</label>
                        <textarea v-model="form.description" rows="3" placeholder="Vabatahtlik kirjeldus..."
                            style="width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;font-size:14px;resize:none;box-sizing:border-box;outline:none;" />
                    </div>

                    <div style="display:flex;gap:8px;">
                        <button type="submit" :disabled="form.processing"
                            style="flex:1;background:#2563eb;color:white;border:none;border-radius:10px;padding:10px;font-size:14px;font-weight:500;cursor:pointer;">
                            {{ form.processing ? '⏳ Salvestan...' : (editMarker ? 'Salvesta' : 'Lisa marker') }}
                        </button>
                        <button v-if="editMarker" type="button" @click="deleteMarker"
                            style="border:1px solid #fca5a5;background:transparent;color:#ef4444;border-radius:10px;padding:10px 16px;font-size:14px;cursor:pointer;">
                            🗑
                        </button>
                    </div>
                </form>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform .2s ease, opacity .2s; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translate(-50%, calc(-50% + 16px)); }
</style>