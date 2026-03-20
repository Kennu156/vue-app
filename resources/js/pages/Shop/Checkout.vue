<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface CartItem {
    product: { id: number; name: string; price: number };
    quantity: number;
    subtotal: number;
}

const props = defineProps<{
    items: CartItem[];
    total: number;
    stripeKey: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pood', href: '/shop' },
    { title: 'Ostukorv', href: '/cart' },
    { title: 'Maksmine', href: '/checkout' },
];

const stripe        = ref<any>(null);
const cardElement   = ref<any>(null);
const cardError     = ref('');
const processing    = ref(false);
const cardMounted   = ref(false);

const form = useForm({
    first_name:        '',
    last_name:         '',
    email:             '',
    phone:             '',
    payment_intent_id: '',
});

onMounted(async () => {
    const script    = document.createElement('script');
    script.src      = 'https://js.stripe.com/v3/';
    script.onload   = () => initStripe();
    document.head.appendChild(script);
});

function initStripe() {
    stripe.value = (window as any).Stripe(props.stripeKey);
    const elements = stripe.value.elements();
    cardElement.value = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#374151',
                '::placeholder': { color: '#9ca3af' },
            },
        },
    });
    cardElement.value.mount('#card-element');
    cardElement.value.on('change', (e: any) => {
        cardError.value = e.error ? e.error.message : '';
    });
    cardMounted.value = true;
}

async function submit() {
    if (!stripe.value || !cardElement.value) return;
    processing.value = true;
    cardError.value  = '';

    try {
        const res    = await fetch(route('checkout.intent'), {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as any)?.content,
            },
        });
        const { clientSecret } = await res.json();

        const { error, paymentIntent } = await stripe.value.confirmCardPayment(clientSecret, {
            payment_method: { card: cardElement.value },
        });

        if (error) {
            cardError.value  = error.message;
            processing.value = false;
            return;
        }

        form.payment_intent_id = paymentIntent.id;
        form.post(route('checkout.store'), {
            onFinish: () => { processing.value = false; },
        });

    } catch (e) {
        cardError.value  = 'Tehniline viga. Proovi uuesti.';
        processing.value = false;
    }
}
</script>

<template>
    <Head title="Maksmine" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl px-4 py-6 space-y-5">
            <h1 class="text-2xl font-bold tracking-tight">Maksmine</h1>

            <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                <h2 class="mb-3 font-semibold">Tellimuse kokkuvõte</h2>
                <div v-for="item in items" :key="item.product.id"
                    class="flex justify-between py-1.5 text-sm">
                    <span>{{ item.product.name }} × {{ item.quantity }}</span>
                    <span class="font-medium">{{ item.subtotal.toFixed(2) }} €</span>
                </div>
                <div class="mt-3 flex justify-between border-t border-border pt-3 font-bold">
                    <span>Kokku</span>
                    <span>{{ total.toFixed(2) }} €</span>
                </div>
            </div>

            <form @submit.prevent="submit"
                class="rounded-2xl border border-border bg-card p-5 shadow-sm space-y-4">
                <h2 class="font-semibold">Kontaktandmed</h2>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Eesnimi *</label>
                        <input v-model="form.first_name" type="text" required
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary/50" />
                        <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-500">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Perenimi *</label>
                        <input v-model="form.last_name" type="text" required
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">E-post *</label>
                    <input v-model="form.email" type="email" required
                        class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Telefon *</label>
                    <input v-model="form.phone" type="tel" required
                        class="w-full rounded-xl border border-border bg-background px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/50" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Kaardi andmed *</label>
                    <div id="card-element"
                        class="rounded-xl border border-border bg-background px-3 py-3" />
                    <p v-if="cardError" class="mt-1 text-xs text-red-500">{{ cardError }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        🔒 Testkaart: <code>4242 4242 4242 4242</code> · Kuupäev: mis tahes tulevikus · CVC: mis tahes 3 numbrit
                    </p>
                </div>

                <button type="submit"
                    :disabled="processing || !cardMounted"
                    class="w-full rounded-xl bg-primary py-3 text-sm font-medium text-primary-foreground
                           transition hover:bg-primary/90 disabled:opacity-50">
                    {{ processing ? '⏳ Töötlen makset...' : `Maksa ${total.toFixed(2)} €` }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>