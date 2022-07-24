"use strict";
const more = $("#more");

// Social media functions
fun.p = {
    // ----- Post -----
    // --- More ---
    // Edit
    A: () => {
        __todo__("Edit");
    },
    // Copy Text
    B: () => {
        __todo__("Copy Text");
    },
    // Share
    C: () => {
        __todo__("Share");
    },
    // Report
    D: () => {
        __todo__("Report");
    },
    // Close
    E: () => {
        body.appendChild(more);
    },
    // --- Post ---
    // Username/Community
    a: () => {
        __todo__("Username/Community");
        const v = ev.target.innerHTML;
        fetch(v[0], "q=" + v);
    },
    // Date
    b: () => {
        __todo__("Date");
    },
    // More
    c: () => {
        ev.target.parentNode.appendChild(more);
    },
    // Like
    d: () => {
        __todo__("Like");
    },
    // Show replies
    e: () => {
        __todo__("Show replies");
    },
    // Save
    f: () => {
        __todo__("Save");
    },
    // Reply
    g: () => {
        __todo__("Reply");
    }
};
