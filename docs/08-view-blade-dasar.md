# Bab 08: View & Blade Dasar 🎨

[⬅️ Bab 07: Pengenalan MVC](07-mvc-pengenalan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 09: Blade Layout & Components ➡️](09-blade-layout.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Bisa membuat file view pertama (.blade.php)
- ✅ Memahami Blade template engine
- ✅ Bisa passing data dari route ke view
- ✅ Menguasai Blade echo: `{{ }}` dan `{!! !!}`
- ✅ Bisa pakai Blade directives: @if, @foreach, @for
- ✅ Membuat halaman profil dengan data dinamis

---

## 🎯 Analogi Sederhana: View seperti Template Undangan

**Tanpa Template:**
```
Setiap mau bikin undangan → Desain dari nol
Undangan 1: Desain manual
Undangan 2: Desain manual lagi
Undangan 3: Desain manual lagi
→ Capek dan lama! 😫
```

**Dengan Template:**
```
Bikin template sekali:
┌─────────────────────┐
│ Undangan Pernikahan │
│                     │
│ Nama: [____]        │
│ Tanggal: [____]     │
│ Tempat: [____]      │
└─────────────────────┘

Tinggal isi nama, tanggal, tempat
→ Cepat dan konsisten! ✨
```

**Blade** adalah template engine untuk bikin view HTML dengan data dinamis!

---

## 📚 Penjelasan: Apa itu View & Blade?

### View
**View** = File HTML yang dilihat user di browser

**Lokasi:** `resources/views/`

**Ekstensi:** `.blade.php` (bukan `.html` biasa!)

### Blade
**Blade** = Template engine bawaan Laravel

**Keuntungan:**
- ✅ Syntax lebih bersih dari PHP biasa
- ✅ Fitur @if, @foreach, @extends, dll
- ✅ Auto-escape HTML (keamanan)
- ✅ Bisa pakai PHP biasa juga
- ✅ Di-compile jadi PHP murni (cepat!)

---

## 🚀 Bagian 1: Membuat View Pertama

### Step 1: Buat File View

Di VS Code, buat file baru: `resources/views/hello.blade.php`

**Isi file:**
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello Blade</title>
</head>
<body>
    <h1>Halo dari Blade!</h1>
    <p>Ini adalah view pertama saya.</p>
</body>
</html>
```

**Save** (`Ctrl+S`)

---

### Step 2: Buat Route yang Return View

Buka `routes/web.php`, tambahkan:

```php
Route::get('/hello', function () {
    return view('hello');
});
```

**Penjelasan:**
- `view('hello')` = Cari file `resources/views/hello.blade.php`
- Tidak perlu tulis `.blade.php`, cukup nama file saja

---

### Step 3: Test di Browser

1. Pastikan server jalan: `php artisan serve`
2. Buka: `http://localhost:8000/hello`

**Kamu akan lihat:**
```
Halo dari Blade!
Ini adalah view pertama saya.
```

**Berhasil!** View pertamamu sudah jalan! 🎉

---

## 💾 Bagian 2: Passing Data ke View

### 🎯 Analogi: Passing Data seperti Isi Formulir

```
Template Undangan Kosong:
┌─────────────────────┐
│ Undangan Pernikahan │
│ Nama: [____]        │  ← Kosong
│ Tanggal: [____]     │  ← Kosong
└─────────────────────┘

Isi dengan Data:
┌─────────────────────┐
│ Undangan Pernikahan │
│ Nama: Budi & Ani    │  ← Diisi!
│ Tanggal: 20 Jan 24  │  ← Diisi!
└─────────────────────┘
```

**Passing data** = Kirim data dari route/controller ke view!

---

### Cara 1: Compact (Recommended)

**Route:**
```php
Route::get('/profile', function () {
    $nama = "Budi Santoso";
    $umur = 25;
    $hobi = ["Coding", "Gaming", "Traveling"];

    return view('profile', compact('nama', 'umur', 'hobi'));
});
```

**View (profile.blade.php):**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>
    <h1>Profile Saya</h1>
    <p>Nama: {{ $nama }}</p>
    <p>Umur: {{ $umur }} tahun</p>
    <p>Hobi: {{ implode(', ', $hobi) }}</p>
</body>
</html>
```

**Output:**
```
Profile Saya
Nama: Budi Santoso
Umur: 25 tahun
Hobi: Coding, Gaming, Traveling
```

---

### Cara 2: Array Associative

```php
Route::get('/profile2', function () {
    return view('profile', [
        'nama' => 'Ani Wijaya',
        'umur' => 23,
        'hobi' => ['Reading', 'Cooking', 'Photography']
    ]);
});
```

**Hasilnya sama!** Pilih cara yang kamu suka.

---

### Cara 3: With Method (Chaining)

```php
Route::get('/profile3', function () {
    return view('profile')
        ->with('nama', 'John Doe')
        ->with('umur', 30)
        ->with('hobi', ['Music', 'Sports']);
});
```

**Jarang dipakai,** tapi boleh juga!

---

## 🔤 Bagian 3: Blade Echo - Menampilkan Data

### 1. Echo Biasa: `{{ }}`

```blade
<p>Nama: {{ $nama }}</p>
<p>Umur: {{ $umur }}</p>
```

**Keuntungan:**
- ✅ **Auto-escape HTML** (mencegah XSS attack)
- ✅ Safe untuk tampilkan input user

**Contoh auto-escape:**
```php
// Route
$nama = "<script>alert('Hacked!')</script>";
return view('test', compact('nama'));

// View
{{ $nama }}

// Output di HTML:
&lt;script&gt;alert('Hacked!')&lt;/script&gt;
// Script tidak jalan! Aman! ✅
```

---

### 2. Raw Echo: `{!! !!}`

```blade
<div>
    {!! $htmlContent !!}
</div>
```

**Kegunaan:**
- Render HTML yang sudah kamu percaya
- Misalnya: konten dari WYSIWYG editor

**⚠️ Hati-hati!** Jangan pakai untuk input user mentah!

**Contoh:**
```php
// Route
$content = "<strong>Teks tebal</strong> dan <em>miring</em>";
return view('article', compact('content'));

// View
{!! $content !!}

// Output:
Teks tebal dan miring  (dengan formatting HTML)
```

---

### 3. PHP Biasa (Jarang Dipakai)

```blade
<?php
    $total = $harga * $jumlah;
    echo $total;
?>
```

**Lebih baik pakai Blade!** Lebih bersih.

---

## 🔀 Bagian 4: Blade Directives - @if, @foreach

### 1. @if, @elseif, @else

**Sintaks:**
```blade
@if (kondisi)
    <!-- Kode jika true -->
@elseif (kondisi2)
    <!-- Kode jika kondisi2 true -->
@else
    <!-- Kode jika semua false -->
@endif
```

**Contoh 1: Cek Status**
```php
// Route
Route::get('/status', function () {
    $isLoggedIn = true;
    return view('status', compact('isLoggedIn'));
});
```

```blade
<!-- View: status.blade.php -->
<!DOCTYPE html>
<html>
<body>
    @if ($isLoggedIn)
        <p>✅ Anda sudah login</p>
        <a href="/dashboard">Ke Dashboard</a>
    @else
        <p>❌ Anda belum login</p>
        <a href="/login">Login Sekarang</a>
    @endif
</body>
</html>
```

---

**Contoh 2: Cek Nilai**
```php
// Route
Route::get('/nilai', function () {
    $nilai = 85;
    return view('nilai', compact('nilai'));
});
```

```blade
<!-- View: nilai.blade.php -->
<h2>Nilai Kamu: {{ $nilai }}</h2>

@if ($nilai >= 90)
    <p>Grade: A - Excellent! 🏆</p>
@elseif ($nilai >= 80)
    <p>Grade: B - Good! 👍</p>
@elseif ($nilai >= 70)
    <p>Grade: C - Not Bad</p>
@else
    <p>Grade: D - Perlu Belajar Lagi 📚</p>
@endif
```

---

**Contoh 3: @unless (kebalikan @if)**
```blade
@unless ($isPremium)
    <div class="ads">
        <p>Iklan: Upgrade ke Premium!</p>
    </div>
@endunless
```

Sama dengan:
```blade
@if (!$isPremium)
    <div class="ads">
        <p>Iklan: Upgrade ke Premium!</p>
    </div>
@endif
```

---

### 2. @foreach - Loop Array

**Sintaks:**
```blade
@foreach ($items as $item)
    <!-- Kode untuk setiap item -->
@endforeach
```

**Contoh 1: Loop Array Simple**
```php
// Route
Route::get('/hobi', function () {
    $hobi = ['Coding', 'Gaming', 'Reading', 'Traveling'];
    return view('hobi', compact('hobi'));
});
```

```blade
<!-- View: hobi.blade.php -->
<h2>Hobi Saya:</h2>
<ul>
    @foreach ($hobi as $item)
        <li>{{ $item }}</li>
    @endforeach
</ul>
```

**Output:**
```
Hobi Saya:
• Coding
• Gaming
• Reading
• Traveling
```

---

**Contoh 2: Loop Array Associative**
```php
// Route
Route::get('/students', function () {
    $students = [
        ['name' => 'Budi', 'score' => 85],
        ['name' => 'Ani', 'score' => 92],
        ['name' => 'Citra', 'score' => 78],
    ];
    return view('students', compact('students'));
});
```

```blade
<!-- View: students.blade.php -->
<h2>Daftar Mahasiswa</h2>
<table border="1">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Nilai</th>
    </tr>
    @foreach ($students as $index => $student)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $student['name'] }}</td>
        <td>{{ $student['score'] }}</td>
    </tr>
    @endforeach
