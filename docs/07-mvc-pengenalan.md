# Bab 07: Pengenalan MVC 🏛️

[⬅️ Bab 06: Routing Lanjutan](06-routing-lanjutan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 08: View & Blade Dasar ➡️](08-view-blade-dasar.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu arsitektur MVC
- ✅ Mengerti analogi MVC sebagai Restoran dengan detail
- ✅ Paham peran Model, View, dan Controller
- ✅ Mengerti flow request-response di Laravel
- ✅ Tahu mengapa MVC itu penting
- ✅ **Siap untuk coding MVC di chapter berikutnya**

⚠️ **Penting:** Bab ini fokus pada KONSEP, belum coding! Pahami dulu konsepnya, baru praktek.

---

## 🎯 Analogi Sederhana: MVC seperti Restoran Padang

Bayangkan kamu buka **Restoran Padang**. Ada 3 bagian utama:

### 🍳 **Model = DAPUR & GUDANG**

**Tugas:**
- Simpan bahan makanan (data)
- Masak makanan (proses data)
- Ambil makanan dari gudang
- Update stok bahan

**Analogi:**
```
Customer: "Saya mau Rendang"
↓
Dapur cek gudang: "Ada daging sapi?"
↓
Kalau ada: Masak rendang
Kalau tidak ada: "Maaf stok habis"
```

**Di Laravel:**
- Model = File PHP yang bicara dengan DATABASE
- Ambil data, simpan data, update data, hapus data
- Contoh: `User.php`, `Post.php`, `Product.php`

---

### 🎨 **View = RUANG MAKAN & PIRING SAJI**

**Tugas:**
- Tempat makanan disajikan
- Harus cantik dan nyaman
- Customer lihat dan nikmati makanan di sini

**Analogi:**
```
Makanan dari dapur → Ditata di piring cantik → Disajikan ke customer
```

**Di Laravel:**
- View = File HTML yang dilihat user
- Tampilan website (halaman, form, tabel)
- File `.blade.php` di folder `resources/views/`
- Contoh: `home.blade.php`, `about.blade.php`

---

### 👔 **Controller = PELAYAN**

**Tugas:**
- Terima pesanan dari customer (request)
- Teruskan pesanan ke dapur (model)
- Ambil makanan dari dapur
- Sajikan ke customer (view)
- Koordinator antara customer, dapur, dan ruang makan

**Analogi:**
```
1. Customer pesan: "Rendang 1 porsi"
2. Pelayan catat pesanan
3. Pelayan ke dapur: "Masak Rendang 1"
4. Dapur masak dan kasih ke pelayan
5. Pelayan bawa ke customer
6. Customer makan dengan senang
```

**Di Laravel:**
- Controller = File PHP yang koordinasi semua
- Terima request dari route
- Minta data ke Model
- Kirim data ke View
- Contoh: `PostController.php`, `UserController.php`

---

## 📊 Diagram MVC Restoran

```
┌─────────────────────────────────────────────────────────┐
│                     🧑 CUSTOMER                          │
│                   (User / Browser)                       │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
            "Saya mau Rendang!"
                     │
┌────────────────────▼────────────────────────────────────┐
│                  📋 BUKU MENU                            │
│                  (routes/web.php)                        │
│  - Cek: Ada menu Rendang?                               │
│  - Panggil: RendangController                           │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌────────────────────▼────────────────────────────────────┐
│              👔 PELAYAN (Controller)                     │
│              RendangController                           │
│                                                          │
│  function tampilkanRendang() {                          │
│    1. "Ke dapur, ambil Rendang"                         │
│    2. Terima Rendang dari dapur                         │
│    3. "Sajikan ke customer"                             │
│  }                                                       │
└─────┬──────────────────────────────────────────┬────────┘
      │                                          │
      ▼ (minta data)                            ▼ (kirim data)
┌─────┴──────────────┐              ┌───────────┴─────────┐
│  🍳 DAPUR (Model)  │              │ 🎨 PIRING (View)    │
│   Rendang.php      │              │  rendang.blade.php  │
│                    │              │                     │
│ - Cek gudang       │              │ <h1>Rendang</h1>    │
│ - Ambil daging     │              │ <img src="...">     │
│ - Masak            │              │ <p>Rp 35.000</p>    │
│ - Return Rendang   │              │                     │
└────────┬───────────┘              └──────────┬──────────┘
         │                                     │
         ▼                                     ▼
┌────────┴─────────────────────────────────────┴──────────┐
│               📦 GUDANG (Database)                       │
│          - Daging Sapi: 50 kg                           │
│          - Bumbu Rendang: 10 pak                        │
│          - ...                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 Flow Request-Response (Detail!)

Mari kita ikuti perjalanan request dari awal sampai akhir:

### Skenario: User ingin lihat halaman "About Us"

**Step 1: Customer Datang**
```
Browser: http://localhost:8000/about
↓
"Saya mau ke halaman About"
```

**Step 2: Pintu Masuk**
```
public/index.php (entry point)
↓
"Ada tamu! Teruskan ke sistem Laravel"
```

**Step 3: Buku Menu (Routes)**
```
routes/web.php
↓
Route::get('/about', [AboutController::class, 'index']);
↓
"Oh, about ya? Panggil AboutController method index()"
```

**Step 4: Pelayan Dipanggil (Controller)**
```
AboutController@index
↓
function index() {
    // 1. Ambil data company dari Model
    $company = Company::first();

    // 2. Kirim data ke View
    return view('about', ['company' => $company]);
}
```

**Step 5: Ke Dapur (Model) - Optional**
```
Company.php (Model)
↓
Eloquent query ke database
↓
SELECT * FROM companies LIMIT 1
↓
Return data company
```

**Step 6: Ke Ruang Makan (View)**
```
resources/views/about.blade.php
↓
<h1>About {{ $company->name }}</h1>
<p>{{ $company->description }}</p>
↓
Generate HTML
```

**Step 7: Sajikan ke Customer**
```
HTML response dikirim ke browser
↓
Browser render HTML
↓
User lihat halaman About yang cantik! 🎉
```

---

## 💡 Mengapa Pakai MVC?

### ❌ Tanpa MVC (Spaghetti Code)

**File: about.php**
```php
<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "mydb");

