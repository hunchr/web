const addResourcesToCache = async res => {
    const cache = await caches.open("v1");
    await cache.addAll(res);
};

self.addEventListener("install", ev => {
    ev.waitUntil(
        addResourcesToCache([
            "/_/main.css",
            "/_/main.js"
        ])
    );
});
