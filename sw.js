const v = 1,
urls = [
    "/manifest.json",
    "/sw.js",
    "/_/main.js",
    "/_/main.css",
    "/_/fonts/roboto.woff2",
];

self.addEventListener("install", ev => {
    ev.waitUntil(
        caches.open("v" + v).then(async cache => {
            await cache.addAll(urls.map(url => url + "?v" + v));
        })
    );
});

self.addEventListener("fetch", ev => {
    ev.respondWith(
        caches.match(ev.request).then(res => {
            return res ? res : fetch(ev.request);
        })
    );
});
