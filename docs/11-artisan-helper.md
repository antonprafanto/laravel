# Bab 11: Artisan Helper 🤖

[⬅️ Bab 10: Controller](10-controller.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 12: Pengenalan Database ➡️](12-database-intro.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu Artisan dan mengapa penting
- ✅ Bisa pakai command `php artisan list`
- ✅ Menguasai command `make:*` untuk generate files
- ✅ Bisa pakai `route:list`, `cache:clear`, `config:clear`
- ✅ Tau cara baca help untuk setiap command
- ✅ Lebih familier dengan terminal

---

## 🎯 Analogi Sederhana: Artisan seperti Asisten Pribadi

**Tanpa Artisan (Manual!):**
```
Mau buat controller:
1. Buka folder app/Http/Controllers
2. Buat file baru
3. Ketik namespace
4. Ketik class extends Controller
5. Ketik method...
→ Lama dan bisa typo! 😫
```

**Dengan Artisan (Otomatis!):**
```
Ketik di terminal:
php artisan make:controller PostController

→ File langsung jadi dengan template! ✨
```

**Artisan** = Asisten robot yang bisa disuruh macam-macam!

---

## 📚 Penjelasan: Apa itu Artisan?

**Artisan** = Command-line tool bawaan Laravel

**Fungsi:**
- 🤖 Generate files (controller, model, migration, dll)
- 🧹 Clear cache
- 🔍 Lihat routes
- 🚀 Jalankan server
- 🗄️ Jalankan migration
- ✨ Dan masih banyak lagi!

**Lokasi:** File `artisan` di root project

---

## 📋 Bagian 1: Lihat Semua Command

### Command: php artisan list

```bash
php artisan list
```

**Output:** Daftar semua command yang tersedia

**Kategori command:**
- `make:*` - Generate files
- `migrate:*` - Database migration
- `route:*` - Route operations
- `cache:*` - Cache operations
- `config:*` - Config operations
- `view:*` - View operations
- Dan masih banyak lagi!

---

### Command: php artisan

```bash
php artisan
```

Sama dengan `php artisan list` - Tampilkan semua command.

---

## 🔧 Bagian 2: Command make:* (Generate Files)

### 1. make:controller - Buat Controller

```bash
# Controller biasa
php artisan make:controller UserController

# Resource controller (7 method CRUD)
php artisan make:controller ProductController --resource

# API controller
php artisan make:controller ApiController --api

# Controller di subfolder
php artisan make:controller Admin/DashboardController
```

**Output:**
```
INFO  Controller [app/Http/Controllers/UserController.php] created successfully.
```

---

### 2. make:model - Buat Model

```bash
# Model biasa
php artisan make:model Post

# Model + migration
php artisan make:model Product -m

# Model + migration + controller + seeder + factory
php artisan make:model Article -mcfs

# Model + semua (migration, controller resource, seeder, factory, policy, requests)
php artisan make:model Book -a
```

**Flag yang berguna:**
- `-m` atau `--migration` - Buat migration sekaligus
- `-c` atau `--controller` - Buat controller sekaligus
- `-r` atau `--resource` - Controller jadi resource
- `-s` atau `--seed` - Buat seeder sekaligus
- `-f` atau `--factory` - Buat factory sekaligus
- `-a` atau `--all` - Buat semua!

---

### 3. make:migration - Buat Migration

```bash
# Buat tabel baru
php artisan make:migration create_posts_table

# Tambah kolom ke tabel existing
php artisan make:migration add_status_to_posts_table

# Ubah kolom di tabel existing
php artisan make:migration modify_posts_table
```

**Naming convention:**
- `create_xxx_table` - Buat tabel baru
- `add_xxx_to_yyy_table` - Tambah kolom
- `drop_xxx_from_yyy_table` - Hapus kolom

---

### 4. make:seeder - Buat Seeder

```bash
php artisan make:seeder UserSeeder
php artisan make:seeder ProductSeeder
```

---

### 5. make:factory - Buat Factory

```bash
php artisan make:factory PostFactory
```

---

### 6. make:middleware - Buat Middleware

```bash
php artisan make:middleware CheckAge
php artisan make:middleware IsAdmin
```

---

### 7. make:request - Buat Form Request

```bash
php artisan make:request StorePostRequest
php artisan make:request UpdateUserRequest
```

---

### 8. make:policy - Buat Policy

```bash
php artisan make:policy PostPolicy
php artisan make:policy PostPolicy --model=Post
```

---

### 9. make:command - Buat Custom Command

```bash
php artisan make:command SendEmails
```

---

## 🗺️ Bagian 3: Command route:*

### 1. route:list - Lihat Semua Routes

```bash
php artisan route:list
```

**Output:**
```
  GET|HEAD  /
  GET|HEAD  /posts              posts.index
  POST      /posts              posts.store
  GET|HEAD  /posts/create       posts.create
  GET|HEAD  /posts/{post}       posts.show
  PUT|PATCH /posts/{post}       posts.update
  DELETE    /posts/{post}       posts.destroy
  GET|HEAD  /posts/{post}/edit  posts.edit
```

**Berguna untuk:**
- Debug route yang tidak jalan
- Lihat semua endpoint
- Cek nama route
- Lihat method yang diterima

---

### 2. route:list dengan Filter

```bash
# Filter berdasarkan method
php artisan route:list --method=GET

# Filter berdasarkan path
php artisan route:list --path=post

# Filter berdasarkan nama
php artisan route:list --name=posts

# Compact view (lebih ringkas)
php artisan route:list --compact

# Hanya tampilkan kolom tertentu
php artisan route:list --columns=uri,name,action
```

---

### 3. route:cache - Cache Routes (Production)

```bash
# Cache routes untuk performa lebih cepat
php artisan route:cache
```

**Gunakan saat production!** Jangan pakai saat development.

---

### 4. route:clear - Clear Route Cache

```bash
php artisan route:clear
```

Gunakan kalau route tidak update setelah perubahan.

---

## 🧹 Bagian 4: Command cache:* dan config:*

### 1. cache:clear - Clear Application Cache

```bash
php artisan cache:clear
```

---

### 2. config:clear - Clear Config Cache

```bash
php artisan config:clear
```

---

### 3. config:cache - Cache Config (Production)

```bash
php artisan config:cache
```

---

### 4. view:clear - Clear Compiled Views

```bash
php artisan view:clear
```

Gunakan kalau view tidak update setelah perubahan.

---

### 5. optimize:clear - Clear All Cache

```bash
php artisan optimize:clear
```

**Ini akan clear:**
- Application cache
- Route cache
- Config cache
- View cache
- Compiled files

**Command paling berguna saat ada masalah cache!**

---

## 🚀 Bagian 5: Command Server & Development

### 1. serve - Jalankan Development Server

```bash
# Default: http://localhost:8000
php artisan serve

# Custom port
php artisan serve --port=8080

# Custom host
php artisan serve --host=192.168.1.100 --port=8000
```

---

### 2. tinker - Interactive Shell

```bash
php artisan tinker
```

**Gunanya:**
- Test code Laravel secara interaktif
- Query database
- Test Model
- Eksperimen dengan data

**Contoh di Tinker:**
```php
>>> $user = new App\Models\User;
>>> $user->name = "Budi";
>>> $user->email = "budi@mail.com";
>>> $user->save();

>>> App\Models\User::all();
>>> App\Models\User::find(1);
>>> App\Models\User::where('name', 'Budi')->first();
```

**Keluar dari Tinker:** Ketik `exit` atau tekan `Ctrl+C`

---

## 🗄️ Bagian 6: Command migrate:*

### 1. migrate - Jalankan Migration

```bash
php artisan migrate
```

Eksekusi semua migration yang belum dijalankan.

---

### 2. migrate:rollback - Rollback Migration

```bash
# Rollback batch terakhir
php artisan migrate:rollback

# Rollback 3 batch terakhir
php artisan migrate:rollback --step=3
```

---

### 3. migrate:reset - Reset Semua Migration

```bash
php artisan migrate:reset
```

Rollback semua migration (database jadi kosong).

---

### 4. migrate:refresh - Refresh Database

```bash
# Rollback semua + migrate lagi
php artisan migrate:refresh

# Refresh + run seeder
php artisan migrate:refresh --seed
```

---

### 5. migrate:fresh - Drop & Recreate

```bash
# Drop semua table + migrate dari awal
php artisan migrate:fresh

# Fresh + seeder
php artisan migrate:fresh --seed
```

**migrate:fresh vs migrate:refresh:**
- `fresh` = Drop semua table, buat baru
- `refresh` = Rollback migration, run lagi

---

### 6. migrate:status - Cek Status Migration

```bash
php artisan migrate:status
```

Lihat migration mana yang sudah dijalankan.

---

## 🌱 Bagian 7: Command db:*

### 1. db:seed - Run Database Seeder

```bash
# Run semua seeder
php artisan db:seed

# Run seeder tertentu
php artisan db:seed --class=UserSeeder
```

---

### 2. db:wipe - Drop Semua Tables

```bash
php artisan db:wipe
```

⚠️ **Hati-hati!** Semua data akan hilang!

---

## ❓ Bagian 8: Cara Baca Help

### Lihat Help untuk Command Tertentu

```bash
# Format: php artisan help [command]

php artisan help make:controller
php artisan help migrate
php artisan help route:list
```

**Output:** Penjelasan lengkap command, options, dan contoh.

---

### Shorthand dengan --help

```bash
php artisan make:controller --help
php artisan migrate --help
```

Sama dengan `php artisan help [command]`

---

## 💡 Bagian 9: Command yang Sering Dipakai

### Top 10 Command untuk Pemula

| No | Command | Fungsi | Frekuensi |
|----|---------|--------|-----------|
| 1 | `serve` | Jalankan server | Setiap coding |
| 2 | `make:controller` | Buat controller | Sering |
| 3 | `make:model` | Buat model | Sering |
| 4 | `make:migration` | Buat migration | Sering |
| 5 | `migrate` | Jalankan migration | Sering |
| 6 | `route:list` | Lihat routes | Sering |
| 7 | `tinker` | Interactive shell | Kadang |
| 8 | `cache:clear` | Clear cache | Saat ada masalah |
| 9 | `optimize:clear` | Clear all cache | Saat ada masalah |
| 10 | `db:seed` | Run seeder | Kadang |

---

## 📝 Latihan: Eksplorasi Artisan

### Latihan 1: Generate Files

Buat files berikut dengan Artisan:
```bash
# 1. Controller untuk Article
php artisan make:controller ArticleController --resource

# 2. Model Category dengan migration
php artisan make:model Category -m

# 3. Seeder untuk Product
php artisan make:seeder ProductSeeder
```

Cek apakah files berhasil dibuat!

---

### Latihan 2: Route List

1. Buat beberapa route di `web.php`
2. Jalankan: `php artisan route:list`
3. Filter hanya route dengan method GET
4. Filter hanya route yang ada kata "post"

---

### Latihan 3: Tinker Experiment

Buka Tinker dan coba:
```bash
php artisan tinker
```

```php
# Di Tinker, coba:
>>> 5 + 3
>>> "Hello " . "World"
>>> collect([1, 2, 3, 4, 5])->sum()
>>> now()
>>> now()->format('Y-m-d')
```

---

### Latihan 4: Help Exploration

Baca help untuk 3 command ini:
```bash
php artisan help make:model
php artisan help migrate
php artisan help db:seed
```

Pelajari options yang tersedia.

---

## 🎯 Workflow Development dengan Artisan

### Skenario: Buat Fitur Blog

**Step 1: Buat Model + Migration**
```bash
php artisan make:model Post -m
```

**Step 2: Edit Migration**
Edit file di `database/migrations/xxxx_create_posts_table.php`

**Step 3: Jalankan Migration**
```bash
php artisan migrate
```

**Step 4: Buat Controller**
```bash
php artisan make:controller PostController --resource
```

**Step 5: Buat Seeder (Optional)**
```bash
php artisan make:seeder PostSeeder
```

**Step 6: Run Seeder**
```bash
php artisan db:seed --class=PostSeeder
```

**Step 7: Cek Routes**
```bash
php artisan route:list --name=posts
```

**Semua dalam hitungan menit!** ⚡

---

## 🆘 Troubleshooting dengan Artisan

### Problem: Route tidak update

**Solusi:**
```bash
php artisan route:clear
php artisan route:cache  # Hanya untuk production
```

---

### Problem: View tidak update

**Solusi:**
```bash
php artisan view:clear
```

---

### Problem: Config tidak update

**Solusi:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

### Problem: Semua tidak jalan (Nuclear Option)

**Solusi:**
```bash
php artisan optimize:clear
composer dump-autoload
```

Ini akan clear semua cache + reload composer autoload.

---

## 🔥 Pro Tips

### 1. Alias untuk Command Panjang

**Windows (PowerShell Profile):**
```powershell
# Edit profile
notepad $PROFILE

# Tambahkan alias
function pa { php artisan $args }
function pam { php artisan make:$args }
```

Sekarang bisa pakai:
```bash
pa serve
pam controller PostController
```

---

**Mac/Linux (Bash/Zsh):**
```bash
# Edit .bashrc atau .zshrc
nano ~/.bashrc

# Tambahkan alias
alias pa="php artisan"
alias pam="php artisan make:"
```

Sekarang bisa pakai:
```bash
pa serve
pam controller PostController
```

---

### 2. Chain Multiple Commands

```bash
# Windows
php artisan migrate:fresh && php artisan db:seed

# Mac/Linux (sama)
php artisan migrate:fresh && php artisan db:seed
```

---

### 3. Background Server

**Windows:**
```powershell
Start-Process php -ArgumentList "artisan serve" -WindowStyle Hidden
```

**Mac/Linux:**
```bash
php artisan serve > /dev/null 2>&1 &
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Artisan = Command-line tool Laravel
- ✅ `php artisan list` = Lihat semua command
- ✅ `make:*` = Generate files (controller, model, dll)
- ✅ `route:list` = Lihat semua routes
- ✅ `cache:clear`, `config:clear`, `view:clear` = Clear cache
- ✅ `optimize:clear` = Clear all cache sekaligus
- ✅ `migrate` = Jalankan database migration
- ✅ `db:seed` = Run seeder
- ✅ `tinker` = Interactive shell
- ✅ `help` = Lihat help untuk command

**Artisan membuat development jadi lebih cepat!** 🚀

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Pengenalan database dan MySQL
- ✅ Setup database di Laravel (.env)
- ✅ Konfigurasi database connection
- ✅ Test koneksi database
- ✅ Persiapan untuk migration

**Saatnya masuk ke database!** 🗄️

---

## 🔗 Referensi

- 📖 [Artisan Console](https://laravel.com/docs/12.x/artisan)
- 📖 [Writing Commands](https://laravel.com/docs/12.x/artisan#writing-commands)
- 🎥 [Laracasts - Artisan](https://laracasts.com)

---

[⬅️ Bab 10: Controller](10-controller.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 12: Pengenalan Database ➡️](12-database-intro.md)

---

<div align="center">

**Artisan sudah dikuasai! Workflow jadi lebih cepat!** 🤖

**Lanjut ke Database untuk aplikasi yang dinamis!** 🗄️

</div>