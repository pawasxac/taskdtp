# 🔄 System Flowchart - Daily Coffee Sidoarjo

Dokumen ini berisi visualisasi alur sistem (flowchart) lengkap untuk **Daily Coffee Sidoarjo**. Semua alur terhubung secara utuh dari awal kunjungan hingga aksi akhir tanpa ada garis yang terputus.

## 🎨 Diagram Flowchart Sistem (Mermaid)

```mermaid
graph TD
    %% Styling
    classDef startEnd fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef process fill:#334155,stroke:#475569,stroke-width:1px,color:#cbd5e1;
    classDef decision fill:#0f172a,stroke:#3b82f6,stroke-width:2px,color:#38bdf8;
    classDef database fill:#1e3a8a,stroke:#3b82f6,stroke-width:1.5px,color:#dbeafe;

    %% NODES DEFINITIONS
    Start(["Mulai: Kunjungan Landing Page"]):::startEnd
    
    %% Guest Actions
    SearchPublic["Cari & Filter Coffee Shop (Publik)"]:::process
    ChooseAction{"Pilih Aksi Autentikasi"}:::decision
    
    %% Auth Flows
    RegForm["Isi Form Registrasi"]:::process
    SaveUser[("Simpan User Baru (Unverified)")]:::database
    LoginForm["Form Login (Username & Password)"]:::process
    VerifyAuth{"Verifikasi Kredensial & Role"}:::decision
    ForgotPass["Lupa Password?"]:::process
    SendEmail["Kirim Link Reset Via Email"]:::process
    ResetForm["Input Password Baru"]:::process
    UpdatePass[("Update Password di DB")]:::database
    
    %% Roles Redirection
    RoleCheck{"Cek Role Akun"}:::decision
    
    %% User Dashboard Flows
    UserDashboard["Halaman Dashboard User"]:::process
    UserAction{"Pilih Menu Aktivitas"}:::decision
    
    %% Profile Update Flow
    ProfileUpdate["Update Nama, Bio, Medsos"]:::process
    UploadPic{"Ada Upload Foto?"}:::decision
    TraitUpload["Proses FileUploadTrait (Simpan ke Storage & DB)"]:::process
    SaveProfile[("Simpan Perubahan Profil")]:::database
    
    %% Community Flow
    CommFeed["Jelajahi Feed Komunitas"]:::process
    JoinComm{"Gabung Komunitas?"}:::decision
    CreatePost["Kirim Postingan / Diskusi Baru"]:::process
    AddComment["Tulis Komentar di Postingan"]:::process
    SaveComm[("Simpan Post/Komentar/Member Baru")]:::database
    
    %% Cafe Review Flow
    CafeReview["Lihat Cafe & Tulis Ulasan (Rating & Review)"]:::process
    SaveReview[("Simpan Review ke DB")]:::database
    
    %% Gathering Request Flow
    GathRequest["Buat Pengajuan Gathering (Cafe + Komunitas)"]:::process
    SaveGath[("Simpan Status Request (Pending)")]:::database
    
    %% Direct Message Flow
    DM_Inbox["Pilih User & Buka Room Chat"]:::process
    CheckLimit{"Jumlah Pesan < 10?"}:::decision
    SendDM["Kirim Pesan Chat (Direct Message)"]:::process
    DM_LimitMsg["Limit Tercapai! Arahkan ke Medsos (WA/IG/Discord)"]:::process
    SaveDM[("Simpan Pesan ke DB")]:::database
    
    %% Admin Flows
    AdminGateway["Admin Gateway Dashboard"]:::process
    AdminAction{"Pilih Menu Manajemen"}:::decision
    
    %% Admin Cafe CRUD
    AdminCafeCRUD["Kelola Coffee Shop (Create, Read, Update, Delete)"]:::process
    UploadCafePhoto{"Upload Foto Cafe?"}:::decision
    SaveCafe[("Simpan Data & Koordinat GPS Cafe")]:::database
    
    %% Admin User CRUD
    AdminUserCRUD["Kelola Akun User & Soft Deletes"]:::process
    SaveUserAdmin[("Update Status/Soft Delete User")]:::database
    
    %% Admin Gathering Approval
    AdminGathRequest["Evaluasi Pengajuan Gathering (Pending)"]:::process
    ApproveReject{"Keputusan Admin?"}:::decision
    StatusApprove["Update Status: Approved"]:::process
    StatusReject["Update Status: Rejected"]:::process
    SaveGathStatus[("Update Status Request di DB")]:::database
    
    %% Logout Flow
    Logout["Aksi Logout"]:::process
    ClearSession["Hapus Session & Cookie"]:::process
    End(["Selesai: Kembali ke Landing Page"]):::startEnd

    %% CONNECTIONS (Semua terhubung, tidak ada yang terputus)
    Start --> SearchPublic
    SearchPublic --> ChooseAction
    
    ChooseAction -->|Registrasi| RegForm
    ChooseAction -->|Login| LoginForm
    ChooseAction -->|Lupa Password| ForgotPass
    
    ForgotPass --> SendEmail
    SendEmail --> ResetForm
    ResetForm --> UpdatePass
    UpdatePass --> LoginForm
    
    RegForm --> SaveUser
    SaveUser --> LoginForm
    
    LoginForm --> VerifyAuth
    VerifyAuth -->|Gagal / Salah Kredensial| LoginForm
    VerifyAuth -->|Sukses| RoleCheck
    
    RoleCheck -->|Role: user| UserDashboard
    RoleCheck -->|Role: admin| AdminGateway
    
    %% User Dashboard Flows
    UserDashboard --> UserAction
    
    UserAction -->|Update Profil| ProfileUpdate
    UserAction -->|Komunitas| CommFeed
    UserAction -->|Ulas Cafe| CafeReview
    UserAction -->|Gathering| GathRequest
    UserAction -->|Pesan Langsung| DM_Inbox
    UserAction -->|Keluar| Logout
    
    %% Profile Update Loop
    ProfileUpdate --> UploadPic
    UploadPic -->|Ya| TraitUpload
    UploadPic -->|Tidak| SaveProfile
    TraitUpload --> SaveProfile
    SaveProfile --> UserDashboard
    
    %% Community Flow Loop
    CommFeed --> JoinComm
    JoinComm -->|Ya| SaveComm
    JoinComm -->|Tidak| CreatePost
    CreatePost --> AddComment
    AddComment --> SaveComm
    SaveComm --> UserDashboard
    
    %% Review Flow Loop
    CafeReview --> SaveReview
    SaveReview --> UserDashboard
    
    %% Gathering Flow Loop
    GathRequest --> SaveGath
    SaveGath --> UserDashboard
    
    %% DM Flow Loop
    DM_Inbox --> CheckLimit
    CheckLimit -->|Ya| SendDM
    CheckLimit -->|Tidak| DM_LimitMsg
    SendDM --> SaveDM
    SaveDM --> UserDashboard
    DM_LimitMsg --> UserDashboard
    
    %% Admin Flows
    AdminGateway --> AdminAction
    
    AdminAction -->|Manajemen Cafe| AdminCafeCRUD
    AdminAction -->|Manajemen User| AdminUserCRUD
    AdminAction -->|Persetujuan Gathering| AdminGathRequest
    AdminAction -->|Keluar| Logout
    
    %% Admin Cafe Loop
    AdminCafeCRUD --> UploadCafePhoto
    UploadCafePhoto -->|Ya| TraitUpload
    UploadCafePhoto -->|Tidak| SaveCafe
    SaveCafe --> AdminGateway
    
    %% Admin User Loop
    AdminUserCRUD --> SaveUserAdmin
    SaveUserAdmin --> AdminGateway
    
    %% Admin Gathering Approval Loop
    AdminGathRequest --> ApproveReject
    ApproveReject -->|Setujui| StatusApprove
    ApproveReject -->|Tolak| StatusReject
    StatusApprove --> SaveGathStatus
    StatusReject --> SaveGathStatus
    SaveGathStatus --> AdminGateway
    
    %% Logout to End
    Logout --> ClearSession
    ClearSession --> End
```