</table>
```

---

**Contoh 3: Cek Array Kosong dengan @forelse**
```blade
<h2>Daftar Produk</h2>

@forelse ($products as $product)
    <div class="product">
        <h3>{{ $product['name'] }}</h3>
        <p>Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
    </div>
@empty
    <p>Belum ada produk tersedia.</p>
@endforelse
```

**Kalau `$products` kosong, akan tampil "Belum ada produk tersedia."**

---

### 3. @for - Loop Angka

```blade
<h2>Tabel Perkalian 5</h2>
<ul>
    @for ($i = 1; $i <= 10; $i++)
        <li>5 × {{ $i }} = {{ 5 * $i }}</li>
    @endfor
</ul>
```

**Output:**
```
Tabel Perkalian 5
• 5 × 1 = 5
• 5 × 2 = 10
• 5 × 3 = 15
...
• 5 × 10 = 50
```

---

### 4. @while - Loop dengan Kondisi

```blade
@php
    $count = 1;
@endphp

@while ($count <= 5)
    <p>Iterasi ke-{{ $count }}</p>
    @php
        $count++;
    @endphp
@endwhile
```

**Jarang dipakai!** Lebih sering pakai @foreach.

---

## 🔄 Bagian 5: Loop Variable - $loop

Di dalam @foreach, Blade menyediakan variable `$loop`:

```blade
@foreach ($items as $item)
    <div>
        Iterasi: {{ $loop->iteration }} <br>
        Index: {{ $loop->index }} <br>
        First? {{ $loop->first ? 'Ya' : 'Tidak' }} <br>
        Last? {{ $loop->last ? 'Ya' : 'Tidak' }} <br>
        Total: {{ $loop->count }}
    </div>
