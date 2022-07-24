"use strict";
// Register service worker // TODO: uncomment
// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register("/sw.js");
// }

let ev,
    layer;

const __todo__ = msg => console.warn(msg),
      wait = async ms => await new Promise(res => setTimeout(res, ms)),
      $ = e => document.querySelector(e),
      body = document.body,
      title = $("h1"),
      search = $("nav input"),
      sideNav = $("#sn"),
      layers = [],

// Show popup
showPopup = (title, text, continueText, cancelText) => {
    console.log(title, text, continueText, cancelText); // TODO
},

// Get form data
getFormData = e => {
    let data = "";

    layer.querySelectorAll(e).forEach((e, i) => {
        data += `${i}=${e.value}&`;
    });

    return data;
},

// Post data to database
post = (fileName, data) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", `/post/${base + fileName}.php`, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.response); // todo: remove
        }
    };
    xhr.send(data);
},

// Fetch data from database
fetch = async (fileName, data, extendsLayer, isGlobal) => {
    // console.log(`/fetch/${(isGlobal ? "root" : base) + fileName}.php`);
    return await new Promise(res => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", `/fetch/${(isGlobal ? "root/" : base) + fileName}.php`, true);
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
            fetch(ev.target.dataset.n, "", false, true);
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
        // Side navigation
        C: () => {
            body.classList.toggle("sn");
        },
    }
};

// Login functions
fun.L = {
    // Login
    a: () => {
        fetch("/auth/login", getFormData("input"), true, true).then(data => {
            if (data) {
                showPopup(...data.split(";"));
            }
            else {
                if (location.pathname === "/login") {
                    location.href = "/home";
                }
                else {
                    location.reload();
                }
            }
        });
    },
    // Signup
    b: () => {
        fetch("/auth/signup", getFormData(".f:first-child input"), true, true).then(data => {
            if (data) {
                showPopup(...data.split(";"));
            }
            else {
                layer.querySelector(".hidden").classList.remove("hidden");
                layer.querySelector(".f").remove();
            }
        });
    },
    // Email verification
    c: () => {
        fetch("/auth/email", getFormData(".f input"), true, true).then(data => {
            if (data) {
                showPopup(...data.split(";"));
            }
            else {
                // Redirect to login
                fetch("login", "", false, true);
            }
        });
    }
};

// Event listener
document.addEventListener("click", e => {
    const f = e.target?.dataset.f;

    if (f) {
        ev = e;
        fun[f[0]]?.[f[1]]?.();
    }
});

// Search
search.addEventListener("keyup", ev => {
    if (ev.key === "Enter") {
        const q = search.value.trim().toLowerCase();

        if (q) {
            body.classList.remove("search");
            search.value = "";
            __todo__("search: " + q);
        }
    }
});
