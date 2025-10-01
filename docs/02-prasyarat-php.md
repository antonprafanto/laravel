# Bab 02: Prerequisites - PHP & OOP 📚

[⬅️ Bab 01: Pengenalan](01-pengenalan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 03: Instalasi Environment ➡️](03-instalasi-environment.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Mengetahui apakah kamu sudah siap belajar Laravel
- ✅ Memahami konsep OOP PHP yang wajib dikuasai
- ✅ Bisa membedakan procedural vs OOP
- ✅ Paham Class, Object, Properties, Methods, Constructor
- ✅ Paham Visibility (public, private, protected)
- ✅ Paham Namespace

---

## ⚠️ PENTING: Bab Ini adalah Foundation Check!

> **Jangan skip bab ini!** Laravel dibangun dengan OOP. Jika kamu tidak paham OOP, kamu akan stuck di tengah jalan!

**Di akhir bab ini ada QUIZ.** Jika kamu dapat nilai < 7/10, **sebaiknya belajar OOP dulu** sebelum lanjut ke Laravel.

---

## 🎯 Analogi Sederhana: OOP seperti Cetakan Kue

### Procedural Programming (Cara Lama)

Bayangkan kamu bikin kue **satu-satu manual**:
```
1. Ambil tepung untuk kue pertama
2. Tambah gula untuk kue pertama
3. Aduk untuk kue pertama
4. Panggang kue pertama
5. Ambil tepung untuk kue kedua
6. Tambah gula untuk kue kedua
7. Aduk untuk kue kedua
8. Panggang kue kedua
... dan seterusnya (cape banget!) 😫
```

### OOP (Cara Modern)

Kamu bikin **cetakan kue** dulu (**Class**), terus tinggal cetak berkali-kali (**Object**):
```
1. Buat cetakan kue (Class)
2. Cetak kue pertama (Object 1)
3. Cetak kue kedua (Object 2)
4. Cetak kue ketiga (Object 3)
... selesai! Cepat dan rapi! 🎉
```

**Intinya:** OOP itu tentang membuat "cetakan" (template) yang bisa dipakai berulang kali!

---

## 📚 Review PHP Dasar

Sebelum OOP, pastikan kamu paham PHP dasar ini:

### 1. Variables & Data Types

```php
<?php

// String
$nama = "Budi";

// Integer
$umur = 25;

// Float
$tinggi = 175.5;

// Boolean
$sudahMenikah = false;

// Array
$hobi = ["coding", "gaming", "traveling"];

// Associative Array
$mahasiswa = [
    "nama" => "Budi",
    "umur" => 25,
    "jurusan" => "Informatika"
];

echo $nama; // Output: Budi
```

---

### 2. Conditionals (If-Else)

```php
<?php

$nilai = 85;

if ($nilai >= 90) {
    echo "Grade: A";
} elseif ($nilai >= 75) {
    echo "Grade: B"; // Output: Grade: B
} else {
    echo "Grade: C";
}
```

---

### 3. Loops (Perulangan)

```php
<?php

// For loop
for ($i = 1; $i <= 5; $i++) {
    echo "Angka: $i<br>";
}

// Foreach loop
$buah = ["Apel", "Jeruk", "Mangga"];
foreach ($buah as $item) {
    echo "Buah: $item<br>";
}
```

---

### 4. Functions

```php
<?php

// MEMBUAT FUNCTION: Seperti membuat resep masakan
function hitungLuasPersegiPanjang($panjang, $lebar) {
    $luas = $panjang * $lebar;
    return $luas;
}

// MEMANGGIL FUNCTION: Seperti ikuti resep untuk masak
$hasil = hitungLuasPersegiPanjang(10, 5);
echo "Luas: $hasil"; // Output: Luas: 50
```

**Jika kamu belum paham ini semua, belajar PHP dasar dulu ya!** 📖

---

## 🏗️ Pengenalan OOP: Class & Object

### 🎯 Analogi: Class = Cetakan Kue, Object = Kue Hasil Cetakan

```php
<?php

// CLASS: Cetakan/Blueprint/Template untuk membuat Mobil
class Mobil {
    // PROPERTIES: Ciri-ciri mobil (seperti warna cetakan, ukuran cetakan)
    public $merk;
    public $warna;
    public $tahun;

    // METHOD: Aksi yang bisa dilakukan mobil (seperti fungsi kue: dimakan, dibungkus)
    public function jalan() {
        echo "Mobil {$this->merk} sedang jalan<br>";
    }

    public function info() {
        echo "Mobil {$this->merk} warna {$this->warna} tahun {$this->tahun}<br>";
    }
}

// OBJECT: Mobil hasil cetakan (mobil asli!)
$mobil1 = new Mobil();
$mobil1->merk = "Toyota Avanza";
$mobil1->warna = "Hitam";
$mobil1->tahun = 2023;

$mobil2 = new Mobil();
$mobil2->merk = "Honda Jazz";
$mobil2->warna = "Merah";
$mobil2->tahun = 2024;

// Panggil method
$mobil1->jalan(); // Output: Mobil Toyota Avanza sedang jalan
$mobil2->jalan(); // Output: Mobil Honda Jazz sedang jalan

$mobil1->info(); // Output: Mobil Toyota Avanza warna Hitam tahun 2023
$mobil2->info(); // Output: Mobil Honda Jazz warna Merah tahun 2024
```

### 🔄 Hubungan Analogi dengan Kode:

| Konsep | Analogi | Kode |
|--------|---------|------|
| **Class** | Cetakan kue | `class Mobil` |
| **Object** | Kue hasil cetakan | `$mobil1 = new Mobil()` |
| **Properties** | Ciri-ciri cetakan (warna, ukuran) | `public $merk`, `public $warna` |
| **Methods** | Fungsi kue (dimakan, dibungkus) | `public function jalan()` |
| **$this** | "Kue ini" (yang lagi kita pegang) | `$this->merk` |

---

## 🔧 Constructor: Menyiapkan Object Saat Dibuat

### 🎯 Analogi: Constructor seperti "Setup Awal" saat unboxing HP baru

Saat beli HP baru, langkah pertama:
1. Nyalakan HP
2. Pilih bahasa
3. Masukin nama kamu
4. Setup password

**Constructor** melakukan hal yang sama untuk object!

```php
<?php

class Mahasiswa {
    public $nama;
    public $npm;
    public $jurusan;

    // CONSTRUCTOR: Otomatis dijalankan saat object dibuat
    // Analogi: Setup awal HP
    public function __construct($nama, $npm, $jurusan) {
        $this->nama = $nama;
        $this->npm = $npm;
        $this->jurusan = $jurusan;

        echo "Mahasiswa baru terdaftar: {$nama}<br>";
    }

    public function perkenalan() {
        echo "Halo, nama saya {$this->nama}, NPM {$this->npm}<br>";
    }
}

// Cara LAMA (tanpa constructor) - ribet!
// $mhs1 = new Mahasiswa();
// $mhs1->nama = "Budi";
// $mhs1->npm = "123456";
// $mhs1->jurusan = "Informatika";

// Cara BARU (dengan constructor) - praktis!
$mhs1 = new Mahasiswa("Budi", "123456", "Informatika");
// Output: Mahasiswa baru terdaftar: Budi

$mhs2 = new Mahasiswa("Ani", "123457", "Sistem Informasi");
// Output: Mahasiswa baru terdaftar: Ani

$mhs1->perkenalan(); // Output: Halo, nama saya Budi, NPM 123456
```

**Lebih praktis kan?** Constructor membuat kode lebih ringkas! ✨

---

## 🔒 Visibility: Public, Private, Protected

### 🎯 Analogi: Kamar di Rumah dengan Tingkat Privasi Berbeda

Bayangkan rumah punya 3 jenis ruangan:

| Visibility | Analogi | Siapa yang Boleh Akses? |
|------------|---------|------------------------|
| **public** | Ruang tamu | Semua orang (tamu, keluarga) |
| **private** | Kamar tidur pribadi | Hanya kamu sendiri |
| **protected** | Ruang keluarga | Kamu dan keluarga dekat |

```php
<?php

class BankAccount {
    // PUBLIC: Bisa diakses dari mana saja (seperti nama di KTP)
    public $nama;

    // PRIVATE: Hanya bisa diakses dari dalam class ini (seperti PIN ATM)
    private $saldo;
    private $pin;

    // PROTECTED: Bisa diakses dari class ini dan class turunannya
    protected $nomorRekening;

    public function __construct($nama, $saldoAwal, $pin) {
        $this->nama = $nama;
        $this->saldo = $saldoAwal;
        $this->pin = $pin;
        $this->nomorRekening = rand(1000000000, 9999999999);
    }

    // PUBLIC METHOD: Untuk akses saldo (dengan validasi)
    public function lihatSaldo($pinInput) {
        if ($pinInput == $this->pin) {
            return "Saldo Anda: Rp " . number_format($this->saldo, 0, ',', '.');
        } else {
            return "PIN salah!";
        }
    }

    // PUBLIC METHOD: Untuk setor uang
    public function setor($jumlah) {
        $this->saldo += $jumlah;
        return "Berhasil setor Rp " . number_format($jumlah, 0, ',', '.');
    }
}

$rekening = new BankAccount("Budi", 1000000, "123456");

// PUBLIC: Bisa diakses langsung
echo $rekening->nama; // Output: Budi

// PRIVATE: Tidak bisa diakses langsung (akan error!)
// echo $rekening->saldo; // ❌ ERROR!
// echo $rekening->pin;   // ❌ ERROR!

// Harus pakai method public
echo $rekening->lihatSaldo("123456"); // Output: Saldo Anda: Rp 1.000.000
echo $rekening->lihatSaldo("000000"); // Output: PIN salah!

echo $rekening->setor(500000); // Output: Berhasil setor Rp 500.000
```

### 💡 Mengapa Pakai Private?

**Keamanan!** Bayangkan jika saldo bisa diubah langsung:
```php
// Tanpa private (bahaya!)
$rekening->saldo = 999999999; // Hack! Langsung kaya! 😱

// Dengan private (aman!)
// $rekening->saldo = 999999999; // ❌ ERROR! Tidak bisa!
// Harus pakai method setor() yang ada validasinya ✅
```

---

## 📦 Namespace: Mengatur File agar Tidak Bentrok

### 🎯 Analogi: Namespace seperti Alamat Lengkap

Bayangkan ada 2 orang bernama "Budi":
- Budi di Jakarta
- Budi di Bandung

**Tanpa namespace** (alamat tidak lengkap):
```
"Panggil Budi!" → Yang mana? Bingung! 😵
```

**Dengan namespace** (alamat lengkap):
```
"Panggil Budi yang di Jakarta!" → Jelas! ✅
"Panggil Budi yang di Bandung!" → Jelas! ✅
```

### Contoh Kode:

```php
<?php

// File: Jakarta/Budi.php
namespace Jakarta;

class Budi {
    public function sapa() {
        echo "Halo dari Budi Jakarta!<br>";
    }
}

// File: Bandung/Budi.php
namespace Bandung;

class Budi {
    public function sapa() {
        echo "Halo dari Budi Bandung!<br>";
    }
}

// File: main.php
// Panggil Budi Jakarta
$budiJakarta = new \Jakarta\Budi();
$budiJakarta->sapa(); // Output: Halo dari Budi Jakarta!

// Panggil Budi Bandung
$budiBandung = new \Bandung\Budi();
$budiBandung->sapa(); // Output: Halo dari Budi Bandung!

// Atau pakai 'use' untuk lebih singkat:
use Jakarta\Budi as BudiJakarta;
use Bandung\Budi as BudiBandung;

$budi1 = new BudiJakarta();
$budi2 = new BudiBandung();
```

**Di Laravel, namespace dipakai di mana-mana!**
```php
use App\Models\Post; // Namespace untuk Model
use App\Http\Controllers\PostController; // Namespace untuk Controller
```

---

## 💡 Contoh Real-World: Class Mahasiswa Lengkap

Mari kita gabungkan semua konsep OOP:

```php
<?php

// NAMESPACE: "Alamat" class ini
namespace App\Models;

// CLASS: Cetakan untuk membuat mahasiswa
class Mahasiswa {
    // PROPERTIES
    public $nama;         // PUBLIC: Bisa diakses dari luar
    private $npm;         // PRIVATE: Hanya di dalam class
    protected $jurusan;   // PROTECTED: Class ini dan turunannya
    private $ipk;

    // CONSTRUCTOR: Setup awal saat object dibuat
    public function __construct($nama, $npm, $jurusan, $ipk = 0.0) {
        $this->nama = $nama;
        $this->npm = $npm;
        $this->jurusan = $jurusan;
        $this->ipk = $ipk;

        echo "✅ Mahasiswa {$nama} berhasil didaftarkan!<br>";
    }

    // METHOD PUBLIC: Bisa dipanggil dari luar
    public function perkenalan() {
        return "Halo, nama saya {$this->nama} dari jurusan {$this->jurusan}";
    }

    public function lihatIPK() {
        return "IPK saya: " . $this->ipk;
    }

    // METHOD PUBLIC: Untuk update IPK (dengan validasi)
    public function updateIPK($ipkBaru) {
        if ($ipkBaru >= 0 && $ipkBaru <= 4) {
            $this->ipk = $ipkBaru;
            return "✅ IPK berhasil diupdate";
        } else {
            return "❌ IPK tidak valid! Harus antara 0-4";
        }
    }

    // METHOD PRIVATE: Hanya bisa dipanggil dari dalam class
    private function hitungPredikat() {
        if ($this->ipk >= 3.5) {
            return "Cumlaude";
        } elseif ($this->ipk >= 3.0) {
            return "Sangat Memuaskan";
        } elseif ($this->ipk >= 2.5) {
            return "Memuaskan";
        } else {
            return "Perlu Perbaikan";
        }
    }

    public function transkrip() {
        $predikat = $this->hitungPredikat(); // Panggil method private
        return "Nama: {$this->nama}, IPK: {$this->ipk}, Predikat: {$predikat}";
    }
}

// MEMBUAT OBJECT
$mhs1 = new Mahasiswa("Budi Santoso", "20230001", "Informatika", 3.75);
// Output: ✅ Mahasiswa Budi Santoso berhasil didaftarkan!

$mhs2 = new Mahasiswa("Ani Wijaya", "20230002", "Sistem Informasi", 3.25);
// Output: ✅ Mahasiswa Ani Wijaya berhasil didaftarkan!

// MENGGUNAKAN OBJECT
echo $mhs1->perkenalan(); // Output: Halo, nama saya Budi Santoso dari jurusan Informatika
echo $mhs1->lihatIPK(); // Output: IPK saya: 3.75
echo $mhs1->transkrip(); // Output: Nama: Budi Santoso, IPK: 3.75, Predikat: Cumlaude

// Update IPK
echo $mhs1->updateIPK(3.85); // Output: ✅ IPK berhasil diupdate
echo $mhs1->updateIPK(5.0);  // Output: ❌ IPK tidak valid! Harus antara 0-4

// Tidak bisa akses private
// echo $mhs1->npm; // ❌ ERROR!
// echo $mhs1->hitungPredikat(); // ❌ ERROR!
```

---

## 📝 Self-Assessment Quiz

**Instruksi:** Jawab pertanyaan berikut untuk cek pemahamanmu!

### Pertanyaan 1:
```php
<?php
class Mobil {
    public $merk;

    public function jalan() {
        echo "Mobil jalan";
    }
}

$mobil1 = new Mobil();
```

**Pertanyaan:** Apa yang dimaksud dengan `$mobil1`?
- A. Class
- B. Object
- C. Method
- D. Property

<details>
<summary>Lihat Jawaban</summary>
<strong>Jawaban: B. Object</strong><br>
$mobil1 adalah object yang dibuat dari class Mobil.
</details>

---

### Pertanyaan 2:
```php
<?php
class User {
    public $nama;
    private $password;
}
```

**Pertanyaan:** Apa perbedaan `public` dan `private`?
- A. Public bisa diakses dari luar class, private tidak
- B. Private lebih cepat dari public
- C. Public untuk string, private untuk integer
- D. Tidak ada perbedaan

<details>
<summary>Lihat Jawaban</summary>
<strong>Jawaban: A. Public bisa diakses dari luar class, private tidak</strong><br>
Public bisa diakses dari mana saja, private hanya dari dalam class.
</details>

---

### Pertanyaan 3:
```php
<?php
class Mahasiswa {
    public $nama;

    public function __construct($nama) {
        $this->nama = $nama;
    }
}
```

**Pertanyaan:** Apa fungsi `__construct()`?
- A. Menghapus object
- B. Membuat class
- C. Setup awal saat object dibuat
- D. Menampilkan data

<details>
<summary>Lihat Jawaban</summary>
<strong>Jawaban: C. Setup awal saat object dibuat</strong><br>
Constructor otomatis dijalankan saat object dibuat untuk setup awal.
</details>

---

### Pertanyaan 4:
```php
<?php
class Hewan {
    public $nama;

    public function suara() {
        echo "Hewan bersuara";
    }
}

$kucing = new Hewan();
$kucing->nama = "Kitty";
$kucing->suara();
```

**Pertanyaan:** Apa output kode di atas?
- A. Kitty
- B. Hewan bersuara
- C. Error
- D. Tidak ada output

<details>
<summary>Lihat Jawaban</summary>
<strong>Jawaban: B. Hewan bersuara</strong><br>
Method suara() akan menampilkan "Hewan bersuara".
</details>

---

### Pertanyaan 5:
```php
<?php
class Kalkulator {
    public function tambah($a, $b) {
        return $a + $b;
    }
}
```

**Pertanyaan:** Bagaimana cara memanggil method `tambah()`?
- A. `Kalkulator::tambah(5, 3)`
- B. `$kalkulator = new Kalkulator(); $kalkulator->tambah(5, 3);`
- C. `tambah(5, 3)`
- D. `Kalkulator->tambah(5, 3)`

<details>
<summary>Lihat Jawaban</summary>
<strong>Jawaban: B. $kalkulator = new Kalkulator(); $kalkulator->tambah(5, 3);</strong><br>
Harus buat object dulu, baru panggil method-nya.
</details>

---

### Pertanyaan 6-10

Buat sendiri 5 pertanyaan lagi untuk menguji pemahamanmu tentang:
- Namespace
- $this keyword
- Properties vs Methods
- Public vs Private vs Protected
- Perbedaan Class vs Object

**Scoring:**
- 9-10 benar: Excellent! Lanjut ke chapter berikutnya! ✅
- 7-8 benar: Good! Tapi review lagi yang salah
- 5-6 benar: Cukup, tapi harus belajar OOP lebih dalam dulu
- < 5 benar: Belajar OOP dulu sebelum lanjut Laravel

---

## 📚 Resource Belajar OOP

Jika belum paham OOP, belajar dulu di sini:

### Video Tutorial (Bahasa Indonesia):
- 🎥 [Web Programming UNPAS - OOP PHP](https://youtube.com/playlist?list=PLFIM0718LjIWvxxll-6wLXrC_16h_Bl_p)
- 🎥 [Programmer Zaman Now - PHP OOP](https://youtube.com/watch?v=5lqIo-PSVc4)

### Artikel:
- 📖 [PHP.net - OOP Tutorial](https://www.php.net/manual/en/language.oop5.php)
- 📖 [Petani Kode - OOP PHP untuk Pemula](https://www.petanikode.com/php-oop/)

**Estimasi waktu belajar OOP:** 3-7 hari (tergantung background)

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Class** = Cetakan/template untuk membuat object
- ✅ **Object** = Hasil cetakan dari class
- ✅ **Properties** = Ciri-ciri/atribut object
- ✅ **Methods** = Aksi/fungsi yang bisa dilakukan object
- ✅ **Constructor** = Setup awal saat object dibuat
- ✅ **Visibility**: public (semua bisa akses), private (hanya dalam class), protected (class + turunan)
- ✅ **Namespace** = Alamat lengkap agar tidak bentrok
- ✅ **$this** = Mengacu pada object yang sedang digunakan

---

## ⚠️ Checkpoint: Apakah Kamu Siap?

**Lanjut ke chapter berikutnya jika:**
- ✅ Quiz dapat nilai minimal 7/10
- ✅ Paham konsep Class, Object, Properties, Methods
- ✅ Paham Constructor dan Visibility
- ✅ Bisa membuat Class sederhana sendiri

**Belajar OOP dulu jika:**
- ❌ Quiz dapat nilai < 7/10
- ❌ Masih bingung dengan konsep OOP
- ❌ Belum pernah bikin Class sendiri

**Ingat:** Foundation yang kuat = Belajar Laravel lebih mudah! 💪

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ Pengenalan Terminal/Command Line (jangan takut!)
- ✅ Instalasi Laragon/XAMPP
- ✅ Instalasi Composer
- ✅ Instalasi VS Code + Extensions
- ✅ Setup environment development yang nyaman

**Get ready untuk setup development environment!** 🛠️

---

## 🔗 Referensi

- 📖 [PHP Manual - OOP](https://www.php.net/manual/en/language.oop5.php)
- 📖 [PHP Manual - Namespaces](https://www.php.net/manual/en/language.namespaces.php)
- 🎥 [Web Programming UNPAS - OOP PHP](https://youtube.com/playlist?list=PLFIM0718LjIWvxxll-6wLXrC_16h_Bl_p)

---

[⬅️ Bab 01: Pengenalan](01-pengenalan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 03: Instalasi Environment ➡️](03-instalasi-environment.md)

---

<div align="center">

**Paham OOP? Ayo lanjut setup environment!** 🚀

</div>