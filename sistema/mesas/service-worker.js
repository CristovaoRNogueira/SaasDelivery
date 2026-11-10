var CACHE_VERSION = '1.0';


var CACHE_NAME = 'projeto-padrao-'+ CACHE_VERSION;


var REQUIRED_FILES = [
  'index.php',
  'painel/index.php',
];

self.addEventListener('install', function (event) {
 
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function (cache) {
       
        return cache.addAll(REQUIRED_FILES);
      })
      .then(function () {
        return self.skipWaiting();
      })
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    caches.match(event.request)
      .then(function (response) {
       
        if (response) {
          return response;
        }
        
        return fetch(event.request);
      }
      )
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim());
});