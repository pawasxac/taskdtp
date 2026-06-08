# Dokumentasi Critical Features Implementation

## 1. Location & Status Features (CRITICAL #1)

### Database Changes
- **Migration**: `2026_05_27_000001_add_location_and_status_to_coffee_shops.php`
  - Tambah `latitude` (DECIMAL 10,8) - untuk koordinat lintang
  - Tambah `longitude` (DECIMAL 11,8) - untuk koordinat bujur
  - Tambah `photo_url` (VARCHAR) - untuk menyimpan URL foto coffee shop
  - Tambah `is_verified` (BOOLEAN) - untuk verifikasi admin
  - Tambah `is_active` (BOOLEAN) - untuk status operasional
  - Tambah soft deletes - untuk audit trail

### Model Updates
- **CoffeeShop**: Sudah ditambah `SoftDeletes` trait dan fillable fields baru
- **Komunitas**: Sudah ditambah `SoftDeletes` trait dan `photo_url` field
- **User**: Sudah ditambah `SoftDeletes` trait

### Usage Examples
```php
// Create coffee shop dengan lokasi
$shop = CoffeeShop::create([
    'nama' => 'Kopi Nusantara',
    'latitude' => -7.2505,
    'longitude' => 112.7508,
    'photo_url' => 'https://...',
    'is_verified' => true,
    'is_active' => true,
]);

// Query dengan soft deletes
$activeShops = CoffeeShop::where('is_active', true)->get();
$allIncludingDeleted = CoffeeShop::withTrashed()->get();
$onlyDeleted = CoffeeShop::onlyTrashed()->get();
```

---

## 2. File Upload System (CRITICAL #2)

### Implementation
- **Trait**: `App\Traits\FileUploadTrait`
- Methods: `uploadFile()`, `deleteFile()`, `updateFile()`

### Features
- Automatic filename generation (random 20 chars)
- Storage management (public/private disk)
- Error logging
- Asset URL generation

### Usage Example
```php
use App\Traits\FileUploadTrait;

class CoffeeShopController extends Controller
{
    use FileUploadTrait;
    
    public function update(Request $request, CoffeeShop $shop)
    {
        if ($request->hasFile('photo')) {
            $photoUrl = $this->uploadFile(
                $request->file('photo'),
                'coffee-shops',
                'public'
            );
            $shop->photo_url = $photoUrl;
        }
        
        $shop->save();
    }
}
```

### Configuration
Pastikan storage symbolic link sudah dibuat:
```bash
php artisan storage:link
```

---

## 3. Email Verification & Password Reset (CRITICAL #3)

### Database Changes
- **Migration**: `2026_05_27_000003_create_password_reset_tokens_table.php`
  - Email (primary key)
  - Token (hashed)
  - Created_at timestamp

### Controller
- **PasswordResetController**: Handle forgot password & reset
  - `showForgotForm()` - tampilkan form lupa password
  - `sendResetLink()` - generate dan kirim token
  - `showResetForm()` - tampilkan form reset password
  - `reset()` - process reset password

### Routes
```
GET /forgot-password → password.request
POST /forgot-password → password.email
GET /reset-password/{token} → password.reset
POST /reset-password → password.update
```

### Usage
```php
// Di form lupa password:
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" required>
    <button>Kirim Link Reset</button>
</form>

// Register sekarang auto-verify email
$user->update(['email_verified_at' => now()]);
```

### Token Expiration
- Token valid selama 24 jam
- Auto-delete setelah expired

---

## 4. API Search & Filter (CRITICAL #4)

### Implementation
- **Trait**: `App\Traits\SearchableFilterable`
- **Methods**: `applySearch()`, `applyFilters()`, `applySorting()`
- **Updated Controller**: `App\Http\Controllers\Api\CoffeeShopController`

### Query Parameters

#### Search
```
GET /api/coffee-shops?search=kopi
```
Mencari di: nama, daerah, kecamatan, alamat, deskripsi

#### Filters
```
GET /api/coffee-shops?kecamatan_id=1&is_verified=1&is_active=1
GET /api/coffee-shops?price_min=50000&price_max=200000
GET /api/coffee-shops?min_rating=3.5
```

#### Sorting
```
GET /api/coffee-shops?sort_by=nama&sort_order=asc
GET /api/coffee-shops?sort_by=rating&sort_order=desc
```

#### Pagination
```
GET /api/coffee-shops?per_page=10&page=2
```

### Complete Example
```
GET /api/coffee-shops?search=kopi&kecamatan_id=1&min_rating=3&sort_by=rating&sort_order=desc&per_page=15
```

Response:
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 45,
    "per_page": 15,
    "current_page": 1,
    "last_page": 3,
    "from": 1,
    "to": 15
  }
}
```

---

## Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Fresh migration (WARNING: drops all data)
php artisan migrate:fresh
```

---

## Testing Checklist

### Location Features
- [ ] Create coffee shop dengan latitude/longitude
- [ ] Query dengan filter `is_verified` dan `is_active`
- [ ] Test soft deletes (delete & restore)

### File Upload
- [ ] Upload foto coffee shop
- [ ] Verify file tersimpan di storage
- [ ] Update foto (old file didelete)
- [ ] Delete coffee shop (foto didelete)

### Password Reset
- [ ] Submit forgot password form
- [ ] Verify token tersimpan di database
- [ ] Click reset link
- [ ] Set password baru
- [ ] Login dengan password baru

### API Search & Filter
- [ ] Search dengan keyword
- [ ] Filter by kecamatan
- [ ] Filter by price range
- [ ] Filter by rating minimum
- [ ] Sort by different fields
- [ ] Test pagination
- [ ] Combine multiple filters

---

## Next Steps (HIGH Priority)

1. **Email Integration**: Setup SMTP untuk send reset links via email
2. **Admin Dashboard Filters**: Integrasikan SearchableFilterable ke admin views
3. **Bulk Upload**: Batch upload foto untuk multiple coffee shops
4. **Location-based Queries**: Hitung jarak menggunakan haversine formula
5. **Image Resizing**: Auto-resize foto ke berbagai ukuran

---

## Environment Setup

Untuk development, pastikan sudah di `.env`:
```
FILESYSTEM_DISK=public
```

Untuk production:
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
```
