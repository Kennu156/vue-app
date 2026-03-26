<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    auth?: { user?: { name: string } };
}>();
</script>

<template>
    <Head title="Avaleht">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-root">
        <div class="noise" />

        <div class="grid-bg" />

        <nav class="nav">
            <div class="nav-logo">
                <span class="logo-mark">⬡</span>
                <span class="logo-text">PLATVORMI</span>
            </div>
            <div class="nav-links">
                <template v-if="$page.props.auth?.user">
                    <span class="nav-user">{{ $page.props.auth.user.name }}</span>
                    <Link :href="route('dashboard')" class="btn-nav">
                        Sisene →
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="btn-ghost">Logi sisse</Link>
                    <Link :href="route('register')" class="btn-nav">Registreeru</Link>
                </template>
            </div>
        </nav>

        <section class="hero">
            <div class="hero-eyebrow">
                <span class="eyebrow-dot" />
                Kõik ühes platvormis
            </div>

            <h1 class="hero-title">
                <span class="title-line">SINU</span>
                <span class="title-line accent">DIGITAALNE</span>
                <span class="title-line">MAAILM</span>
            </h1>

            <p class="hero-sub">
                Ilmaennustus, interaktiivne kaart, blogi,<br />
                e-pood ja Volkswagen andmebaas — kõik ühes kohas.
            </p>

            <div class="hero-cta">
                <Link v-if="$page.props.auth?.user" :href="route('dashboard')" class="btn-primary">
                    Mine dashboardile
                    <span class="btn-arrow">→</span>
                </Link>
                <template v-else>
                    <Link :href="route('register')" class="btn-primary">
                        Alusta tasuta
                        <span class="btn-arrow">→</span>
                    </Link>
                    <Link :href="route('login')" class="btn-secondary">Logi sisse</Link>
                </template>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-num">5+</span>
                    <span class="stat-label">Moodulit</span>
                </div>
                <div class="stat-divider" />
                <div class="stat">
                    <span class="stat-num">API</span>
                    <span class="stat-label">Integratsioon</span>
                </div>
                <div class="stat-divider" />
                <div class="stat">
                    <span class="stat-num">∞</span>
                    <span class="stat-label">Võimalusi</span>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="features-header">
                <span class="section-label">— Funktsioonid</span>
                <h2 class="features-title">Kõik mida vajad</h2>
            </div>

            <div class="cards">
                <a :href="$page.props.auth?.user ? route('dashboard') : route('login')" class="card card--weather">
                    <div class="card-icon">🌤</div>
                    <div class="card-content">
                        <h3 class="card-title">Ilmaennustus</h3>
                        <p class="card-desc">Reaalajas ilmaandmed OpenWeatherMap API kaudu. 5-päeva prognoos, radar ja linnade otsing.</p>
                    </div>
                    <div class="card-tag">API + Cache</div>
                </a>

                <a :href="$page.props.auth?.user ? route('map') : route('login')" class="card card--map">
                    <div class="card-icon">🗺</div>
                    <div class="card-content">
                        <h3 class="card-title">Interaktiivne kaart</h3>
                        <p class="card-desc">Leaflet + OpenStreetMap. Lisa, muuda ja kustuta markereid. Klõpsa kaardil koordinaatide salvestamiseks.</p>
                    </div>
                    <div class="card-tag">Leaflet.js</div>
                </a>

                <a :href="$page.props.auth?.user ? route('blog') : route('login')" class="card card--blog">
                    <div class="card-icon">✍️</div>
                    <div class="card-content">
                        <h3 class="card-title">Blogi</h3>
                        <p class="card-desc">Postituste loomine, muutmine ja kustutamine. Kommentaaride haldus administraatoritele.</p>
                    </div>
                    <div class="card-tag">CRUD</div>
                </a>

                <a :href="$page.props.auth?.user ? route('shop') : route('login')" class="card card--shop">
                    <div class="card-icon">🛒</div>
                    <div class="card-content">
                        <h3 class="card-title">E-pood</h3>
                        <p class="card-desc">9 toodet, ostukorv, Stripe maksed. Tellimuste salvestamine andmebaasi.</p>
                    </div>
                    <div class="card-tag">Stripe</div>
                </a>

                <a :href="$page.props.auth?.user ? route('volkswagens') : route('login')" class="card card--vw card--wide">
                    <div class="card-icon">🚗</div>
                    <div class="card-content">
                        <h3 class="card-title">Volkswagen andmebaas</h3>
                        <p class="card-desc">Lisa, sirvi ja halda Volkswagen autosid. REST API võtmepõhise autentimisega — otsi, filtreeri, sorteeri.</p>
                    </div>
                    <div class="card-tag">REST API</div>
                    <div class="card-vw-badge">VW</div>
                </a>
            </div>
        </section>

        <section class="tech-strip">
            <span class="tech-label">Tehnoloogiad:</span>
            <div class="tech-items">
                <span>Laravel 12</span>
                <span class="sep">·</span>
                <span>Vue 3</span>
                <span class="sep">·</span>
                <span>Inertia.js</span>
                <span class="sep">·</span>
                <span>Tailwind CSS</span>
                <span class="sep">·</span>
                <span>Stripe</span>
                <span class="sep">·</span>
                <span>Leaflet</span>
                <span class="sep">·</span>
                <span>OpenWeatherMap</span>
                <span class="sep">·</span>
                <span>SQLite</span>
            </div>
        </section>

        <footer class="footer">
            <span>© 2026 Platvormi</span>
            <span class="sep">·</span>
            <span>Laravel + Vue + Inertia</span>
        </footer>
    </div>
