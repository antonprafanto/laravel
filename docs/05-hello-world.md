# Bab 05: Hello World - Quick Win! 🎉

[⬅️ Bab 04: Project Pertama](04-project-pertama.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 06: Routing Lanjutan ➡️](06-routing-lanjutan.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Membuat route pertama yang return teks sederhana
- ✅ Memahami file `routes/web.php` - "Buku Menu" restoran
- ✅ Membuat route dengan parameter dinamis
- ✅ Merasakan **dopamine hit** pertama - "Saya bisa coding Laravel!" 💪
- ✅ Build confidence sebelum masuk konsep yang lebih kompleks

---

## 🎯 Analogi Sederhana: Route seperti Bilang "Halo" ke Tamu

**Tanpa sistem (kacau!):**
```
Tamu: "Halo!"
Restoran: *bingung* "Siapa? Mau apa?"
Tamu: *pergi* 😞
```

**Dengan Route (jelas!):**
```
Tamu datang ke pintu (/halo)
→ Satpam (Route) cek buku tamu
→ "Oh ada di daftar! Jawab: Halo juga!"
→ Tamu senang! 😊
```

**Route** adalah aturan: "Kalau ada yang datang ke alamat ini, jawab ini!"

---

## 📚 Penjelasan: Apa itu Route?

**Route** = Peta jalan yang menghubungkan **URL** dengan **Action**

```
URL: /about
Action: Tampilkan halaman about
```

**Analogi:**
```
Buku Menu Restoran:
- Halaman 1 (/home)     → Menu Utama
- Halaman 2 (/about)    → Tentang Kami
- Halaman 3 (/contact)  → Kontak
- Halaman 4 (/menu)     → Daftar Makanan
```

Semua route di Laravel ada di file: **`routes/web.php`**

---

## 🚀 Bagian 1: Membuat Route Pertama

### Step 1: Pastikan Server Jalan

Buka terminal, pastikan kamu di folder project:
```bash
cd blog-app
```

Jalankan server (jika belum jalan):
```bash
php artisan serve
```

**Output:**
```
INFO  Server running on [http://127.0.0.1:8000].
```

**Biarkan terminal tetap terbuka!**

---

### Step 2: Buka File routes/web.php

Di VS Code, buka: **`routes/web.php`**

Kamu akan lihat kode seperti ini:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

**Penjelasan:**
- `Route::get('/')` = Route untuk homepage (`http://localhost:8000/`)
- `function()` = Function yang akan dijalankan
- `return view('welcome')` = Tampilkan view welcome.blade.php

---

### Step 3: Tambah Route "Halo Dunia"

**Copy-paste kode ini di bawah route yang sudah ada:**

```php
Route::get('/halo', function () {
    return "Halo Dunia! Ini route pertama saya! 🎉";
});
```

**File lengkapnya sekarang:**

```php
<?php

use Illuminate\Support\Facades\Route;

// Route homepage
Route::get('/', function () {
    return view('welcome');
});

// 🆕 ROUTE BARU: Halo Dunia
Route::get('/halo', function () {
    return "Halo Dunia! Ini route pertama saya! 🎉";
});
```

**Save file** (`Ctrl+S`)

---

### Step 4: Test di Browser

1. Buka browser
2. Ketik: `http://localhost:8000/halo`
3. Tekan Enter

**Kamu akan lihat:**
```
Halo Dunia! Ini route pertama saya! 🎉
```

**BERHASIL!** Ini adalah route pertamamu! 🎉🎉🎉

---

## 🔄 Hubungan Analogi dengan Kode

Mari kita pahami kode tadi dengan analogi:

```php
Route::get('/halo', function () {
    return "Halo Dunia! Ini route pertama saya! 🎉";
});
```

**Analogi Restoran:**

| Kode | Analogi | Arti |
|------|---------|------|
| `Route::get` | "Saat ada tamu..." | Method HTTP GET |
| `'/halo'` | "...yang datang ke meja nomor 'halo'..." | URL path |
| `function()` | "...maka pelayan akan..." | Action yang dilakukan |
| `return` | "...menyajikan..." | Kirim response |
| `"Halo Dunia!"` | "...hidangan ini" | Konten yang ditampilkan |

**Terjemahan lengkap:**
> "Saat ada tamu yang datang ke `/halo`, maka tampilkan teks 'Halo Dunia!'"

---

## 💡 Latihan Bertahap (Baby Steps!)

### Latihan 1: Buat Route /nama

**Goal:** Tampilkan namamu sendiri

```php
Route::get('/nama', function () {
    return "Nama saya adalah Budi Santoso";
});
```

**Test:** Buka `http://localhost:8000/nama`

**Expected Output:**
```
Nama saya adalah Budi Santoso
```

**Ubah "Budi Santoso" dengan namamu!**

---

### Latihan 2: Buat Route /hobi

**Goal:** Tampilkan hobimu

```php
Route::get('/hobi', function () {
    return "Hobi saya adalah coding, gaming, dan traveling";
});
```

**Test:** Buka `http://localhost:8000/hobi`

---

### Latihan 3: Buat Route /tentang-saya

**Goal:** Tampilkan perkenalan lengkap

```php
Route::get('/tentang-saya', function () {
    return "Halo! Nama saya Budi, umur 23 tahun.
            Saya sedang belajar Laravel dan sangat excited! 🚀";
});
```

**Test:** Buka `http://localhost:8000/tentang-saya`

---

## 🎨 Return HTML (Bukan Cuma Teks!)

Route bisa return **HTML** juga!

```php
Route::get('/welcome', function () {
    return "<h1>Selamat Datang!</h1>
            <p>Ini adalah halaman welcome saya</p>
            <ul>
                <li>Belajar Laravel</li>
                <li>Membuat aplikasi</li>
                <li>Jadi developer handal!</li>
            </ul>";
});
```

**Test:** Buka `http://localhost:8000/welcome`

**Kamu akan lihat:**
- Heading besar "Selamat Datang!"
- Paragraph
- Bullet list

**HTML bekerja!** 🎨

---

## 🔢 Bagian 2: Route dengan Parameter

### 🎯 Analogi: Parameter seperti Memesan Makanan dengan Level Pedas

**Tanpa parameter (statis):**
```
Customer: "Pesan Nasi Goreng"
Chef: "Siap! Nasi Goreng level sedang"
```

**Dengan parameter (dinamis):**
```
Customer: "Pesan Nasi Goreng LEVEL 5 pedas"
Chef: "Siap! Nasi Goreng level 5!"
```

Parameter membuat route **dinamis** - bisa berubah-ubah!

---

### Contoh 1: Route dengan Parameter Angka

```php
Route::get('/angka/{nomor}', function ($nomor) {
    return "Angka yang kamu pilih adalah: " . $nomor;
});
```

**Penjelasan:**
- `{nomor}` = Parameter (tempat kosong yang bisa diisi)
- `function ($nomor)` = Tangkap parameter
- `$nomor` = Variable yang berisi value parameter

**Test:**
- `http://localhost:8000/angka/5` → "Angka yang kamu pilih adalah: 5"
- `http://localhost:8000/angka/100` → "Angka yang kamu pilih adalah: 100"
- `http://localhost:8000/angka/7` → "Angka yang kamu pilih adalah: 7"

**Dinamis kan?** Parameter `{nomor}` bisa berubah-ubah! 🎉

---

### Contoh 2: Route dengan Parameter Nama

```php
Route::get('/halo/{nama}', function ($nama) {
    return "Halo, " . $nama . "! Selamat datang di Laravel! 👋";
});
```

**Test:**
- `http://localhost:8000/halo/Budi` → "Halo, Budi! Selamat datang di Laravel! 👋"
- `http://localhost:8000/halo/Ani` → "Halo, Ani! Selamat datang di Laravel! 👋"
- `http://localhost:8000/halo/John` → "Halo, John! Selamat datang di Laravel! 👋"

**Ganti "Budi" dengan nama apapun!**

---

### Contoh 3: Route dengan 2 Parameter

```php
Route::get('/pesanan/{makanan}/{level}', function ($makanan, $level) {
    return "Anda memesan: " . $makanan . " dengan level pedas: " . $level;
});
```

**Test:**
- `http://localhost:8000/pesanan/nasi-goreng/5`
  → "Anda memesan: nasi-goreng dengan level pedas: 5"

- `http://localhost:8000/pesanan/mie-ayam/2`
  → "Anda memesan: mie-ayam dengan level pedas: 2"

**Bisa pakai beberapa parameter!** 🍜

---

### Contoh 4: Route dengan Operasi Matematika

```php
Route::get('/kali/{angka1}/{angka2}', function ($angka1, $angka2) {
    $hasil = $angka1 * $angka2;
    return $angka1 . " × " . $angka2 . " = " . $hasil;
});
```

**Test:**
- `http://localhost:8000/kali/5/3` → "5 × 3 = 15"
- `http://localhost:8000/kali/10/7` → "10 × 7 = 70"
- `http://localhost:8000/kali/12/12` → "12 × 12 = 144"

**Laravel bisa jadi kalkulator!** 🧮

---

## 📝 Latihan: Parameter (Level Up!)

### Latihan 4: Buat Route /umur/{tahun_lahir}

**Goal:** Hitung umur berdasarkan tahun lahir

```php
Route::get('/umur/{tahun_lahir}', function ($tahun_lahir) {
    $tahun_sekarang = 2024;
    $umur = $tahun_sekarang - $tahun_lahir;
    return "Umur kamu adalah: " . $umur . " tahun";
});
```

**Test:**
- `http://localhost:8000/umur/2000` → "Umur kamu adalah: 24 tahun"
- `http://localhost:8000/umur/1995` → "Umur kamu adalah: 29 tahun"

---

### Latihan 5: Buat Route /biodata/{nama}/{umur}/{kota}

**Goal:** Tampilkan biodata lengkap

```php
Route::get('/biodata/{nama}/{umur}/{kota}', function ($nama, $umur, $kota) {
    return "
        <h2>Biodata</h2>
        <p><strong>Nama:</strong> $nama</p>
        <p><strong>Umur:</strong> $umur tahun</p>
        <p><strong>Kota:</strong> $kota</p>
    ";
});
```

**Test:**
- `http://localhost:8000/biodata/Budi/25/Jakarta`

**Expected Output:**
```
Biodata
Nama: Budi
Umur: 25 tahun
Kota: Jakarta
```

---

### Latihan 6: Buat Route /suhu/{celsius}

**Goal:** Convert Celsius ke Fahrenheit

```php
Route::get('/suhu/{celsius}', function ($celsius) {
    $fahrenheit = ($celsius * 9/5) + 32;
    return $celsius . "°C = " . $fahrenheit . "°F";
});
```

**Test:**
- `http://localhost:8000/suhu/0` → "0°C = 32°F"
- `http://localhost:8000/suhu/100` → "100°C = 212°F"
- `http://localhost:8000/suhu/37` → "37°C = 98.6°F"

---

## 🎨 Return dengan Styling HTML

Buat route yang lebih cantik dengan inline CSS:

```php
Route::get('/profile', function () {
    return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Profile</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 50px;
                    text-align: center;
                }
                .card {
                    background: rgba(255,255,255,0.1);
                    padding: 30px;
                    border-radius: 10px;
                    backdrop-filter: blur(10px);
                }
                h1 {
                    font-size: 3em;
                    margin: 0;
                }
            </style>
        </head>
        <body>
            <div class='card'>
                <h1>👨‍💻</h1>
                <h1>Budi Santoso</h1>
                <p>Laravel Developer</p>
                <p>🚀 Belajar Laravel itu menyenangkan!</p>
            </div>
        </body>
        </html>
    ";
});
```

**Test:** `http://localhost:8000/profile`

**Hasilnya cantik!** 🎨

---

## 💪 Challenge: Buat Route Kreativitasmu!

Sekarang giliran kamu berkreasi! Buat route yang:

1. **Route /quotes** - Tampilkan quote favoritmu
2. **Route /skills** - List skill yang kamu punya
3. **Route /contact** - Info kontak kamu (email, GitHub, LinkedIn)
4. **Route /calculator/{operasi}/{a}/{b}** - Kalkulator sederhana
   - `/calculator/tambah/5/3` → 8
   - `/calculator/kurang/10/4` → 6
   - `/calculator/kali/6/7` → 42
   - `/calculator/bagi/20/5` → 4

**Hint untuk challenge 4:**

```php
Route::get('/calculator/{operasi}/{a}/{b}', function ($operasi, $a, $b) {
    if ($operasi == 'tambah') {
        $hasil = $a + $b;
    } elseif ($operasi == 'kurang') {
        $hasil = $a - $b;
    } elseif ($operasi == 'kali') {
        $hasil = $a * $b;
    } elseif ($operasi == 'bagi') {
        $hasil = $a / $b;
    } else {
        $hasil = "Operasi tidak valid";
    }

    return "$a $operasi $b = $hasil";
});
```

---

## ⚠️ Troubleshooting

### Problem 1: 404 Not Found

**Penyebab:** Typo di URL atau route belum disave

**Solusi:**
1. Cek spelling URL di browser
2. Cek spelling route di `web.php`
3. Pastikan sudah save file (`Ctrl+S`)
4. Refresh browser (`F5`)

---

### Problem 2: Route Tidak Muncul

**Penyebab:** Server tidak restart setelah ubah file

**Solusi:**
1. Stop server (`Ctrl+C`)
2. Clear cache: `php artisan route:clear`
3. Start server lagi: `php artisan serve`

---

### Problem 3: Syntax Error

**Error:**
```
syntax error, unexpected '=>' (T_DOUBLE_ARROW)
```

**Penyebab:** Lupa titik koma (`;`) atau kurung kurawal (`}`)

**Solusi:** Cek kode, pastikan semua syntax benar

---

### Problem 4: Parameter Tidak Terdeteksi

**Solusi:** Pastikan nama parameter di URL sama dengan di function:

```php
// ✅ BENAR
Route::get('/halo/{nama}', function ($nama) {
    return "Halo " . $nama;
});

// ❌ SALAH - nama parameter tidak sama
Route::get('/halo/{nama}', function ($name) {
    return "Halo " . $name; // $name undefined!
});
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Route adalah peta yang menghubungkan URL dengan Action
- ✅ Semua route web ada di `routes/web.php`
- ✅ `Route::get('/path', function() { ... })` adalah cara buat route
- ✅ Route bisa return teks, HTML, bahkan HTML + CSS
- ✅ Parameter route dengan `{nama}` membuat route dinamis
- ✅ Bisa pakai beberapa parameter sekaligus
- ✅ Route bisa dipakai untuk operasi (hitung umur, calculator, dll)

**Yang paling penting:** Kamu sudah buat route pertama dan lihat hasilnya! 🎉

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Route dengan method POST, PUT, DELETE
- ✅ Route naming (kasih nama route)
- ✅ Route groups (kelompokkan route)
- ✅ Route parameters optional
- ✅ Route constraints (validasi parameter)

**Routing akan makin powerful!** 🚀

---

## 🔗 Referensi

- 📖 [Laravel Routing Docs](https://laravel.com/docs/12.x/routing)
- 📖 [Route Parameters](https://laravel.com/docs/12.x/routing#route-parameters)
- 🎥 [Laracasts - Routing](https://laracasts.com/series/laravel-from-scratch/episodes/3)

---

[⬅️ Bab 04: Project Pertama](04-project-pertama.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 06: Routing Lanjutan ➡️](06-routing-lanjutan.md)

---

<div align="center">

**Selamat! Kamu sudah bisa buat route sendiri!** 🎉

**"Saya bisa coding Laravel!" - Kamu, sekarang** 💪

**Lanjut ke chapter berikutnya untuk routing yang lebih advanced!** 🚀

</div>