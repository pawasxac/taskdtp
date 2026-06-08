# 📊 Native Eraser.io Diagram Syntax - Daily Coffee User Flow

Dokumen ini berisi kode diagram yang sudah ditambahkan proses input kredensial (Username/Email & Password) saat user berada di halaman login sebelum masuk ke dashboard utama.

---

```
// Nodes & Pages Definitions
Start [shape: oval, icon: play, label: "User Mengakses Website"]
LandingPage [shape: rounded, label: "Landing Page\n(Tamu Hanya Bisa Lihat Info Umum & Promosi)"]
ClickCTA [shape: box, label: "User Mengklik Salah Satu Menu Utama:\nCari Cafe / Forum / Gathering"]

// Autentikasi & Input Kredensial
CheckAuth [shape: diamond, label: "Sudah Login?"]
LoginPage [shape: rounded, icon: log-in, label: "Halaman Login / Register"]
InputCredentials [shape: box, label: "User Memasukkan Email/Username & Password"]
UserDashboard [shape: rounded, icon: grid, label: "Dashboard Utama\nAkses Semua Fitur Terbuka"]

// Cafe Feature Flow
SearchAction [shape: box, label: "Input Nama Kafe / Pilih Kecamatan"]
FilterOption [shape: diamond, label: "Gunakan Filter?"]
ApplyFilter [shape: box, label: "Filter: Rentang Harga & Rating"]
CafeList [shape: rounded, label: "Halaman Daftar Coffee Shops Sidoarjo"]
CafeDetail [shape: rounded, label: "Halaman Detail Coffee Shop & Lokasi Map"]
SubmitReview [shape: box, icon: star, label: "Menulis Review & Beri Rating Bintang"]

// Community Feature Flow
ComunityMenu [shape: rounded, icon: message-square, label: "Halaman Forum Komunitas"]
AddPost [shape: box, label: "Buat Post Diskusi Baru / Balas Diskusi"]

// Gathering Feature Flow
GatheringMenu [shape: rounded, icon: users, label: "Halaman Gathering / Kopdar"]
SubmitGathering [shape: box, label: "Ajukan Gathering Baru / Konfirmasi Kehadiran"]

End [shape: oval, icon: log-out, label: "Logout / Keluar"]

// Connections & Flows

Start -> LandingPage
LandingPage -> ClickCTA
ClickCTA -> CheckAuth

// Alur Belum Login -> Ke Halaman Login -> Input Kredensial -> Masuk Dashboard
CheckAuth -> LoginPage [label: "Belum"]
LoginPage -> InputCredentials
InputCredentials -> UserDashboard [label: "Verifikasi Sukses"]

// Alur Sudah Login -> Langsung Ke Dashboard
CheckAuth -> UserDashboard [label: "Sudah"]

// Dashboard Redirections
UserDashboard -> SearchAction [label: "Menu: Cari Cafe"]
UserDashboard -> ComunityMenu [label: "Menu: Forum"]
UserDashboard -> GatheringMenu [label: "Menu: Gathering"]
UserDashboard -> End [label: "Ingin Keluar"]

// Cafe Path
SearchAction -> FilterOption
FilterOption -> ApplyFilter [label: "Ya"]
FilterOption -> CafeList [label: "Tidak"]
ApplyFilter -> CafeList
CafeList -> CafeDetail
CafeDetail -> SubmitReview
SubmitReview -> CafeDetail
CafeDetail -> End

// Community Path
ComunityMenu -> AddPost
AddPost -> ComunityMenu
ComunityMenu -> End

// Gathering Path
GatheringMenu -> SubmitGathering
SubmitGathering -> GatheringMenu
GatheringMenu -> End
```
