# Quick Start - CRITICAL Features

## 🚀 Setup & Migration

### 1. Run Migrations
```bash
cd c:\xampp\htdocs\dailycoffee

# Run semua migration baru
php artisan migrate

# Output yang diharapkan:
# Migrated: 2026_05_27_000001_add_location_and_status_to_coffee_shops
# Migrated: 2026_05_27_000002_add_photo_to_komunitas
# Migrated: 2026_05_27_000003_create_password_reset_tokens_table
# Migrated: 2026_05_27_000004_add_soft_deletes_to_users
```

### 2. Setup Storage Symlink (untuk file uploads)
```bash
php artisan storage:link

# Create: public/storage -> storage/app/public
# Jika sudah ada, akan muncul warning (normal)
```

### 3. Database Check
```bash
# Verify columns sudah ada
php artisan db:table coffee_shops

# Pastikan ada: latitude, longitude, photo_url, is_verified, is_active, deleted_at
```

---

## 🧪 Testing Features

### Test 1: Location & Photo
```bash
# Buka tinker console
php artisan tinker

# Create coffee shop dengan location
>>> $shop = \App\Models\CoffeeShop::create([
    'nama' => 'Test Kopi Surabaya',
    'daerah' => 'Surabaya',
    'kecamatan' => 'Rungkut',
    'alamat' => 'Jl. Test No 123',
    'jam_buka' => '07:00',
    'jam_tutup' => '22:00',
    'harga_min' => 15000,
    'harga_max' => 50000,
    'rating' => 4.5,
    'deskripsi' => 'Kopi enak dan tempat nyaman',
    'latitude' => -7.2505,
    'longitude' => 112.7508,
    'photo_url' => 'https://example.com/photo.jpg',
    'is_verified' => true,
    'is_active' => true,
]);

# Verify
>>> $shop = \App\Models\CoffeeShop::first();
>>> echo "Lat: {$shop->latitude}, Long: {$shop->longitude}";

# Soft delete test
>>> $shop->delete();
>>> \App\Models\CoffeeShop::count(); # Will not include deleted
>>> \App\Models\CoffeeShop::withTrashed()->count(); # Includes deleted
>>> $shop->restore();
```

### Test 2: File Upload API
```bash
# Using curl atau Postman
POST /api/coffee-shops

Body (form-data):
- nama: "Kopi Baru"
- daerah: "Sidoarjo"
- kecamatan: "Waru"
- alamat: "Jl. Baru"
- jam_buka: "06:00"
- jam_tutup: "23:00"
- harga_min: 10000
- harga_max: 40000
- deskripsi: "Tempat kopi terbaru"
- latitude: -7.45
- longitude: 112.75
- photo: [upload file]
```

### Test 3: API Search & Filter
```bash
# Basic search
GET http://localhost:8000/api/coffee-shops?search=kopi

# Filter by kecamatan dan price
GET http://localhost:8000/api/coffee-shops?kecamatan_id=1&price_min=20000&price_max=100000

# Filter by rating minimum
GET http://localhost:8000/api/coffee-shops?min_rating=3.5

# Sorting & Pagination
GET http://localhost:8000/api/coffee-shops?sort_by=rating&sort_order=desc&per_page=10&page=1

# Kombinasi semua
GET "http://localhost:8000/api/coffee-shops?search=kopi&kecamatan_id=1&min_rating=3&is_verified=1&sort_by=rating&sort_order=desc&per_page=5"

# Response format:
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 25,
    "per_page": 5,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 5
  }
}
```

### Test 4: Password Reset Flow
```bash
# 1. Buka browser: http://localhost:8000/forgot-password
# 2. Submit email: test@example.com
# 3. Check database: SELECT * FROM password_reset_tokens;
# 4. Copy token dari database
# 5. Klik reset link atau manual: /reset-password/{token}
# 6. Submit form dengan password baru
# 7. Login dengan password baru
# 8. Token otomatis didelete dari database
```

---

## 📝 Code Examples

