
/* ==========================================
   ANNET AGRICULTURAL INITIATIVE
   Main JavaScript File
========================================== */


// 1. Function to load reusable HTML components

async function loadComponent(id, file) {

    try {

        const response = await fetch(file);

        if (!response.ok) {
            throw new Error("Failed to load " + file);
        }

        const html = await response.text();

        document.getElementById(id).innerHTML = html;

    } catch (error) {

        console.error("Component loading error:", error);

    }

}


// 2. Load Header and Footer

async function loadLayout() {

    // Load reusable header
    await loadComponent("header", "components/header.html");

    // Load reusable footer
    await loadComponent("footer", "components/footer.html");


    // 3. Mobile Navigation Menu

    const menuBtn = document.getElementById("menuBtn");

    const navLinks = document.getElementById("navLinks");

    if (menuBtn && navLinks) {

        menuBtn.addEventListener("click", function () {

            navLinks.classList.toggle("hidden");

            // Update accessibility attribute
            const isOpen = !navLinks.classList.contains("hidden");

            menuBtn.setAttribute("aria-expanded", String(isOpen));

        });

    }


    // 4. Automatically Update Copyright Year

    const year = document.getElementById("year");

    if (year) {

        year.textContent = new Date().getFullYear();

    }

}


// 5. Run when the page has loaded

document.addEventListener("DOMContentLoaded", function () {

    loadLayout();

});
