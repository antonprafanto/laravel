# Bab 14: Seeder & Factory 🌱

[⬅️ Bab 13: Migration Dasar](13-migration-dasar.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 15: Model & Eloquent ➡️](15-model-eloquent.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu Seeder dan Factory
- ✅ Bisa membedakan kapan pakai Seeder vs Factory
- ✅ Bisa membuat data dummy untuk testing
- ✅ Menguasai command `php artisan db:seed`
- ✅ Bisa buat Factory untuk generate data dalam jumlah besar
- ✅ Paham workflow development dengan data dummy

---

## 🎯 Analogi Sederhana: Seeder & Factory

### Analogi Seeder = Isi Kulkas Display Toko

Bayangkan kamu punya toko elektronik yang jual kulkas. Sebelum buka toko, kamu harus **isi kulkas display** dengan makanan/minuman palsu supaya terlihat menarik untuk customer.

**Seeder = Mengisi data awal** ke database supaya aplikasi terlihat "hidup" saat development/demo.

```
🏪 Toko Elektronik (Aplikasi)
   ├── 🧊 Kulkas Display 1 (Table users)
   │   └── Diisi: Susu, Telur, Sayur (Data dummy users)
   ├── 🧊 Kulkas Display 2 (Table posts)
   │   └── Diisi: Jus, Buah, Daging (Data dummy posts)
   └── 🧊 Kulkas Display 3 (Table categories)
       └── Diisi: Roti, Keju, Butter (Data dummy categories)
```

**Karakteristik Seeder:**
- Data yang **spesifik** dan **terkontrol**
- Biasanya sedikit (5-10 data saja)
- Untuk data master atau data awal aplikasi
- Contoh: Admin user, kategori default, role/permission

---

### Analogi Factory = Pabrik Mainan

Sekarang bayangkan kamu punya **pabrik mainan**. Kamu bisa buat mainan dalam jumlah banyak dengan cetakan yang sama, tapi setiap mainan punya warna/detail berbeda.

**Factory = Cetakan untuk generate data dummy** dalam jumlah besar dengan variasi random.

```
🏭 Pabrik Mainan (Factory)
   ├── 📐 Cetakan Robot (User Factory)
   │   ├── Robot 1: Nama "Budi", Email "budi@mail.com"
   │   ├── Robot 2: Nama "Ani", Email "ani@mail.com"
   │   └── ... (bisa 100-1000 robot dengan nama random)
   ├── 📐 Cetakan Mobil (Post Factory)
   │   ├── Mobil 1: Judul "Tutorial Laravel", Body "..."
   │   ├── Mobil 2: Judul "Tips PHP", Body "..."
   │   └── ... (bisa 50-500 posts dengan judul random)
```

**Karakteristik Factory:**
- Data **random** dan **banyak**
- Untuk testing dengan dataset besar
- Pakai library Faker untuk generate data realistis
- Contoh: 100 users, 500 posts, 1000 products

---

### Kapan Pakai Seeder vs Factory?

| Skenario | Pakai Seeder | Pakai Factory |
|----------|--------------|---------------|
| Butuh 3 kategori default | ✅ | ❌ |
| Butuh 1 admin user | ✅ | ❌ |
| Butuh 100 users untuk testing | ❌ | ✅ |
| Butuh 500 posts dengan data random | ❌ | ✅ |
| Butuh role & permission default | ✅ | ❌ |
| Butuh data yang konsisten | ✅ | ❌ |
| Butuh data yang bervariasi | ❌ | ✅ |

**Tapi sering digabung!** → Seeder **memanggil** Factory untuk generate data banyak.

---

## 📚 Bagian 1: Pengenalan Seeder

### Apa itu Seeder?

**Seeder** = File PHP yang berisi logic untuk **insert data** ke database.

**Lokasi:** `database/seeders/`

**Fungsi:**
- Isi data awal aplikasi
- Data dummy untuk development
- Data master (kategori, role, settings)

---

### Membuat Seeder

**Command:**
```bash
php artisan make:seeder CategorySeeder
```

**Output:**
```
INFO  Seeder [database/seeders/CategorySeeder.php] created successfully.
```

**File yang dibuat:** `database/seeders/CategorySeeder.php`

---

### Struktur File Seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🌱 Logic untuk insert data di sini
    }
}
```

**Method `run()`** → Akan dijalankan saat kita execute seeder.

---

## 💡 Bagian 2: Contoh Seeder Sederhana

### Contoh 1: Seeder dengan DB::table()

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * 🌱 Isi kategori default untuk blog
     */
    public function run(): void
    {
        // 🧊 Seperti isi kulkas display dengan item-item tetap
        DB::table('categories')->insert([
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Artikel tentang teknologi dan programming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Artikel tentang gaya hidup dan hobi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bisnis',
                'slug' => 'bisnis',
                'description' => 'Artikel tentang bisnis dan keuangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
```

