const CACHE_VERSION = 'v2';
const STATIC_CACHE = `delivery-static-${CACHE_VERSION}`;
const START_PAGES_CACHE = `delivery-start-pages-${CACHE_VERSION}`;
const OLD_CACHE_PREFIXES = ['Aplicativo Delivery-', 'delivery-static-', 'delivery-start-pages-'];

const STATIC_FILES = [
  './app_assets/css/style.css',
  './app_assets/css/bootstrap.css'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => cache.addAll(STATIC_FILES))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.map(key => {
        const pertenceAoApp = OLD_CACHE_PREFIXES.some(prefix => key.startsWith(prefix));
        const cacheAtual = key === STATIC_CACHE || key === START_PAGES_CACHE;
        return pertenceAoApp && !cacheAtual ? caches.delete(key) : Promise.resolve(false);
      })))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('message', event => {
  if (!event.data || event.data.type !== 'CACHE_START_URL' || !event.data.url) {
    return;
  }

  const startUrl = new URL(event.data.url, self.location.origin);
  if (startUrl.origin !== self.location.origin) {
    return;
  }

  event.waitUntil(
    fetch(startUrl.href, { credentials: 'include' })
      .then(response => {
        if (!response.ok) {
          throw new Error('Não foi possível armazenar a página inicial do app.');
        }
        return caches.open(START_PAGES_CACHE)
          .then(cache => cache.put(startUrl.href, response));
      })
  );
});

self.addEventListener('fetch', event => {
  const request = event.request;
  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);
  if (url.origin !== self.location.origin || url.pathname.endsWith('/manifest.php')) {
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match(request).then(response => {
        return response || caches.match(url.href);
      }))
    );
    return;
  }

  const arquivoEstatico = /\.(?:css|js|png|jpe?g|gif|webp|svg|woff2?|ttf)$/i.test(url.pathname);
  if (arquivoEstatico) {
    event.respondWith(
      caches.match(request).then(cached => cached || fetch(request).then(response => {
        if (response.ok) {
          const copia = response.clone();
          caches.open(STATIC_CACHE).then(cache => cache.put(request, copia));
        }
        return response;
      }))
    );
  }
});
