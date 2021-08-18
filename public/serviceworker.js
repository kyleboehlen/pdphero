var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/assets/css/about.css',
    '/assets/css/affirmations.css',
    '/assets/css/app.css',
    '/assets/css/auth.css',
    '/assets/css/goals.css',
    '/assets/css/habits.css',
    '/assets/css/home.css',
    '/assets/css/journal.css',
    '/assets/css/profile.css',
    '/assets/css/todo.css',
    '/assets/js/app.js',
    '/assets/icons/goals-white.png',
    '/assets/icons/habits-white.png',
    '/assets/icons/journal-white.png',
    '/assets/icons/todo-white.png',
    '/pwa/icon-72x72.png',
    '/pwa/icon-96x96.png',
    '/pwa/icon-128x128.png',
    '/pwa/icon-144x144.png',
    '/pwa/icon-152x152.png',
    '/pwa/icon-192x192.png',
    '/pwa/icon-384x384.png',
    '/pwa/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});

// Webpush notifications
self.addEventListener('push', function (e) {
    if(!(self.Notification && self.Notification.permission === 'granted')){
        // notifications aren't supported or permission not granted!
        return;
    }

    if(e.data){
        var msg = e.data.json();
        console.log(msg)
        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon,
            actions: msg.actions
        }));
    }
});