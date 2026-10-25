/*
 * Application bootstrap.
 *
 * spapp turns every <section> inside <main id="spapp"> into a page.
 * The URL hash (#recipes, #favorites, ...) decides which page is shown,
 * so navigation happens without reloading the page.
 */
$(document).ready(function () {
    // Views with the full-screen auth layout (no sidebar, no top bar)
    const AUTH_VIEWS = ["login", "register"];

    const app = $.spapp({
        templateDir: "./views/",
        defaultView: "dashboard",
        pageNotFound: "error_404"
    });

    function onViewChange() {
        const view = location.hash.slice(1) || "dashboard";

        $("body").toggleClass("auth-mode", AUTH_VIEWS.includes(view));

        $("#main-nav .nav-link")
            .removeClass("active")
            .filter('[href="#' + view + '"]')
            .addClass("active");

        const title = $('#main-nav .nav-link[href="#' + view + '"]').text().trim();
        $("#page-title").text(title || "Sofra");

        // Close the sidebar on mobile after navigation
        const sidebar = bootstrap.Offcanvas.getInstance("#sidebar");
        if (sidebar) sidebar.hide();

        window.scrollTo(0, 0);
    }

    window.addEventListener("hashchange", onViewChange);
    onViewChange();

    app.run();
});
