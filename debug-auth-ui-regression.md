# Debug Session: auth-ui-regression

Status: OPEN

Symptoms:
- Semua route auth (`/dashboard`, `/admin/gateway`, panel admin) masih error / tidak bisa diakses.
- Ada `Attempt to read property "name" on string` di `routes/web.php`.
- Layout masih oversize, popup aneh, teks overflow, dan belum responsif lintas device.

Initial hypotheses:
- H1: Masih ada data `kecamatan` atau field serupa yang kadang string, kadang object, lalu diakses pakai `->name` atau `.name`.
- H2: Sebagian route auth/admin masih membaca state user/data lama dari cache route/view yang belum sinkron dengan kode sekarang.
- H3: Modal/detail card memiliki width, scale, atau zoom-sensitive sizing yang tidak dibatasi sehingga overflow di viewport kecil dan desktop.
- H4: Komponen global seperti `Navbar`, custom cursor, provider auth lokal, atau style legacy masih memengaruhi layout akhir dan memicu UI aneh.
- H5: Ada mismatch data shape antara backend Inertia props dan komponen React, sehingga dashboard/admin gagal render walau route lolos middleware.

Evidence plan:
- Tangkap stack trace Laravel aktual untuk request `/dashboard` dan `/admin/gateway`.
- Telusuri semua akses `.name` / `->name` pada data yang bisa berbentuk string.
- Jalankan diagnostics serta build/runtime check untuk komponen utama UI.
- Verifikasi CSS/layout constraints pada modal, hero, navbar, dashboard, dan admin gateway.