// Query
$sql = "SELECT * FROM companies LIMIT 1";
$result = mysqli_query($conn, $sql);
$company = mysqli_fetch_assoc($result);

// HTML + PHP campur aduk (berantakan!)
?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us</title>
    <style>
        body { font-family: Arial; }
        h1 { color: blue; }
    </style>
</head>
<body>
    <h1>About <?php echo $company['name']; ?></h1>
    <p><?php echo $company['description']; ?></p>

    <?php
    // Logic lagi di tengah HTML
    if ($company['active']) {
        echo "<p>Company is active</p>";
    }
    ?>
</body>
</html>
<?php
mysqli_close($conn);
?>
```

**Masalah:**
- ❌ Database, logic, HTML campur jadi satu
- ❌ Susah dibaca
- ❌ Susah maintenance
- ❌ Tidak bisa reuse code
- ❌ Testing susah
- ❌ Kalau ada bug, bingung cari di mana

**Analogi:**
```
Seperti dapur, ruang makan, kasir jadi satu ruangan
→ Berantakan dan bau! 😵
```

---

### ✅ Dengan MVC (Terorganisir)

**1. Model (Company.php):**
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    // Fungsi untuk ambil company aktif
    public static function getActive()
    {
        return self::where('active', true)->first();
    }
}
```

**2. Controller (AboutController.php):**
```php
<?php
namespace App\Http\Controllers;

use App\Models\Company;

class AboutController extends Controller
{
    public function index()
    {
        // Ambil data dari Model
        $company = Company::getActive();

        // Kirim ke View
        return view('about', compact('company'));
    }
}
```

**3. View (about.blade.php):**
```html
<!DOCTYPE html>
<html>
<head>
    <title>About Us</title>
</head>
<body>
    <h1>About {{ $company->name }}</h1>
    <p>{{ $company->description }}</p>

    @if($company->active)
        <p>Company is active</p>
    @endif
</body>
</html>
```

**4. Route (web.php):**
```php
Route::get('/about', [AboutController::class, 'index']);
```

**Keuntungan:**
- ✅ Terpisah jelas: Data, Logic, Tampilan
- ✅ Mudah dibaca dan dipahami
- ✅ Mudah maintenance
- ✅ Code reusable
- ✅ Testing lebih mudah
- ✅ Kalau ada bug, langsung tahu di mana

**Analogi:**
```
Dapur terpisah, ruang makan terpisah, kasir terpisah
→ Rapi dan profesional! ✨
```

---

## 📋 Perbandingan Lengkap

| Aspek | Tanpa MVC | Dengan MVC |
|-------|-----------|------------|
| **Struktur** | Campur aduk | Terpisah rapi |
| **Readability** | Susah dibaca | Mudah dibaca |
| **Maintenance** | Susah update | Mudah update |
| **Reusability** | Susah reuse | Mudah reuse |
| **Testing** | Hampir impossible | Mudah test |
| **Collaboration** | Susah kerja tim | Mudah kerja tim |
| **Scalability** | Susah scale | Mudah scale |
| **Learning Curve** | Mudah awal, susah lama-lama | Susah awal, mudah lama-lama |

---

## 🎭 Analogi Lain: MVC seperti Tim Produksi Film

**Model** = **Scriptwriter & Data Manager**
- Punya semua data cerita
- Karakter, plot, dialog
- Update script kalau ada perubahan

**View** = **Kameraman & Editor**
- Tangkap scene (ambil data)
- Edit jadi cantik
- Final result yang dilihat penonton

**Controller** = **Sutradara**
- Koordinasi semua tim
- "Action!" (trigger)
- "Ambil scene ini dari scriptwriter"
- "Edit dengan style ini"
- "Cut! OK, scene berikutnya"

---

## 🏗️ Struktur Folder MVC di Laravel

