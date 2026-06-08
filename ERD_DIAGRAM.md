# 📊 Entity Relationship Diagram (ERD) - Daily Coffee

Dokumen ini berisi visualisasi dan spesifikasi hubungan antar-tabel (*Entity Relationship Diagram*) untuk sistem database **Daily Coffee Sidoarjo** menggunakan format Mermaid.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string username UK
        string password
        string role "admin | user"
        timestamp email_verified_at
        timestamp deleted_at "Soft Deletes"
        timestamp created_at
        timestamp updated_at
    }

    KECAMATANS {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    COFFEE_SHOPS {
        bigint id PK
        string nama
        string daerah
        string kecamatan "Text backup"
        bigint kecamatan_id FK
        text alamat
        string jam_buka
        string jam_tutup
        integer harga_min
        integer harga_max
        decimal rating
        text deskripsi
        decimal latitude
        decimal longitude
        string photo_url
        boolean is_verified
        boolean is_active
        timestamp deleted_at "Soft Deletes"
        timestamp created_at
        timestamp updated_at
    }

    COFFEE_SHOP_REVIEWS {
        bigint id PK
        bigint coffee_shop_id FK
        bigint user_id FK
        integer rating
        text ulasan
        timestamp created_at
        timestamp updated_at
    }

    KOMUNITAS {
        bigint id PK
        string nama_komunitas
        string domisili
        string ketua
        text deskripsi
        date tanggal_dibentuk
        integer jumlah_anggota
        string kontak
        string status "aktif | nonaktif"
        string photo_url
        timestamp deleted_at "Soft Deletes"
        timestamp created_at
        timestamp updated_at
    }

    COMMUNITY_MEMBERS {
        bigint id PK
        bigint komunitas_id FK
        bigint user_id FK
        string role "member | admin_komunitas"
        timestamp joined_at
        timestamp created_at
        timestamp updated_at
    }

    COMMUNITY_POSTS {
        bigint id PK
        bigint komunitas_id FK
        bigint user_id FK
        string title
        text content
        string photo_url
        timestamp created_at
        timestamp updated_at
    }

    COMMUNITY_COMMENTS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        text comment
        timestamp created_at
        timestamp updated_at
    }

    GATHERING_REQUESTS {
        bigint id PK
        bigint komunitas_id FK
        bigint coffee_shop_id FK
        bigint user_id FK
        string nama_kegiatan
        text deskripsi
        date tanggal_kegiatan
        time waktu_kegiatan
        string status "pending | approved | rejected"
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    KECAMATANS ||--o{ COFFEE_SHOPS : "has many"
    USERS ||--o{ COFFEE_SHOP_REVIEWS : "writes"
    COFFEE_SHOPS ||--o{ COFFEE_SHOP_REVIEWS : "receives"
    
    USERS ||--o{ COMMUNITY_MEMBERS : "joins"
    KOMUNITAS ||--o{ COMMUNITY_MEMBERS : "has"
    
    USERS ||--o{ COMMUNITY_POSTS : "creates"
    KOMUNITAS ||--o{ COMMUNITY_POSTS : "contains"
    
    COMMUNITY_POSTS ||--o{ COMMUNITY_COMMENTS : "has"
    USERS ||--o{ COMMUNITY_COMMENTS : "writes"
    
    KOMUNITAS ||--o{ GATHERING_REQUESTS : "proposes"
    COFFEE_SHOPS ||--o{ GATHERING_REQUESTS : "hosts"
    USERS ||--o{ GATHERING_REQUESTS : "creates"
```

---

## 📝 Penjelasan Hubungan Relasi (Relationships)

1.  **Kecamatan & Coffee Shop (`One-to-Many`)**
    *   Satu `KECAMATAN` dapat menampung banyak `COFFEE_SHOP`.
    *   Kunci Relasi: `kecamtan_id` di tabel `coffee_shops`.

2.  **User & Coffee Shop Reviews (`Many-to-Many via Review`)**
    *   Satu `USER` dapat menulis banyak `COFFEE_SHOP_REVIEWS`.
    *   Satu `COFFEE_SHOP` dapat menerima banyak `COFFEE_SHOP_REVIEWS` dari pengguna yang berbeda.

3.  **User & Komunitas (`Many-to-Many via Member`)**
    *   Satu `USER` dapat bergabung ke banyak `KOMUNITAS` sebagai anggota (`COMMUNITY_MEMBERS`).
    *   Satu `KOMUNITAS` dapat diisi oleh banyak anggota (`COMMUNITY_MEMBERS`).

4.  **Komunitas, User & Post/Comments (`Hierarchical Relations`)**
    *   User memposting thread (`COMMUNITY_POSTS`) di dalam suatu `KOMUNITAS`.
    *   Postingan tersebut dapat dikomentari (`COMMUNITY_COMMENTS`) oleh user lain.

5.  **Gathering Requests (`Three-Way Relation`)**
    *   Pengajuan kumpul bareng/kegiatan diajukan oleh seorang `USER` yang mewakili `KOMUNITAS` tertentu, dengan memilih lokasi di salah satu `COFFEE_SHOP`.