**Penjelasan:**
- `DB::table('categories')` → Akses tabel categories
- `insert([...])` → Insert array data
- `now()` → Helper Laravel untuk timestamp sekarang
- Data **spesifik** dan **terkontrol**

---

### Contoh 2: Seeder dengan Model (Eloquent)

**Lebih clean!** Pakai Model (nanti kita bahas detail di Bab 15).

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 🌱 Pakai Model - lebih clean!
        Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
            'description' => 'Artikel tentang teknologi dan programming',
        ]);

        Category::create([
            'name' => 'Lifestyle',
            'slug' => 'lifestyle',
            'description' => 'Artikel tentang gaya hidup dan hobi',
        ]);

        Category::create([
            'name' => 'Bisnis',
            'slug' => 'bisnis',
            'description' => 'Artikel tentang bisnis dan keuangan',
        ]);
    }
}
```

**Keuntungan pakai Model:**
- Tidak perlu tulis `created_at` dan `updated_at` (otomatis!)
- Lebih readable
- Bisa pakai event/observer (advanced)

---

### Contoh 3: Seeder dengan Loop

```php
<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // 🌱 Array data tags
        $tags = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js',
            'React', 'Docker', 'Git', 'MySQL',
            'Tailwind CSS', 'Alpine.js'
        ];

        // 🔄 Loop dan insert
        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => str()->slug($tagName), // 🔧 Helper Laravel untuk slugify
            ]);
        }
    }
}
```

**Helper `str()->slug()`** → Convert "Vue.js" jadi "vue-js"

---

## 🚀 Bagian 3: Menjalankan Seeder

### Cara 1: Run Seeder Tertentu

```bash
php artisan db:seed --class=CategorySeeder
```

**Penjelasan:**
- `--class=` → Nama seeder class yang mau dijalankan
- Hanya jalankan 1 seeder saja

---

### Cara 2: Run Semua Seeder via DatabaseSeeder

**File:** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🌱 Panggil seeder lain di sini
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            // UserSeeder::class, // Nanti kita buat
        ]);
    }
}
```

**Jalankan:**
```bash
php artisan db:seed
```

**Akan jalankan:**
1. CategorySeeder
2. TagSeeder
3. (Urutan sesuai array)

---

### Cara 3: Migrate Fresh + Seed (Reset Database)

```bash
# Drop semua table, migrate ulang, jalankan seeder
php artisan migrate:fresh --seed
```

**Gunakan saat:**
- Development
- Mau reset database dari awal
- Testing

⚠️ **Jangan pakai di production!** Data akan hilang semua!

---

## 🏭 Bagian 4: Pengenalan Factory

### Apa itu Factory?

**Factory** = Cetakan untuk **generate data dummy** dalam jumlah besar dengan data random.

**Lokasi:** `database/factories/`

**Library:** Menggunakan **Faker** (bawaan Laravel) untuk generate data realistis.