```
app/
├── Http/
│   └── Controllers/          👔 PELAYAN
│       ├── AboutController.php
│       ├── PostController.php
│       └── UserController.php
│
├── Models/                   🍳 DAPUR
│   ├── User.php
│   ├── Post.php
│   └── Company.php
│
resources/
└── views/                    🎨 RUANG MAKAN
    ├── about.blade.php
    ├── home.blade.php
    └── posts/
        ├── index.blade.php
        └── show.blade.php

routes/
└── web.php                   📋 BUKU MENU
```

---

## 💭 Mental Model: Hafal Alur Ini!

```
1. User buka URL
   ↓
2. Route cek: "URL ini masuk mana?"
   ↓
3. Route panggil Controller
   ↓
4. Controller minta data ke Model (optional)
   ↓
5. Model query database (optional)
   ↓
6. Model return data ke Controller
   ↓
7. Controller kirim data ke View
   ↓
8. View render HTML
   ↓
9. HTML dikirim ke browser
   ↓
10. User lihat halaman! 🎉
```

**Hafal alur ini = Kunci paham Laravel!**

---

## 🎯 Kapan Pakai Model?

**Pakai Model kalau:**
- ✅ Butuh data dari database
- ✅ Mau simpan data ke database
- ✅ Mau update atau hapus data
- ✅ Query kompleks

**Tidak pakai Model kalau:**
- ✅ Halaman statis (About, Contact)
- ✅ Tidak ada interaksi dengan database
- ✅ Cuma return teks atau view simple

**Contoh tanpa Model:**
```php
Route::get('/hello', function () {
    return view('hello'); // Langsung return view
});
```

**Contoh dengan Model:**
```php
Route::get('/posts', [PostController::class, 'index']);
// Controller akan pakai Model untuk ambil data posts dari DB
```

---

## 📝 Latihan Pemahaman (No Coding Yet!)

### Latihan 1: Identifikasi Komponen

**Skenario:** User ingin lihat daftar produk di toko online

**Pertanyaan:**
1. Apa tugas **Model**?
2. Apa tugas **Controller**?
3. Apa tugas **View**?

<details>
<summary>Lihat Jawaban</summary>

**Model (Product.php):**
- Query database: `SELECT * FROM products`
- Return data produk ke Controller

**Controller (ProductController.php):**
- Terima request dari route
- Panggil Model untuk ambil data produk
- Kirim data ke View

**View (products/index.blade.php):**
- Terima data produk
- Loop dan tampilkan dalam HTML
- Render tampilan yang cantik
</details>

---

### Latihan 2: Flow Request

**Skenario:** User klik button "Tambah ke Keranjang"

**Pertanyaan:** Urutkan alur yang benar!

A. View render halaman success
B. Model simpan data ke tabel `cart`
C. User klik button
D. Route panggil `CartController@add`
E. Controller validasi dan panggil Model

<details>
<summary>Lihat Jawaban</summary>

**Urutan yang benar:**
1. C - User klik button
2. D - Route panggil CartController@add
3. E - Controller validasi dan panggil Model
4. B - Model simpan data ke tabel cart
5. A - View render halaman success
</details>

---

### Latihan 3: Analogi

**Pertanyaan:** Jika MVC itu restoran, maka:
- Database itu apa?
- Route itu apa?
- Browser itu apa?

<details>
<summary>Lihat Jawaban</summary>

- **Database** = Gudang penyimpanan (freezer, rak bumbu)
- **Route** = Buku menu / daftar pesanan
- **Browser** = Customer yang pesan makanan
</details>

---

## 📖 Summary

Di bab ini kamu sudah memahami:

- ✅ **MVC** = Model, View, Controller
- ✅ **Model** = Dapur (bicara dengan database)
- ✅ **View** = Ruang Makan (tampilan untuk user)
- ✅ **Controller** = Pelayan (koordinator)
- ✅ **Flow**: Route → Controller → Model → Controller → View → User
- ✅ **Mengapa MVC**: Code terorganisir, mudah maintenance, scalable
- ✅ MVC bukan untuk halaman simple, tapi untuk aplikasi besar

**Konsep MVC adalah fondasi Laravel!** 🏗️

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ **Mulai praktek!** Buat View pertama
- ✅ Belajar Blade template engine
- ✅ Passing data dari Route ke View
- ✅ Blade directives: @if, @foreach
- ✅ Render HTML dinamis

**Saatnya coding View!** 🎨

---

## 🔗 Referensi

- 📖 [MVC Pattern Explained](https://www.tutorialspoint.com/mvc_framework/mvc_framework_introduction.htm)
- 📖 [Laravel Architecture Concepts](https://laravel.com/docs/12.x/lifecycle)
- 📖 [Understanding MVC](https://www.codecademy.com/article/mvc)
- 🎥 [Laracasts - MVC Explained](https://laracasts.com)

---

[⬅️ Bab 06: Routing Lanjutan](06-routing-lanjutan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 08: View & Blade Dasar ➡️](08-view-blade-dasar.md)

---

<div align="center">

**Paham konsep MVC? Perfect!** ✅

**Sekarang kita siap praktek buat View!** 🎨

**MVC bukan monster, tapi teman baik untuk organisir code!** 💪

</div>