@endforeach
```

**Loop Variable Properties:**

| Property | Deskripsi | Contoh |
|----------|-----------|--------|
| `$loop->index` | Index (mulai dari 0) | 0, 1, 2, 3 |
| `$loop->iteration` | Iterasi (mulai dari 1) | 1, 2, 3, 4 |
| `$loop->first` | Apakah iterasi pertama? | true/false |
| `$loop->last` | Apakah iterasi terakhir? | true/false |
| `$loop->count` | Total item | 10 |
| `$loop->remaining` | Sisa iterasi | 5 |
| `$loop->depth` | Kedalaman loop (nested) | 1, 2, 3 |
| `$loop->parent` | Loop parent (nested) | object |

**Contoh Praktis:**
```blade
<table>
    @foreach ($users as $user)
    <tr class="{{ $loop->even ? 'bg-gray' : 'bg-white' }}">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $user->name }}</td>
    </tr>
    @endforeach
</table>
```

Baris genap akan punya background abu-abu!

---

## 💡 Contoh Lengkap: Halaman Profil Dinamis

**Route:**
```php
Route::get('/my-profile', function () {
    $data = [
        'name' => 'Budi Santoso',
        'age' => 25,
        'job' => 'Laravel Developer',
        'skills' => [
            ['name' => 'PHP', 'level' => 90],
            ['name' => 'Laravel', 'level' => 85],
            ['name' => 'JavaScript', 'level' => 75],
            ['name' => 'MySQL', 'level' => 80],
        ],
        'isPremium' => true,
        'bio' => 'Passionate about building web applications with Laravel.'
    ];

    return view('my-profile', $data);
});
```

**View (my-profile.blade.php):**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ $name }} - Profile</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .badge { background: gold; padding: 5px 10px; border-radius: 5px; }
        .skill-bar { background: #ddd; border-radius: 5px; overflow: hidden; margin: 5px 0; }
        .skill-progress { background: #4CAF50; color: white; padding: 5px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $name }}</h1>

        @if ($isPremium)
            <span class="badge">⭐ Premium Member</span>
        @endif

        <p><strong>Umur:</strong> {{ $age }} tahun</p>
        <p><strong>Pekerjaan:</strong> {{ $job }}</p>

        <h2>Bio</h2>
        <p>{{ $bio }}</p>

        <h2>Skills</h2>
        @foreach ($skills as $skill)
            <div class="skill-bar">
                <div class="skill-progress" style="width: {{ $skill['level'] }}%">
                    {{ $skill['name'] }} - {{ $skill['level'] }}%
                </div>
            </div>
        @endforeach

        <h2>Status</h2>
        @if ($age < 20)
            <p>🎓 Masih muda, semangat belajar!</p>
        @elseif ($age < 30)
            <p>💼 Usia produktif, saatnya berkarir!</p>
        @else
            <p>🌟 Berpengalaman dan wise!</p>
        @endif
    </div>
</body>
</html>
```

