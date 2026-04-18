document.addEventListener("DOMContentLoaded", function () {
    // 1. Fitur Auto-hide Alerts:
    // Menghilangkan pesan sukses/error secara otomatis setelah 4 detik.
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                // Efek transisi menghilang perlahan (fade out)
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                
                // Menghapus elemen dari HTML setelah transisi selesai
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000); // 4000 ms = 4 detik
    }

    // 2. Fitur Highlight Navigasi Aktif:
    // Menyorot menu sidebar sebelah kiri berdasarkan halaman yang sedang dibuka.
    const currentUrl = window.location.href;
    const sidebarLinks = document.querySelectorAll(".sidebar-nav a");
    
    sidebarLinks.forEach(link => {
        // Cek apakah URL teks link cocok dengan URL di browser
        if (currentUrl.includes(link.getAttribute("href")) && link.getAttribute("href") !== "#") {
            // Berikan penanda visual
            link.style.color = "var(--white)";
            link.style.backgroundColor = "rgba(255, 255, 255, 0.1)";
            link.style.borderLeft = "3px solid var(--blue)";
        }
    });
});
