/* TokoOnline service worker — v1.0.0
 * - Navigations: network-first, fallback to /offline when unreachable.
 * - Static GET assets (/build/*, images, fonts): cache-first.
 * - NEVER cache non-GET, checkout/cart/account/API, or admin requests.
 */
const VERSION = 'tokoonline-v2';
const STATIC_CACHE = `${VERSION}-static`;
const OFFLINE_URL = '/offline';

const STATIC_PREFIXES = ['/build/', '/css/', '/js/', '/images/', '/marketing/'];
const STATIC_EXTS = ['.png', '.jpg', '.jpeg', '.webp', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.css', '.js'];
const NEVER_CACHE = ['/checkout', '/cart', '/account', '/admin', '/api/', '/login', '/register', '/logout', '/install', '/__pair', '/webhooks/'];

function isNeverCache(url) {
  const path = new URL(url).pathname;
  return NEVER_CACHE.some((p) => path === p || path.startsWith(p));
}

function isStaticAsset(url) {
  const { pathname } = new URL(url);
  if (STATIC_PREFIXES.some((p) => pathname.startsWith(p))) return true;
  return STATIC_EXTS.some((e) => pathname.endsWith(e));
}

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => cache.add(OFFLINE_URL)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== STATIC_CACHE).map((k) => caches.delete(k)))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET' || isNeverCache(request.url)) return;

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((res) => res)
        .catch(() => caches.match(OFFLINE_URL).then((r) => r || Response.error()))
    );
    return;
  }

  if (isStaticAsset(request.url)) {
    event.respondWith(
      caches.match(request).then(
        (hit) =>
          hit ||
          fetch(request).then((res) => {
            if (res && res.ok) {
              const copy = res.clone();
              caches.open(STATIC_CACHE).then((cache) => cache.put(request, copy));
            }
            return res;
          })
      )
    );
  }
});
