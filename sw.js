const v = 1,
urls = [
    "/manifest.json",
    "/font/roboto.woff2",
    "/css/main.css",
    "/js/main.js",
    "/sw.js",
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
