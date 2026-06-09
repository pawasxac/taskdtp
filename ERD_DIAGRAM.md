# 📊 Entity Relationship Diagram (ERD) - Daily Coffee

Dokumen ini berisi visualisasi *Entity Relationship Diagram* (ERD) konseptual menggunakan Notasi Chen untuk sistem database **Daily Coffee Sidoarjo**.

## 🎨 Diagram ERD (Notasi Chen)

```mermaid
graph TD
    %% Styling
    classDef entity fill:#1e293b,stroke:#38bdf8,stroke-width:2px,color:#f8fafc;
    classDef relationship fill:#0f172a,stroke:#3b82f6,stroke-width:2px,color:#38bdf8;
    classDef attribute fill:#334155,stroke:#475569,stroke-width:1px,color:#cbd5e1;
    classDef pk fill:#334155,stroke:#f59e0b,stroke-width:2px,color:#fef08a;

    %% ENTITIES (Entitas - Persegi Panjang)
    User["User"]:::entity
    Kecamatan["Kecamatan"]:::entity
    CoffeeShop["Coffee Shop"]:::entity
    Komunitas["Komunitas"]:::entity
    Post["Community Post"]:::entity
    Comment["Community Comment"]:::entity
    Gathering["Gathering Request"]:::entity
    DM["Direct Message"]:::entity

    %% RELATIONSHIPS (Relasi - Belah Ketupat)
    R_Mempunyai{"Mempunyai"}:::relationship
    R_Mengulas{"Mengulas"}:::relationship
    R_Bergabung{"Bergabung"}:::relationship
    R_MembuatPost{"Membuat"}:::relationship
    R_MempunyaiPost{"Mempunyai"}:::relationship
    R_MenulisComment{"Menulis"}:::relationship
    R_MemilikiComment{"Memiliki"}:::relationship
    R_MengajukanGathering{"Mengajukan"}:::relationship
    R_MengadakanGathering{"Mengadakan"}:::relationship
    R_MenjadiTempat{"Menjadi Tempat"}:::relationship
    R_MengirimDM{"Mengirim"}:::relationship
    R_MenerimaDM{"Menerima"}:::relationship

    %% ATTRIBUTES (Atribut - Oval/Stadium)
    %% User Attributes
    U_id(["<u>id</u>"]):::pk
    U_name(["name"]):::attribute
    U_email(["email"]):::attribute
    U_username(["username"]):::attribute
    U_role(["role"]):::attribute
    U_ig(["instagram"]):::attribute
    U_wa(["whatsapp"]):::attribute
    U_dc(["discord"]):::attribute
    
    User --- U_id
    User --- U_name
    User --- U_email
    User --- U_username
    User --- U_role
    User --- U_ig
    User --- U_wa
    User --- U_dc

    %% Kecamatan Attributes
    K_id(["<u>id</u>"]):::pk
    K_name(["name"]):::attribute
    
    Kecamatan --- K_id
    Kecamatan --- K_name

    %% CoffeeShop Attributes
    CS_id(["<u>id</u>"]):::pk
    CS_nama(["nama"]):::attribute
    CS_daerah(["daerah"]):::attribute
    CS_rating(["rating"]):::attribute
    CS_verified(["is_verified"]):::attribute
    
    CoffeeShop --- CS_id
    CoffeeShop --- CS_nama
    CoffeeShop --- CS_daerah
    CoffeeShop --- CS_rating
    CoffeeShop --- CS_verified

    %% Komunitas Attributes
    Ko_id(["<u>id</u>"]):::pk
    Ko_nama(["nama_komunitas"]):::attribute
    Ko_domisili(["domisili"]):::attribute
    Ko_status(["status"]):::attribute
    
    Komunitas --- Ko_id
    Komunitas --- Ko_nama
    Komunitas --- Ko_domisili
    Komunitas --- Ko_status

    %% Post Attributes
    P_id(["<u>id</u>"]):::pk
    P_title(["title"]):::attribute
    P_content(["content"]):::attribute
    
    Post --- P_id
    Post --- P_title
    Post --- P_content

    %% Comment Attributes
    C_id(["<u>id</u>"]):::pk
    C_comment(["comment"]):::attribute
    
    Comment --- C_id
    Comment --- C_comment

    %% Gathering Attributes
    G_id(["<u>id</u>"]):::pk
    G_nama(["nama_kegiatan"]):::attribute
    G_tanggal(["tanggal_kegiatan"]):::attribute
    G_status(["status"]):::attribute
    
    Gathering --- G_id
    Gathering --- G_nama
    Gathering --- G_tanggal
    Gathering --- G_status

    %% DM Attributes
    D_id(["<u>id</u>"]):::pk
    D_message(["message"]):::attribute
    D_read(["read_at"]):::attribute
    
    DM --- D_id
    DM --- D_message
    DM --- D_read

    %% RELATIONSHIP ATTRIBUTES (Atribut Relasi Many-to-Many)
    %% Mengulas Attributes
    R_Rev_rating(["rating"]):::attribute
    R_Rev_ulasan(["ulasan"]):::attribute
    R_Mengulas --- R_Rev_rating
    R_Mengulas --- R_Rev_ulasan

    %% Bergabung Attributes
    R_Mem_role(["role"]):::attribute
    R_Mem_joined(["joined_at"]):::attribute
    R_Bergabung --- R_Mem_role
    R_Bergabung --- R_Mem_joined

    %% CONNECTIONS & CARDINALITY (Hubungan & Kardinalitas)
    Kecamatan --- |1| R_Mempunyai
    R_Mempunyai --- |N| CoffeeShop

    User --- |1| R_Mengulas
    R_Mengulas --- |N| CoffeeShop

    User --- |M| R_Bergabung
    R_Bergabung --- |N| Komunitas

    User --- |1| R_MembuatPost
    R_MembuatPost --- |N| Post

    Komunitas --- |1| R_MempunyaiPost
    R_MempunyaiPost --- |N| Post

    User --- |1| R_MenulisComment
    R_MenulisComment --- |N| Comment

    Post --- |1| R_MemilikiComment
    R_MemilikiComment --- |N| Comment

    User --- |1| R_MengajukanGathering
    R_MengajukanGathering --- |N| Gathering

    Komunitas --- |1| R_MengadakanGathering
    R_MengadakanGathering --- |N| Gathering

    CoffeeShop --- |1| R_MenjadiTempat
    R_MenjadiTempat --- |N| Gathering

    User --- |1 (Sender)| R_MengirimDM
    R_MengirimDM --- |N| DM

    User --- |1 (Receiver)| R_MenerimaDM
    R_MenerimaDM --- |N| DM
```