**Contoh data Faker:**
- Nama: "Budi Santoso", "Ani Wijaya"
- Email: "budi@example.com"
- Alamat: "Jl. Merdeka No. 123, Jakarta"
- Tanggal: Random date
- Text: Lorem ipsum paragraph

---

### Membuat Factory

**Command:**
```bash
php artisan make:factory PostFactory --model=Post
```

**Output:**
```
INFO  Factory [database/factories/PostFactory.php] created successfully.
```

**File yang dibuat:** `database/factories/PostFactory.php`

---

### Struktur File Factory

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 🏭 Return array dengan data random
        return [
            // 'column_name' => fake()->method()
        ];
    }
}
```

**Method `definition()`** → Return array dengan struktur data yang mau di-generate.

**`fake()`** → Helper untuk akses Faker library.

---

## 💡 Bagian 5: Contoh Factory

### Contoh 1: Factory untuk Post

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 🏭 Generate data random dengan Faker
            'title' => fake()->sentence(6), // "Tutorial Laravel Untuk Pemula Absolut"
            'slug' => fake()->slug(), // "tutorial-laravel-untuk-pemula"
            'body' => fake()->paragraphs(5, true), // 5 paragraf lorem ipsum
            'excerpt' => fake()->text(200), // Ringkasan 200 karakter
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'), // Random date tahun lalu
            'is_published' => fake()->boolean(80), // 80% true, 20% false
        ];
    }
}
```

**Penjelasan Faker:**
- `sentence(6)` → Kalimat dengan 6 kata
- `slug()` → URL-friendly string
- `paragraphs(5, true)` → 5 paragraf, return as string
- `text(200)` → Random text 200 karakter
- `dateTimeBetween('-1 year', 'now')` → Date antara 1 tahun lalu sampai sekarang
- `boolean(80)` → 80% kemungkinan true

---

### Contoh 2: Factory untuk User

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(), // "Budi Santoso"
            'email' => fake()->unique()->safeEmail(), // "budi.santoso@example.com"
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Default: "password"
            'remember_token' => Str::random(10),
        ];
    }
}
```

**Penjelasan:**
- `name()` → Nama lengkap random
- `unique()` → Pastikan email tidak duplicate
- `safeEmail()` → Email dengan domain @example.com (aman untuk testing)
- `Hash::make('password')` → Password di-hash (Laravel Bcrypt)

---

### Contoh 3: Factory untuk Product (E-commerce)

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true), // "Sepatu Running Nike"
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(4), // 4 paragraf
            'price' => fake()->randomFloat(2, 50000, 5000000), // Harga 50rb - 5jt
            'stock' => fake()->numberBetween(0, 100), // Stock 0-100
            'weight' => fake()->numberBetween(100, 5000), // Berat gram 100-5000
            'is_available' => fake()->boolean(90), // 90% available
            'rating' => fake()->randomFloat(1, 3.0, 5.0), // Rating 3.0 - 5.0
        ];
    }
}
```

**Faker Methods Berguna:**
- `words(3, true)` → 3 kata random, return as string
- `randomFloat(2, min, max)` → Float dengan 2 desimal
- `numberBetween(min, max)` → Integer random
- `paragraph(4)` → 4 paragraf text

---

## 🌱 Bagian 6: Menggunakan Factory

### Cara 1: Generate 1 Data dengan Tinker

```bash
php artisan tinker
```

```php
>>> use App\Models\Post;
>>> Post::factory()->create();
```

**Output:** 1 post baru ter-create dengan data random!

---

### Cara 2: Generate Banyak Data

```php
>>> Post::factory()->count(10)->create();
```

**Output:** 10 posts ter-create!

---

### Cara 3: Generate dengan Attribute Custom

```php
>>> Post::factory()->create([
...     'title' => 'Tutorial Laravel',
...     'is_published' => true,
... ]);
```

**Output:** 1 post dengan title "Tutorial Laravel" dan sisanya random.

---

### Cara 4: Factory di Seeder

