const CACHE_NAME = "omaria-v2";

const ASSETS_TO_CACHE = [
    "/css/collecte.css",
    "/css/dashboard.css",
    "/css/depenses.css",
    "/css/points.css",
    "/css/style.css",
    "/icons/icon-192.png",
    "/icons/icon-512.png",
    "/manifest.json",
];

// ── Installation ──────────────────────────────────────────────
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_TO_CACHE)),
    );
    self.skipWaiting();
});

// ── Activation ────────────────────────────────────────────────
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((k) => k !== CACHE_NAME)
                        .map((k) => caches.delete(k)),
                ),
            ),
    );
    self.clients.claim();
});

// ── Fetch ─────────────────────────────────────────────────────
self.addEventListener("fetch", (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // ✅ 1. Ignorer tout sauf GET
    if (request.method !== "GET") return;

    // ✅ 2. Ignorer les autres domaines (CDN, fonts, etc.)
    if (url.origin !== self.location.origin) return;

    // ✅ 3. CRITIQUE — laisser Laravel gérer toutes les pages HTML
    if (request.mode === "navigate") return;

    // ✅ 4. Ignorer les routes Laravel sensibles
    const laravelRoutes = [
        "/login",
        "/logout",
        "/dashboard",
        "/collectes",
        "/depenses",
        "/points",
        "/admin",
    ];
    if (laravelRoutes.some((route) => url.pathname.startsWith(route))) return;

    // ✅ 5. Seulement cacher les assets statiques
    const isStaticAsset =
        url.pathname.includes("/css/") ||
        url.pathname.includes("/js/") ||
        url.pathname.includes("/img/") ||
        url.pathname.includes("/icons/") ||
        url.pathname.includes("/build/") ||
        url.pathname.includes("/fonts/");

    if (!isStaticAsset) return;

    // Cache-first pour les assets
    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) return cached;

            return fetch(request).then((response) => {
                // Ne cacher que les réponses propres
                if (response.ok && response.type === "basic") {
                    const clone = response.clone();
                    caches
                        .open(CACHE_NAME)
                        .then((cache) => cache.put(request, clone));
                }
                return response;
            });
        }),
    );
});