---

## 📝 Deskripsi Entitas & Relasi

1. **User - Coffee Shop (Mengulas)**:
   - Relasi *Many-to-Many* (`1:N` dari sisi User dan Coffee Shop ke Relasi) di mana pengguna memberikan ulasan pada kedai kopi. Atribut relasi meliputi `rating` dan `ulasan`.

2. **Kecamatan - Coffee Shop (Mempunyai)**:
   - Relasi *One-to-Many* (`1:N`) di mana setiap Kecamatan dapat memiliki beberapa Coffee Shop, tetapi satu Coffee Shop hanya terdaftar di satu Kecamatan.

3. **User - Komunitas (Bergabung)**:
   - Relasi *Many-to-Many* (`M:N`) yang mencatat keanggotaan pengguna di dalam komunitas. Memiliki atribut relasi berupa `role` (misalnya: *member* atau *admin_komunitas*) dan `joined_at`.

4. **Community Post & Comments**:
   - `User` membuat postingan (`Community Post`) yang berada di bawah naungan `Komunitas` tertentu.
   - `User` juga dapat menulis komentar (`Community Comment`) pada postingan tersebut.

5. **Gathering Requests**:
   - Relasi berseri di mana `User` mengajukan kegiatan berkumpul atas nama `Komunitas` dengan lokasi di salah satu `Coffee Shop`.