**File:** `database/seeders/PostSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // 🏭 Generate 50 posts dengan Factory
        Post::factory()->count(50)->create();
    }
}
```

**Jalankan:**
```bash
php artisan db:seed --class=PostSeeder
```

**Hasil:** 50 posts dengan data random masuk ke database!

---

## 🔄 Bagian 7: Seeder + Factory (Kombinasi)

### Contoh Real-World: Blog Seeder Lengkap

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🌱 SEEDER: Buat kategori spesifik (data master)
        $categories = [
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
            ['name' => 'Bisnis', 'slug' => 'bisnis'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // 🌱 SEEDER: Buat 1 admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@blog.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // 🏭 FACTORY: Buat 20 users random
        User::factory()->count(20)->create();

        // 🏭 FACTORY: Buat 100 posts random
        Post::factory()->count(100)->create();

        // 🌱 SEEDER: Buat tags spesifik
        $tags = ['Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React'];
        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => str()->slug($tagName),
            ]);
        }
    }
}
```

**Workflow:**
1. **Seeder** → Buat 3 kategori spesifik (master data)
2. **Seeder** → Buat 1 admin user (login awal)
3. **Factory** → Generate 20 users random (testing)
4. **Factory** → Generate 100 posts random (testing)
5. **Seeder** → Buat 5 tags spesifik (master data)

**Jalankan:**
```bash
php artisan migrate:fresh --seed
```

**Hasil:**
- Database direset
- Table ter-create via migration
- Data ter-isi via seeder & factory
- Siap untuk development!

---

## 📊 Bagian 8: Faker Cheat Sheet

### Data Teks

```php
fake()->word()                    // "lorem"
fake()->words(3, true)            // "lorem ipsum dolor"
fake()->sentence(6)               // "Lorem ipsum dolor sit amet consectetur."
fake()->sentences(3, true)        // "Lorem ipsum. Dolor sit. Amet consectetur."
fake()->paragraph(4)              // 4 paragraf
fake()->text(200)                 // Text 200 karakter
```

---

### Data Personal

```php
fake()->name()                    // "Budi Santoso"
fake()->firstName()               // "Budi"
fake()->lastName()                // "Santoso"
fake()->email()                   // "budi@gmail.com"
fake()->safeEmail()               // "budi@example.com"
fake()->userName()                // "budi.santoso"
fake()->phoneNumber()             // "081234567890"
```

---

### Data Alamat

```php
fake()->address()                 // "Jl. Merdeka No. 123, Jakarta"
fake()->city()                    // "Jakarta"
fake()->country()                 // "Indonesia"
fake()->postcode()                // "12345"
```

---

### Data Angka

```php
fake()->numberBetween(1, 100)     // Random integer 1-100
fake()->randomFloat(2, 0, 1000)   // Random float 0-1000 dengan 2 desimal
fake()->randomDigit()             // Random digit 0-9
fake()->randomNumber(5)           // Random number 5 digit
```

---

### Data Boolean & Date

```php
fake()->boolean()                 // true/false (50%-50%)
fake()->boolean(80)               // true/false (80%-20%)
fake()->date()                    // "2024-03-15"
fake()->dateTime()                // DateTime object
fake()->dateTimeBetween('-1 year', 'now')  // Random date tahun lalu
```

---

### Data Internet

```php
fake()->url()                     // "https://example.com/path"
fake()->slug()                    // "lorem-ipsum-dolor"
fake()->ipv4()                    // "192.168.1.1"
fake()->userAgent()               // "Mozilla/5.0..."
```

---

### Data Unik

```php
fake()->unique()->email()         // Email unik (tidak duplicate)
fake()->unique()->userName()      // Username unik
```

**Reset unique:**
```php
fake()->unique(true)->email();    // Reset counter
```

---

## 💡 Bagian 9: Factory States (Advanced)

### Custom States untuk Skenario Tertentu

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'slug' => fake()->slug(),
            'body' => fake()->paragraphs(5, true),
            'is_published' => false, // Default: draft
            'published_at' => null,
        ];
    }

    /**
     * 🎯 State: Post yang sudah published
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * 🎯 State: Post yang featured
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'views' => fake()->numberBetween(1000, 10000),
        ]);
    }
}
```

