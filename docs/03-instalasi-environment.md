# Bab 03: Instalasi Environment 🛠️

[⬅️ Bab 02: Prerequisites PHP](02-prasyarat-php.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 04: Project Laravel Pertama ➡️](04-project-pertama.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Tidak takut lagi dengan Terminal/Command Line
- ✅ Bisa navigasi folder dengan command line
- ✅ Berhasil install Laragon/XAMPP
- ✅ Berhasil install Composer
- ✅ Berhasil install VS Code + extensions
- ✅ Siap untuk membuat project Laravel pertama

---

## 📋 Yang Akan Kita Install

| Software | Fungsi | Analogi |
|----------|--------|---------|
| **Laragon/XAMPP** | Web server + MySQL + PHP | Rumah untuk aplikasi Laravel |
| **Composer** | Package manager PHP | GoFood untuk download package |
| **VS Code** | Code editor | Mesin ketik super canggih |
| **VS Code Extensions** | Plugin untuk VS Code | Aksesori tambahan mesin ketik |

**Estimasi waktu:** 1-2 jam (tergantung kecepatan internet)

---

## 💻 Bagian 1: Pengenalan Terminal/Command Line

### 🎯 Analogi: Terminal seperti "Juru Ketik yang Super Cepat"

**GUI (Graphical User Interface)** = Pakai mouse untuk klik-klik
```
📁 Klik folder Documents
📁 Klik kanan > New Folder
⌨️ Ketik nama folder "laravel-app"
✅ Enter
```

**CLI (Command Line Interface)** = Ketik perintah
```
⌨️ Ketik: cd Documents
⌨️ Ketik: mkdir laravel-app
✅ Enter - Selesai!
```

**Lebih cepat kan?** Sekali kamu terbiasa, terminal jauh lebih cepat! ⚡

---

### 📚 Mengapa Harus Pakai Terminal?

Laravel dan tools modern **mengharuskan** pakai terminal:
- ✅ Install Laravel via Composer
- ✅ Menjalankan server (`php artisan serve`)
- ✅ Membuat controller, model, migration
- ✅ Menjalankan migration database
- ✅ Dan masih banyak lagi!

**Jangan takut!** Terminal itu mudah kok! 💪

---

### 🚀 Cara Membuka Terminal

#### Windows:
1. **Command Prompt (CMD)**
   - Tekan `Win + R`
   - Ketik `cmd`
   - Enter

2. **PowerShell** (Recommended)
   - Tekan `Win + X`
   - Pilih "Windows PowerShell" atau "Terminal"

3. **Git Bash** (Jika sudah install Git)
   - Klik kanan di folder
   - Pilih "Git Bash Here"

#### Mac:
- Tekan `Cmd + Space`
- Ketik "Terminal"
- Enter

#### Linux:
- Tekan `Ctrl + Alt + T`

---

### 🎓 Command Dasar Terminal (Baby Steps!)

#### 1. Melihat Folder Saat Ini

**Windows (CMD/PowerShell):**
```bash
cd
```

**Mac/Linux:**
```bash
pwd
```

**Output contoh:**
```
C:\Users\Budi
```

**Analogi:** "Saya sekarang ada di mana?"

---

#### 2. Lihat Isi Folder

**Windows:**
```bash
dir
```

**Mac/Linux:**
```bash
ls
```

**Output contoh:**
```
Documents
Downloads
Pictures
Desktop
```

**Analogi:** "Apa saja isi folder ini?"

---

#### 3. Pindah ke Folder Lain

```bash
cd Documents
```

**Analogi:** "Masuk ke folder Documents"

**Kembali ke folder sebelumnya:**
```bash
cd ..
```

**Analogi:** "Keluar dari folder ini"

---

#### 4. Membuat Folder Baru

```bash
mkdir nama-folder
```

**Contoh:**
```bash
mkdir project-laravel
```

**Analogi:** "Bikin folder baru namanya 'project-laravel'"

---

#### 5. Cek Versi Software

```bash
php --version
composer --version
```

**Analogi:** "Cek apakah PHP dan Composer sudah terinstall"

---

### 📝 Latihan Terminal (5 menit)

Coba ketik command ini satu per satu:

```bash
# 1. Lihat folder saat ini
cd

# 2. Pindah ke Documents
cd Documents

# 3. Buat folder baru
mkdir belajar-laravel

# 4. Masuk ke folder tersebut
cd belajar-laravel

# 5. Lihat folder saat ini
cd
```

**Berhasil?** Selamat! Kamu sudah bisa pakai terminal! 🎉

---

## 🏗️ Bagian 2: Instalasi Laragon (Windows - RECOMMENDED)

### 🎯 Kenapa Laragon?

| Kelebihan | Penjelasan |
|-----------|------------|
| **All-in-one** | PHP + MySQL + Apache dalam 1 installer |
| **Mudah** | Tinggal klik-klik, no ribet! |
| **Cepat** | Start/stop server super cepat |
| **Isolated** | Tidak ganggu sistem Windows |
| **Pretty URLs** | Bisa akses `http://myapp.test` |

---

### 📥 Step 1: Download Laragon

1. Buka [https://laragon.org/download/](https://laragon.org/download/)
2. Download **Laragon Full** (yang sudah include PHP, MySQL, dll)
3. Ukuran: ~200 MB
4. Tunggu sampai selesai download

![Screenshot placeholder](../assets/images/03-laragon-download.png)

---

### ⚙️ Step 2: Install Laragon

1. Double-click file installer yang sudah didownload
2. **Destination folder**: Default `C:\laragon` (recommended)
3. Centang semua options:
   - ✅ Add Laragon to PATH
   - ✅ Auto start Laragon
   - ✅ Run Laragon
4. Klik **Install**
5. Tunggu proses instalasi (2-5 menit)
6. Klik **Finish**

![Screenshot placeholder](../assets/images/03-laragon-install.png)

---

### ✅ Step 3: Test Laragon

1. **Laragon otomatis jalan** setelah instalasi
2. Klik **Start All** (button hijau)
3. Tunggu sampai Apache dan MySQL running (icon jadi hijau)
4. Buka browser, ketik: `http://localhost`
5. Jika muncul halaman Laragon → **Berhasil!** 🎉

![Screenshot placeholder](../assets/images/03-laragon-localhost.png)

---

### 🔧 Step 4: Cek PHP dan Composer

Buka Terminal (dari Laragon):
1. Klik **Menu** di Laragon
2. Pilih **Terminal**
3. Ketik:

```bash
php --version
```

**Output seharusnya:**
```
PHP 8.2.x (cli) (built: ...)
```

```bash
composer --version
```

**Output seharusnya:**
```
Composer version 2.x.x
```

**Kedua command berhasil?** Perfect! Laragon sudah siap! ✅

---

## 🔧 Alternatif: Instalasi XAMPP (Jika Tidak Pakai Laragon)

### 📥 Step 1: Download XAMPP

1. Buka [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download versi **PHP 8.2 atau lebih baru**
3. Pilih sesuai OS (Windows/Mac/Linux)
4. Ukuran: ~150 MB

---

### ⚙️ Step 2: Install XAMPP

1. Double-click installer
2. Pilih komponen:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
3. Install di `C:\xampp` (Windows) atau `/Applications/XAMPP` (Mac)
4. Klik **Next** sampai selesai

---

### ✅ Step 3: Start XAMPP

1. Buka **XAMPP Control Panel**
2. Klik **Start** di Apache
3. Klik **Start** di MySQL
4. Jika sudah hijau → berhasil!
5. Test: Buka `http://localhost` di browser

---

### 📦 Step 4: Install Composer (XAMPP tidak include Composer)

#### Windows:
1. Download [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)
2. Jalankan installer
3. **PHP Path**: Pilih `C:\xampp\php\php.exe`
4. Klik **Install**
5. Finish

#### Mac/Linux:
```bash
# Download dan install
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Pindahkan ke folder global
sudo mv composer.phar /usr/local/bin/composer

# Test
composer --version
```

---

## 📝 Bagian 3: Instalasi VS Code

### 🎯 Kenapa VS Code?

| Kelebihan | Penjelasan |
|-----------|------------|
| **Gratis** | Open-source, bebas pakai selamanya |
| **Ringan** | Tidak berat seperti IDE lain |
| **Extensions** | Banyak plugin untuk Laravel |
| **Integrated Terminal** | Terminal langsung di editor |
| **Populer** | Banyak tutorial pakai VS Code |

---

### 📥 Step 1: Download VS Code

1. Buka [https://code.visualstudio.com/](https://code.visualstudio.com/)
2. Download versi sesuai OS
3. Ukuran: ~80 MB

---

### ⚙️ Step 2: Install VS Code

**Windows:**
1. Double-click installer
2. Centang semua options:
   - ✅ Add "Open with Code" to context menu
   - ✅ Add to PATH
3. Klik **Install**
4. Finish

**Mac:**
1. Buka file `.dmg`
2. Drag VS Code ke folder Applications
3. Done!

---

### 🔌 Step 3: Install Extensions Wajib

Buka VS Code, tekan `Ctrl+Shift+X` (atau `Cmd+Shift+X` di Mac) untuk buka Extensions.

Install extensions ini satu per satu:

#### 1. **Laravel Extension Pack**
- **ID:** `onecentlin.laravel-extension-pack`
- **Fungsi:** Bundle lengkap untuk Laravel
- Cara install: Cari "Laravel Extension Pack" → Install

#### 2. **PHP Intelephense**
- **ID:** `bmewburn.vscode-intelephense-client`
- **Fungsi:** Autocomplete PHP yang powerful
- **PENTING:** Disable extension PHP bawaan VS Code!
  - Search "PHP" di extensions
  - Disable "PHP Language Features" bawaan VS Code

#### 3. **Laravel Blade Snippets**
- **ID:** `onecentlin.laravel-blade`
- **Fungsi:** Autocomplete untuk Blade template

#### 4. **Laravel Blade Spacer**
- **ID:** `austenc.laravel-blade-spacer`
- **Fungsi:** Format Blade code otomatis

#### 5. **Laravel Snippets**
- **ID:** `onecentlin.laravel5-snippets`
- **Fungsi:** Shortcut untuk kode Laravel

#### 6. **DotENV**
- **ID:** `mikestead.dotenv`
- **Fungsi:** Syntax highlighting untuk file .env

![Screenshot placeholder](../assets/images/03-vscode-extensions.png)

---

### ⚙️ Step 4: Konfigurasi VS Code (Optional tapi Recommended)

Tekan `Ctrl+,` (atau `Cmd+,` di Mac) untuk buka Settings.

**Settings yang direkomendasikan:**

```json
{
    // Format otomatis saat save
    "editor.formatOnSave": true,

    // Tab size 4 spaces (Laravel standard)
    "editor.tabSize": 4,

    // Auto save
    "files.autoSave": "afterDelay",

    // Font size
    "editor.fontSize": 14,

    // Line height
    "editor.lineHeight": 22,

    // PHP Intelephense
    "php.suggest.basic": false,
    "intelephense.diagnostics.undefinedTypes": false
}
```

---

## ✅ Bagian 4: Verifikasi Semua Instalasi

Buka Terminal (di VS Code atau Laragon), ketik command berikut:

### 1. Cek PHP
```bash
php --version
```

**Output yang diharapkan:**
```
PHP 8.2.x atau lebih baru ✅
```

---

### 2. Cek Composer
```bash
composer --version
```

**Output yang diharapkan:**
```
Composer version 2.x.x ✅
```

---

### 3. Cek MySQL (jika pakai Laragon/XAMPP)
```bash
mysql --version
```

**Output yang diharapkan:**
```
mysql Ver 8.x.x ✅
```

---

### 4. Test buat folder dengan Terminal
```bash
cd Documents
mkdir test-laravel-env
cd test-laravel-env
```

**Berhasil tanpa error?** Perfect! ✅

---

## ⚠️ Troubleshooting

### Problem 1: "php is not recognized"

**Solusi Windows:**
1. Tambahkan PHP ke PATH:
   - Buka "Environment Variables"
   - Edit PATH
   - Tambahkan: `C:\laragon\bin\php\php-8.2` (sesuaikan versi)
2. Restart terminal
3. Test: `php --version`

**Solusi Mac:**
```bash
export PATH="/Applications/XAMPP/bin:$PATH"
echo 'export PATH="/Applications/XAMPP/bin:$PATH"' >> ~/.zshrc
```

---

### Problem 2: "composer is not recognized"

**Solusi:** Install ulang Composer dan pastikan centang "Add to PATH"

---

### Problem 3: Port 80 sudah dipakai

**Penyebab:** Ada aplikasi lain pakai port 80 (Skype, IIS, dll)

**Solusi:**
1. Tutup aplikasi yang pakai port 80
2. Atau ganti port Apache di Laragon/XAMPP ke 8080

---

### Problem 4: Apache tidak bisa start di XAMPP

**Solusi:**
1. Buka Task Manager
2. Kill process "httpd.exe" atau "nginx.exe"
3. Start Apache lagi

---

### Problem 5: VS Code extension tidak jalan

**Solusi:**
1. Restart VS Code
2. Atau reload window: `Ctrl+Shift+P` → "Reload Window"

---

## 📖 Summary

Di bab ini kamu sudah:

- ✅ Paham terminal dan command dasar (cd, ls/dir, mkdir)
- ✅ Install Laragon/XAMPP (web server + MySQL + PHP)
- ✅ Install Composer (package manager)
- ✅ Install VS Code + extensions Laravel
- ✅ Verifikasi semua tools sudah jalan dengan benar

**Environment development kamu sekarang sudah siap!** 🎉

---

## 💪 Challenge

Sebelum lanjut ke chapter berikutnya, coba:

1. Buka Terminal
2. Navigasi ke folder Documents
3. Buat folder "laravel-projects"
4. Masuk ke folder tersebut
5. Ketik: `composer --version` dan `php --version`

**Command lengkapnya:**
```bash
cd Documents
mkdir laravel-projects
cd laravel-projects
composer --version
php --version
```

**Berhasil?** Kamu siap membuat project Laravel pertama! 🚀

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ Membuat project Laravel pertama via Composer
- ✅ Menjalankan Laravel development server
- ✅ Membuka project di VS Code
- ✅ Menjelajahi struktur folder Laravel
- ✅ Memahami flow request di Laravel

**Akhirnya kita mulai coding Laravel!** 🎉

---

## 🔗 Referensi

- 🌐 [Laragon Official](https://laragon.org)
- 🌐 [XAMPP Official](https://www.apachefriends.org)
- 🌐 [Composer Official](https://getcomposer.org)
- 🌐 [VS Code Official](https://code.visualstudio.com)
- 📖 [Laravel System Requirements](https://laravel.com/docs/12.x/installation#server-requirements)

---

[⬅️ Bab 02: Prerequisites PHP](02-prasyarat-php.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 04: Project Laravel Pertama ➡️](04-project-pertama.md)

---

<div align="center">

**Environment sudah siap? Ayo buat project Laravel pertama!** 🚀

</div>