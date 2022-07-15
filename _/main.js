"use strict";
// Register service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register("/sw.js");
}

let ev,
    layer,
    base = document.querySelector("html").dataset.base || "sm/";

const __todo__ = msg => console.warn(msg),
      wait = async ms => await new Promise(res => setTimeout(res, ms)),
      $ = e => document.querySelector(e),
      body = document.body,
      title = $("h1"),
      search = $("nav input"),
      acc = $("menu"),
      username = $("html").dataset.name,
      layers = [],

// Hide account menu
hideAcc = () => {
    acc.classList.add("hidden");
    document.removeEventListener("click", hideAcc);
},

// Search
find = () => {
    const q = search.value.trim().toLowerCase();

    if (q) {
        body.classList.remove("search");
        search.value = "";
        __todo__("search");
    }
},

// Post data to database
post = (fileName, data) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", `/${base}db/post/${fileName}.php`, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.response); // todo: remove
        }
    };
    xhr.send(data);
},

fetch = async (fileName, data, extendsLayer) => {
    return await new Promise(res => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", `/${base}db/fetch/${fileName}.php`, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (extendsLayer) {
                    res(xhr.response);
                }
                else {
                    // --- New layer ---
                    const temp = document.createElement("div");
                    temp.innerHTML = xhr.response;
                    console.log(xhr.response);
                    layer = temp.querySelector("main");
                    body.appendChild(layer);

                    layers.push(layer);
                    layers.length === 1 ? body.classList.remove("back") : body.classList.add("back");

                    // --- Title ---
                    const ttl = layer.dataset.title;

                    if (ttl) {
                        document.title = ttl;
                        title.innerHTML = ttl;
                        history.pushState(null, "", "/" + layer.dataset.url);
                    }

                    res();
                }
            }
        };
        xhr.send(data || "");
    });
},

// Global functions
fun = {
    _: {
        // New global layer
        $: () => {
            const temp = base;
            base = "sm/";
            fetch(ev.target.dataset.n);
            base = temp;
        },
        // New layer
        _: () => {
            fetch(ev.target.dataset.n);
        },
        // ----- Nav -----
        // Home/Back
        A: () => {            
            if (body.classList.contains("back")) {
                history.back();
                layers.pop().remove();
                layer = layers.at(-1);
                if (layers.length === 1) body.classList.remove("back");
                title.innerHTML = document.title;
            }
            else {
                __todo__("homepage/index");
            }
        },
        // Search
        B: () => {
            if (body.classList.contains("search")) {
                body.classList.remove("search");
                search.value = "";
            }
            else {
                body.classList.add("search");
                search.focus();
            }
        },
        // Account menu
        C: () => {
            acc.classList.toggle("hidden");

            if (!acc.classList.contains("hidden")) {
                wait(100).then(() => document.addEventListener("click", hideAcc));
            }
        },
    }
};

// Event listener
document.addEventListener("click", e => {
    const f = e.target?.dataset.f;

    if (f) {
        ev = e;
        fun[f[0]][f[1]]();
    }
});

// Search
search.addEventListener("keyup", ev => {
    if (ev.key === "Enter") find();
});