**Penggunaan:**
```php
// Buat 10 posts yang sudah published
Post::factory()->count(10)->published()->create();

// Buat 5 posts yang featured
Post::factory()->count(5)->featured()->create();

// Buat 3 posts yang published DAN featured
Post::factory()->count(3)->published()->featured()->create();
```

**Gunanya:** Buat skenario testing yang spesifik!

---

## 🎯 Bagian 10: Best Practices

### 1. Seeder untuk Data Master

```php
// ✅ Good: Data master/spesifik pakai Seeder
CategorySeeder::class  // 3-5 kategori fixed
RoleSeeder::class      // Admin, Editor, User
SettingSeeder::class   // App settings
```

---

### 2. Factory untuk Data Testing

```php
// ✅ Good: Data random/banyak pakai Factory
Post::factory()->count(100)->create();
User::factory()->count(50)->create();
Product::factory()->count(500)->create();
```

---

### 3. Kombinasi di DatabaseSeeder

```php
public function run(): void
{
    // 1️⃣ Seeder dulu (data master)
    $this->call([
        CategorySeeder::class,
        RoleSeeder::class,
    ]);

    // 2️⃣ Factory untuk data testing
    User::factory()->count(20)->create();
    Post::factory()->count(100)->create();
}
```

---

### 4. Truncate Sebelum Insert (Hindari Duplikat)

```php
use Illuminate\Support\Facades\DB;

public function run(): void
{
    // 🗑️ Hapus data lama dulu
    DB::table('categories')->truncate();

    // 🌱 Insert data baru
    Category::create(['name' => 'Teknologi', ...]);
}
```

⚠️ **Hati-hati:** `truncate()` akan hapus semua data!

---

### 5. Pakai Foreign Key Relations (Nanti di Bab 20)

```php
// Buat user dengan posts-nya sekaligus
User::factory()
    ->has(Post::factory()->count(5))
    ->create();
```

**Hasil:** 1 user dengan 5 posts otomatis!

---

## 📝 Latihan: Buat Seeder & Factory

### Latihan 1: Buat CategorySeeder

**Task:**
1. Buat CategorySeeder dengan Artisan
2. Isi dengan 4 kategori: Teknologi, Lifestyle, Bisnis, Kesehatan
3. Jalankan seeder dan cek di database

**Command:**
```bash
php artisan make:seeder CategorySeeder
```

**Isi file seeder:**
```php
// (Isi sesuai contoh di atas)
```

**Jalankan:**
```bash
php artisan db:seed --class=CategorySeeder
```

---

### Latihan 2: Buat PostFactory

**Task:**
1. Buat PostFactory dengan Artisan
2. Definisikan columns: title, slug, body, excerpt, published_at, is_published
3. Generate 10 posts via Tinker

**Command:**
```bash
php artisan make:factory PostFactory --model=Post
```

**Test di Tinker:**
```bash
php artisan tinker
```

```php
>>> Post::factory()->count(10)->create();
```

---

### Latihan 3: Kombinasi Seeder + Factory

**Task:**
1. Edit `DatabaseSeeder.php`
2. Panggil CategorySeeder
3. Generate 50 posts via PostFactory
4. Jalankan `migrate:fresh --seed`

**Isi DatabaseSeeder:**
```php
public function run(): void
{
    $this->call([CategorySeeder::class]);
    Post::factory()->count(50)->create();
}
```

**Jalankan:**
```bash
php artisan migrate:fresh --seed
```

---

### Latihan 4: Factory dengan State

**Task:**
1. Tambahkan method `published()` di PostFactory
2. Generate 20 posts yang sudah published
3. Generate 10 posts yang draft (default)