</template>

<style scoped>
.welcome-root {
    min-height: 100vh;
    background: #0c0c0e;
    color: #e8e6e0;
    font-family: 'DM Sans', sans-serif;
    position: relative;
    overflow-x: hidden;
}

.noise {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 1;
    opacity: 0.035;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
    background-size: 200px;
}

.grid-bg {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 64px 64px;
}

.nav {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 2.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-mark {
    font-size: 1.4rem;
    color: #d4a843;
    line-height: 1;
}

.logo-text {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.3rem;
    letter-spacing: 0.15em;
    color: #e8e6e0;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-user {
    font-size: 0.8rem;
    color: rgba(232,230,224,0.5);
    letter-spacing: 0.05em;
}

.btn-ghost {
    font-size: 0.875rem;
    color: rgba(232,230,224,0.6);
    text-decoration: none;
    padding: 0.4rem 0.75rem;
    transition: color 0.2s;
}
.btn-ghost:hover { color: #e8e6e0; }

.btn-nav {
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.08em;
    color: #0c0c0e;
    background: #d4a843;
    text-decoration: none;
    padding: 0.5rem 1.25rem;
    border-radius: 4px;
    transition: background 0.2s, transform 0.1s;
}
.btn-nav:hover { background: #e0b84e; transform: translateY(-1px); }

.hero {
    position: relative;
    z-index: 5;
    padding: 6rem 2.5rem 4rem;
    max-width: 900px;
}

.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #d4a843;
    margin-bottom: 2rem;
    animation: fadeUp 0.6s ease both;
}

.eyebrow-dot {
    width: 6px;
    height: 6px;
    background: #d4a843;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.hero-title {
    display: flex;
    flex-direction: column;
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(5rem, 15vw, 10rem);
    line-height: 0.9;
    letter-spacing: 0.02em;
    margin: 0 0 2rem;
}

.title-line {
    animation: fadeUp 0.6s ease both;
}
.title-line:nth-child(2) { animation-delay: 0.1s; }
.title-line:nth-child(3) { animation-delay: 0.2s; }

.accent {
    color: transparent;
    -webkit-text-stroke: 2px #d4a843;
}

.hero-sub {
    font-size: 1.05rem;
    line-height: 1.7;
    color: rgba(232,230,224,0.55);
    margin-bottom: 2.5rem;
    max-width: 480px;
    animation: fadeUp 0.6s 0.3s ease both;
}

.hero-cta {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    animation: fadeUp 0.6s 0.4s ease both;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #d4a843;
    color: #0c0c0e;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    text-decoration: none;
    padding: 0.875rem 2rem;
    border-radius: 4px;
    transition: background 0.2s, transform 0.15s;
}
.btn-primary:hover { background: #e0b84e; transform: translateY(-2px); }

.btn-arrow { transition: transform 0.2s; }
.btn-primary:hover .btn-arrow { transform: translateX(4px); }

.btn-secondary {
    font-size: 0.875rem;
    color: rgba(232,230,224,0.5);
    text-decoration: none;
    padding: 0.875rem 1rem;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 4px;
    transition: all 0.2s;
}
.btn-secondary:hover {
    color: #e8e6e0;
    border-color: rgba(255,255,255,0.25);
}

.hero-stats {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 4rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.06);
    animation: fadeUp 0.6s 0.5s ease both;
}

.stat { display: flex; flex-direction: column; gap: 0.2rem; }

.stat-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    line-height: 1;
    color: #d4a843;
    letter-spacing: 0.05em;
}

.stat-label {
    font-size: 0.7rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: rgba(232,230,224,0.35);
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,0.08);
}