---

## 📝 Penjelasan Detail Alur

1. **Alur Autentikasi & Registrasi**:
   - Pengunjung umum (*guest*) dapat mencari cafe tanpa login. Jika memilih login/registrasi, sistem akan memvalidasi data dan mengarahkan ke dashboard yang sesuai berdasarkan **Role** (`user` atau `admin`).
   - Fitur **Lupa Password** menyediakan pengiriman token reset via email untuk memperbarui kata sandi secara aman.

2. **Aktivitas User Dashboard**:
   - **Update Profil**: Menggunakan `FileUploadTrait` untuk pemrosesan foto profil baru, lalu menyimpan data medsos terupdate.
   - **Komunitas & Diskusi**: Pengguna dapat menjelajahi feed komunitas, bergabung menjadi member, memosting thread diskusi baru, dan mengomentari postingan anggota lain.
   - **Ulasan Cafe**: Pengguna dapat melihat daftar cafe terdekat dan memberikan rating serta komentar ulasan.
   - **Direct Messages**: Chat personal antar pengguna dibatasi **maksimal 10 pesan**. Jika terlampaui, user akan diarahkan untuk melanjutkan komunikasi lewat kontak medsos yang tertera di profil.
   - **Gathering Request**: Pengguna dapat mengajukan kegiatan kumpul komunitas di kedai kopi tertentu. Pengajuan berstatus *pending* akan diverifikasi oleh Admin.

3. **Aktivitas Admin Panel**:
   - Admin berwenang mengelola data Coffee Shop (termasuk upload gambar, input titik koordinat GPS), mengelola akun pengguna (dengan fitur *soft delete*), serta memberikan persetujuan (*Approve*) atau penolakan (*Reject*) terhadap pengajuan gathering dari komunitas.

4. **Sesi Logout**:
   - Baik pengguna maupun admin dapat keluar dari sesi secara bersih, yang akan menghapus session cookie aktif dan mengembalikan ke Landing Page publik.
