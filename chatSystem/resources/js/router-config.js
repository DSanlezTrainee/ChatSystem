// resources/js/router-config.js

import { router } from "@inertiajs/vue3";

// Configure global router behavior
router.on("start", () => {
    console.log("Navigation started");
});

router.on("finish", () => {
    console.log("Navigation finished");
});

// Set default options for router.visit
const defaultVisitOptions = {
    preserveScroll: false, // Don't preserve scroll position between page visits
    preserveState: true, // Preserve page state between visits when navigating within the same component
    replace: false, // Don't replace the current browser history entry
    only: [], // Don't limit which properties to update on partial reloads
};

// Custom router function with default options
export function visitRoute(url, options = {}) {
    router.visit(url, { ...defaultVisitOptions, ...options });
}

export function reloadPage(options = {}) {
    router.reload({ ...options });
}

export default router;