**Test:** `http://localhost:8000/my-profile`

**Hasilnya cantik dan dinamis!** 🎨

---

## 📝 Latihan

### Latihan 1: Daftar To-Do

Buat halaman to-do list dengan data:
```php
$todos = [
    ['task' => 'Belajar Laravel', 'done' => true],
    ['task' => 'Buat project', 'done' => false],
    ['task' => 'Deploy ke server', 'done' => false],
];
```

Tampilkan dengan:
- ✅ Task yang selesai (dengan strikethrough)
- ⬜ Task yang belum selesai

---

### Latihan 2: Kartu Ucapan

Buat route `/greeting/{name}/{time}` yang menampilkan:
- "Selamat Pagi, {name}!" jika time = pagi
- "Selamat Siang, {name}!" jika time = siang
- "Selamat Malam, {name}!" jika time = malam

---

### Latihan 3: Tabel Perkalian

Buat halaman yang menampilkan tabel perkalian 1-10 dalam bentuk tabel HTML.

---

## ⚠️ Troubleshooting

### Problem 1: View not found

**Error:** `View [nama] not found`

**Solusi:**
1. Cek spelling nama file
2. Cek file ada di `resources/views/`
3. Cek ekstensi `.blade.php`
4. Clear view cache: `php artisan view:clear`

---

### Problem 2: Variable undefined

**Error:** `Undefined variable: nama`

**Solusi:**
1. Pastikan variable di-pass dari route
2. Cek spelling variable name
3. Gunakan `{{ $nama ?? 'Default' }}` untuk default value

---

### Problem 3: HTML tidak ter-render

**Penyebab:** Pakai `{{ }}` untuk HTML

**Solusi:** Pakai `{!! !!}` (tapi hati-hati XSS!)

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ View = File HTML di `resources/views/`
- ✅ Blade = Template engine Laravel
- ✅ `{{ $var }}` = Echo dengan auto-escape (aman)
- ✅ `{!! $var !!}` = Raw HTML (hati-hati!)
- ✅ `@if`, `@elseif`, `@else` = Conditional
- ✅ `@foreach`, `@forelse` = Loop array
- ✅ `@for`, `@while` = Loop angka
- ✅ `$loop` = Loop variable dengan properties berguna
- ✅ Passing data dengan `compact()` atau array

**Kamu sekarang bisa buat tampilan dinamis!** 🎨

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Master layout dengan @extends
- ✅ @section dan @yield
- ✅ Membuat header dan footer reusable
- ✅ Components dan slots
- ✅ Asset management (CSS, JS, images)

**Blade akan lebih powerful!** 🚀

---

## 🔗 Referensi

- 📖 [Laravel Blade Docs](https://laravel.com/docs/12.x/blade)
- 📖 [Blade Directives](https://laravel.com/docs/12.x/blade#blade-directives)
- 🎥 [Laracasts - Blade Basics](https://laracasts.com)

---

[⬅️ Bab 07: Pengenalan MVC](07-mvc-pengenalan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 09: Blade Layout & Components ➡️](09-blade-layout.md)

---

<div align="center">

**View pertama sudah jalan! Blade directives sudah dikuasai!** ✅

**Lanjut ke layout untuk code yang lebih DRY!** 🚀

</div>