6. **Direct Messages**:
   - Menghubungkan sesama `User` sebagai Pengirim (*Sender*) dan Penerima (*Receiver*) pesan langsung (DM). Atribut pesan meliputi isi `message` dan waktu dibaca `read_at`.

---

## 💾 Kode Eraser.io (Diagram-as-Code)

Anda dapat menyalin kode di bawah ini langsung ke **[Eraser.io](https://www.eraser.io/)** untuk menghasilkan diagram ERD interaktif:

```dbml
// Eraser.io - Native ERD Diagram-as-Code Syntax

USERS [icon: user] {
  id bigint pk
  name string
  email string
  profile_picture string
  bio text
  phone_number string
  username string
  password string
  role string
  instagram string
  whatsapp string
  discord string
  email_verified_at timestamp
  deleted_at timestamp
  created_at timestamp
  updated_at timestamp
}

KECAMATANS [icon: map] {
  id bigint pk
  name string
  created_at timestamp
  updated_at timestamp
}

COFFEE_SHOPS [icon: coffee] {
  id bigint pk
  nama string
  daerah string
  kecamatan string
  kecamatan_id bigint
  alamat text
  jam_buka string
  jam_tutup string
  harga_min integer
  harga_max integer
  rating decimal
  deskripsi text
  latitude decimal
  longitude decimal
  photo_url string
  is_verified boolean
  is_active boolean
  deleted_at timestamp
  created_at timestamp
  updated_at timestamp
}

COFFEE_SHOP_REVIEWS {
  id bigint pk
  coffee_shop_id bigint
  user_id bigint
  rating integer
  ulasan text
  created_at timestamp
  updated_at timestamp
}

KOMUNITAS [icon: users] {
  id bigint pk
  nama_komunitas string
  domisili string
  ketua string
  deskripsi text
  tanggal_dibentuk date
  jumlah_anggota integer
  kontak string
  status string
  photo_url string
  deleted_at timestamp
  created_at timestamp
  updated_at timestamp
}

COMMUNITY_MEMBERS {
  id bigint pk
  komunitas_id bigint
  user_id bigint
  role string
  joined_at timestamp
  created_at timestamp
  updated_at timestamp
}

COMMUNITY_POSTS {
  id bigint pk
  komunitas_id bigint
  user_id bigint
  title string
  content text
  photo_url string
  created_at timestamp
  updated_at timestamp
}

COMMUNITY_COMMENTS {
  id bigint pk
  post_id bigint
  user_id bigint
  comment text
  created_at timestamp
  updated_at timestamp
}

GATHERING_REQUESTS {
  id bigint pk
  komunitas_id bigint
  coffee_shop_id bigint
  user_id bigint
  nama_kegiatan string
  deskripsi text
  tanggal_kegiatan date
  waktu_kegiatan time
  status string
  created_at timestamp
  updated_at timestamp
}

DIRECT_MESSAGES [icon: message-square] {
  id bigint pk
  sender_id bigint
  receiver_id bigint
  message text
  read_at timestamp
  created_at timestamp
  updated_at timestamp
}

// Relationships
KECAMATANS.id < COFFEE_SHOPS.kecamatan_id
USERS.id < COFFEE_SHOP_REVIEWS.user_id
COFFEE_SHOPS.id < COFFEE_SHOP_REVIEWS.coffee_shop_id

USERS.id < COMMUNITY_MEMBERS.user_id
KOMUNITAS.id < COMMUNITY_MEMBERS.komunitas_id

USERS.id < COMMUNITY_POSTS.user_id
KOMUNITAS.id < COMMUNITY_POSTS.komunitas_id

COMMUNITY_POSTS.id < COMMUNITY_COMMENTS.post_id
USERS.id < COMMUNITY_COMMENTS.user_id

KOMUNITAS.id < GATHERING_REQUESTS.komunitas_id
COFFEE_SHOPS.id < GATHERING_REQUESTS.coffee_shop_id
USERS.id < GATHERING_REQUESTS.user_id

USERS.id < DIRECT_MESSAGES.sender_id
USERS.id < DIRECT_MESSAGES.receiver_id
```


