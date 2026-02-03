document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.querySelector(".menu-btn");

    if (!sidebar || !menuBtn) return;

    menuBtn.addEventListener("click", function () {
        sidebar.classList.toggle("hide");
    });

    const content = document.querySelector(".content");
    if (content) {
        content.addEventListener("click", function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.add("hide");
            }
        });
    }

    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("hide");
        }
    });
});