**Hint:**
```php
// Published
Post::factory()->count(20)->published()->create();

// Draft
Post::factory()->count(10)->create();
```

---

## ⚠️ Troubleshooting

### Problem: "Class 'Database\Seeders\CategorySeeder' not found"

**Penyebab:** Seeder belum di-register atau typo.

**Solusi:**
```bash
# Reload autoload Composer
composer dump-autoload

# Pastikan nama class sesuai dengan nama file
CategorySeeder.php → class CategorySeeder
```

---

### Problem: "SQLSTATE[23000]: Integrity constraint violation"

**Penyebab:** Duplicate data atau foreign key constraint.

**Solusi:**
```php
// Truncate dulu sebelum insert
DB::table('categories')->truncate();

// Atau pakai unique() di Factory
fake()->unique()->email();
```

---

### Problem: Factory generate data aneh (bahasa Inggris)

**Penyebab:** Faker default bahasa Inggris.

**Solusi (Optional - ubah locale):**

**File:** `config/app.php`
```php
'faker_locale' => 'id_ID', // Indonesia
```

**Hasil:** Nama akan jadi "Budi Santoso", "Siti Nurhaliza", dll.

---

### Problem: Seeder jalan tapi data tidak masuk

**Penyebab:**
1. Migration belum dijalankan (table tidak ada)
2. Connection database salah

**Solusi:**
```bash
# 1. Pastikan migration sudah jalan
php artisan migrate

# 2. Cek .env database config
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=

# 3. Test koneksi
php artisan tinker
>>> DB::connection()->getPdo();
```

---

### Problem: "Call to undefined method Illuminate\Database\Query\Builder::factory()"

**Penyebab:** Pakai `DB::table()` bukan Model.

**Solusi:**
```php
// ❌ Salah (DB::table tidak punya factory)
DB::table('posts')->factory()->create();

// ✅ Benar (Pakai Model)
use App\Models\Post;
Post::factory()->create();
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Seeder** = Isi data awal/master ke database (data spesifik, sedikit)
- ✅ **Factory** = Generate data dummy random dalam jumlah besar
- ✅ **Faker** = Library untuk generate data realistis
- ✅ **Kapan pakai Seeder vs Factory** = Seeder untuk master, Factory untuk testing
- ✅ Command penting:
  - `php artisan make:seeder NameSeeder`
  - `php artisan make:factory NameFactory --model=Name`
  - `php artisan db:seed --class=NameSeeder`
  - `php artisan migrate:fresh --seed`
- ✅ **Kombinasi Seeder + Factory** = Power combo untuk development!
- ✅ **Factory States** = Custom scenario untuk testing
- ✅ **Faker Methods** = name(), email(), sentence(), numberBetween(), dll

**Sekarang kamu bisa isi database dengan data dummy untuk testing dan development!** 🌱

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Apa itu Model dan Eloquent ORM
- ✅ Eloquent dengan analogi "Google Translate" untuk database
- ✅ CRUD operations dengan Eloquent (Create, Read, Update, Delete)
- ✅ Query Builder vs Eloquent
- ✅ Praktik langsung di Tinker

**Saatnya bicara dengan database pakai bahasa yang lebih manusiawi!** 🗣️

---

## 🔗 Referensi

- 📖 [Database: Seeding](https://laravel.com/docs/12.x/seeding)
- 📖 [Database Testing: Factories](https://laravel.com/docs/12.x/eloquent-factories)
- 📖 [Faker Documentation](https://fakerphp.org/)
- 🎥 [Laracasts - Database Seeding](https://laracasts.com)

---

[⬅️ Bab 13: Migration Dasar](13-migration-dasar.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 15: Model & Eloquent ➡️](15-model-eloquent.md)

---

<div align="center">

**Seeder & Factory sudah dikuasai! Data dummy siap!** 🌱

**Lanjut ke Model & Eloquent untuk CRUD operations!** 🗣️

</div>