.features {
    position: relative;
    z-index: 5;
    padding: 4rem 2.5rem 5rem;
}

.features-header {
    display: flex;
    align-items: baseline;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.section-label {
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(232,230,224,0.3);
}

.features-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.5rem;
    letter-spacing: 0.05em;
    color: #e8e6e0;
    margin: 0;
}

.cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .cards { grid-template-columns: 1fr; }
    .card--wide { grid-column: span 1; }
}

.card {
    position: relative;
    background: #0f0f11;
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: background 0.2s;
    overflow: hidden;
}

.card:hover { background: #141416; }

.card--wide {
    grid-column: span 2;
    flex-direction: row;
    align-items: center;
    gap: 2rem;
}

.card-icon {
    font-size: 2rem;
    line-height: 1;
    flex-shrink: 0;
}

.card-content { flex: 1; }

.card-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.4rem;
    letter-spacing: 0.05em;
    color: #e8e6e0;
    margin: 0 0 0.4rem;
}

.card-desc {
    font-size: 0.825rem;
    line-height: 1.65;
    color: rgba(232,230,224,0.45);
    margin: 0;
}

.card-tag {
    font-size: 0.65rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #d4a843;
    border: 1px solid rgba(212,168,67,0.3);
    padding: 0.25rem 0.6rem;
    border-radius: 3px;
    align-self: flex-start;
    flex-shrink: 0;
}

.card-vw-badge {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-family: 'Bebas Neue', sans-serif;
    font-size: 5rem;
    letter-spacing: 0.05em;
    color: rgba(212,168,67,0.06);
    pointer-events: none;
    user-select: none;
}

.card--weather { border-left: 2px solid rgba(96,165,250,0.4); }
.card--map     { border-left: 2px solid rgba(52,211,153,0.4); }
.card--blog    { border-left: 2px solid rgba(167,139,250,0.4); }
.card--shop    { border-left: 2px solid rgba(251,146,60,0.4); }
.card--vw      { border-left: 2px solid rgba(212,168,67,0.5); }

.tech-strip {
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 1.5rem 2.5rem;
    border-top: 1px solid rgba(255,255,255,0.06);
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.tech-label {
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(232,230,224,0.25);
}

.tech-items {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    font-size: 0.75rem;
    color: rgba(232,230,224,0.35);
    letter-spacing: 0.05em;
}

.sep { color: rgba(232,230,224,0.15); }

.footer {
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 2.5rem;
    font-size: 0.75rem;
    color: rgba(232,230,224,0.2);
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>