### Using FileUploadTrait in Controller
```php
<?php
namespace App\Http\Controllers\Admin;

use App\Traits\FileUploadTrait;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;

class CoffeeShopController extends Controller
{
    use FileUploadTrait;

    public function store(Request $request)
    {
        // Upload foto jika ada
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $this->uploadFile(
                $request->file('photo'),
                'coffee-shops'
            );
        }

        $shop = CoffeeShop::create([
            'nama' => $request->nama,
            'photo_url' => $photoUrl,
            // ... field lainnya
        ]);

        return response()->json(['data' => $shop]);
    }

    public function update(Request $request, CoffeeShop $shop)
    {
        // Update foto
        if ($request->hasFile('photo')) {
            $photoUrl = $this->updateFile(
                $request->file('photo'),
                $shop->photo_url, // old file
                'coffee-shops'
            );
            $shop->photo_url = $photoUrl;
        }

        $shop->update($request->only(['nama', 'deskripsi', /* ... */]));
        
        return response()->json(['data' => $shop]);
    }
}
```

### Using SearchableFilterable in API
```php
<?php
namespace App\Http\Controllers\Api;

use App\Traits\SearchableFilterable;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;

class CoffeeShopController extends Controller
{
    use SearchableFilterable;

    protected function getSortableFields(): array
    {
        return ['id', 'nama', 'rating', 'harga_min', 'harga_max', 'created_at'];
    }

    public function index(Request $request)
    {
        $query = CoffeeShop::query();

        // Apply search
        if ($request->search) {
            $query = $this->applySearch($query, $request->search, [
                'nama', 'daerah', 'deskripsi'
            ]);
        }

        // Apply filters
        $filters = [];
        if ($request->kecamatan_id) {
            $filters['kecamatan_id'] = $request->kecamatan_id;
        }
        if ($request->has('is_verified')) {
            $filters['is_verified'] = $request->is_verified;
        }
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }

        // Apply sorting & pagination
        $query = $this->applySorting($query, $request->sort_by, $request->sort_order);
        $perPage = $request->per_page ?? 15;
        $results = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'pagination' => [...]
        ]);
    }
}
```

---

## 🔧 Troubleshooting

### Migrations tidak berjalan
```bash
# Cek status
php artisan migrate:status

# Force run
php artisan migrate --force

# Reset semua (WARNING: hapus semua data)
php artisan migrate:refresh
```

### File upload tidak bekerja
```bash
# Check storage permissions
ls -la storage/
chmod -R 775 storage/
chmod -R 775 public/storage/

# Verify symlink
ls -la public/storage
```

### Password reset token tidak valid
```bash
# Check database
php artisan tinker
>>> \DB::table('password_reset_tokens')->get();

# Clear old tokens manually
>>> \DB::table('password_reset_tokens')->where('created_at', '<', now()->subHours(24))->delete();
```

### API search tidak mengembalikan hasil
```bash
# Cek query parameter format
GET /api/coffee-shops?search=test%20keyword
# (space harus di-encode sebagai %20)

# Debug dengan logging
php artisan tinker
>>> \App\Models\CoffeeShop::where('nama', 'like', '%test%')->get();
```

---

## 📚 File References

- Migrations: `database/migrations/2026_05_27_*`
- Models: `app/Models/` (CoffeeShop, User, Komunitas updated)
- Traits: 
  - `app/Traits/FileUploadTrait.php` 
  - `app/Traits/SearchableFilterable.php`
- Controllers:
  - `app/Http/Controllers/PasswordResetController.php`
  - `app/Http/Controllers/Api/CoffeeShopController.php`
- Routes: `routes/web.php` (password reset routes added)

---

## ✅ Checklist - Before Going to Production

- [ ] Run `php artisan migrate` successfully
- [ ] Setup storage symlink dengan `php artisan storage:link`
- [ ] Test file upload dengan real file
- [ ] Konfigurasi SMTP untuk email notification
- [ ] Test password reset flow end-to-end
- [ ] Verify soft delete query pada reporting
- [ ] Load test API search dengan large dataset
- [ ] Setup monitoring untuk file storage usage
- [ ] Backup database sebelum migrate

---

## 📞 Support

Untuk pertanyaan atau issues:
1. Cek dokumentasi lengkap: `CRITICAL_FEATURES_IMPLEMENTATION.md`
2. Review test cases di atas
3. Check database schema: `php artisan db:table`
