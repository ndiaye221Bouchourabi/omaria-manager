const CACHE_NAME = "omaria-v1";

// Fichiers à mettre en cache pour le mode hors-ligne
const ASSETS_TO_CACHE = [
    "/",
    "/css/collecte.css",
    "/css/dashboard.css",
    "/css/depenses.css",
    "/css/points.css",
    "/css/style.css",
    "/icons/icon-192.png",
    "/icons/icon-512.png",
    "/manifest.json",
];

// ── Installation : mise en cache des assets ──────────────────
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        }),
    );
    self.skipWaiting();
});

// ── Activation : suppression des anciens caches ───────────────
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key)),
            );
        }),
    );
    self.clients.claim();
});

// ── Fetch : cache en priorité, réseau en fallback ────────────
self.addEventListener("fetch", (event) => {
    // Ignorer les requêtes non-GET (POST, PUT, DELETE pour Laravel)
    if (event.request.method !== "GET") return;

    // Ignorer les requêtes vers d'autres domaines
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }
            return fetch(event.request)
                .then((networkResponse) => {
                    // Mettre en cache les nouvelles réponses CSS/JS/images
                    if (
                        networkResponse.ok &&
                        (event.request.url.includes("/css/") ||
                            event.request.url.includes("/js/") ||
                            event.request.url.includes("/img/") ||
                            event.request.url.includes("/icons/") ||
                            event.request.url.includes("/build/"))
                    ) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Page hors-ligne de secours si disponible
                    return caches.match("/");
                });
        }),
    );
});
