# Bab 18: Form & Validasi 📋

[⬅️ Bab 17: Praktik To-Do List](17-praktik-todo.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 19: Project Blog CRUD ➡️](19-project-blog.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami pentingnya validasi form
- ✅ Bisa implement @csrf token untuk security
- ✅ Menguasai validation rules Laravel (required, max, email, dll)
- ✅ Bisa menampilkan error messages yang user-friendly
- ✅ Paham old() helper untuk preserve input saat error
- ✅ Bisa membuat custom error messages
- ✅ Menguasai Form Request classes untuk validasi kompleks

---

## 🎯 Analogi Sederhana: Validasi = Satpam Formulir

### Analogi: Form Validation = Satpam di Kantor

Bayangkan kamu mau daftar SIM di kantor polisi:

```
👤 Kamu datang dengan formulir
   ↓
🛡️ SATPAM (Validator) cek formulir:
   ├── ❌ Nama kosong? → "Nama wajib diisi!"
   ├── ❌ Email salah format? → "Email tidak valid!"
   ├── ❌ Umur < 17 tahun? → "Umur minimal 17 tahun!"
   └── ✅ Semua oke? → Boleh masuk!
```

**Tanpa Validasi (Bahaya!):**
```php
// 😱 User bisa kirim data apa aja!
- Email: "bukan-email"
- Password: "" (kosong)
- Age: -5
- SQL Injection: "'; DROP TABLE users--"
```

**Dengan Validasi (Aman!):**
```php
// ✅ Data dijaga ketat
- Email: Harus format email valid
- Password: Minimal 8 karakter
- Age: Harus angka 17-100
- SQL Injection: Otomatis di-escape!
```

---

## 📚 Bagian 1: CSRF Protection

### Apa itu CSRF?

**CSRF** = Cross-Site Request Forgery (serangan hacker!)

**Analogi:** Seperti **pemalsuan tanda tangan** di dokumen penting.

---

### Skenario Serangan CSRF

**Tanpa CSRF Protection:**

```
1. Kamu login di bank.com
2. Hacker kirim email dengan link jahat
3. Kamu klik link → Otomatis kirim request ke bank.com
4. Bank pikir request dari kamu (karena kamu login)
5. Uang kamu ditransfer ke hacker! 💸😱
```

**Dengan CSRF Protection:**

```
1. Kamu login di bank.com
2. Bank kasih kamu TOKEN rahasia
3. Setiap request harus sertakan TOKEN
4. Hacker tidak punya TOKEN → Request ditolak! ✅
```

---

### @csrf Token di Laravel

**Semua form POST/PUT/DELETE WAJIB pakai `@csrf`!**

```blade
<form action="/tasks" method="POST">
    @csrf {{-- 🔐 CSRF Token (WAJIB!) --}}

    <input type="text" name="title">
    <button type="submit">Submit</button>
</form>
```

**Output HTML:**
```html
<input type="hidden" name="_token" value="BxDg7Kj...random...">
```

**Laravel otomatis cek token ini di setiap request!** 🛡️

---

### Apa yang Terjadi Jika Lupa @csrf?

```blade
<form action="/tasks" method="POST">
    {{-- ❌ Lupa @csrf --}}
    <input type="text" name="title">
    <button type="submit">Submit</button>
</form>
```

**Result:** Error **419 | Page Expired** 💀

**Solusi:** Tambahkan `@csrf`! Jangan pernah lupa! 🔐

---

## ✅ Bagian 2: Validation Basics

### Validasi di Controller

**Controller:** `app/Http/Controllers/TaskController.php`

```php
public function store(Request $request)
{
    // ✅ Validasi request
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
    ]);

    // Jika validasi gagal, Laravel otomatis redirect back dengan errors

    // Jika validasi sukses, lanjut create
    Task::create($validated);

    return redirect()->route('tasks.index')
                     ->with('success', 'Task berhasil ditambahkan!');
}
```

**Penjelasan:**
- `$request->validate([...])` → Validasi input
- Jika gagal → Auto redirect dengan error messages
- Jika sukses → Return validated data (safe to use!)

---

### Validation Rules

**Format:** `'field' => 'rule1|rule2|rule3'`

```php
$request->validate([
    'title' => 'required|max:255',
    'email' => 'required|email|unique:users',
    'age' => 'required|integer|min:17|max:100',
    'password' => 'required|min:8|confirmed',
]);
```

---

## 📋 Bagian 3: Validation Rules Lengkap

### 1. required - Wajib Diisi

```php
'title' => 'required',
```

**Cek:** Field tidak boleh kosong, null, atau empty string.

---

### 2. nullable - Boleh Kosong

```php
'description' => 'nullable',
```

**Cek:** Field boleh kosong (optional).

---

### 3. email - Format Email

```php
'email' => 'required|email',
```

**Cek:** Harus format email valid (`user@domain.com`).

---

### 4. max / min - Panjang String atau Angka

```php
'title' => 'required|max:255',       // Maksimal 255 karakter
'age' => 'required|min:17',          // Minimal 17
'content' => 'required|min:10|max:1000', // 10-1000 karakter
```

---

### 5. integer / numeric - Harus Angka

```php
'age' => 'required|integer',          // Integer (1, 2, 3)
'price' => 'required|numeric',        // Numeric (1.5, 2.75)
```

---

### 6. between - Antara Min dan Max

```php
'age' => 'required|integer|between:17,100', // 17-100
'price' => 'required|numeric|between:1000,1000000',
```

---

### 7. in - Harus Salah Satu dari List

```php
'status' => 'required|in:pending,approved,rejected',
'priority' => 'required|in:low,medium,high',
```

**Valid values:** `pending`, `approved`, atau `rejected` saja.

---

### 8. unique - Harus Unik di Database

```php
'email' => 'required|email|unique:users',
'slug' => 'required|unique:posts',
```

**Cek:** Email/slug belum ada di tabel `users`/`posts`.

**Ignore saat update:**
```php
// Update: Ignore current record
'email' => 'required|email|unique:users,email,' . $user->id,
```

---

### 9. exists - Harus Ada di Database

```php
'category_id' => 'required|exists:categories,id',
```

**Cek:** `category_id` harus ada di tabel `categories` kolom `id`.

---

### 10. confirmed - Harus Sama dengan Field _confirmation

```php
'password' => 'required|min:8|confirmed',
```

**Form:**
```html
<input type="password" name="password">
<input type="password" name="password_confirmation">
```

**Cek:** `password` harus sama dengan `password_confirmation`.

---

### 11. date / date_format - Format Tanggal

```php
'birthdate' => 'required|date',
'start_date' => 'required|date_format:Y-m-d',
```

---

### 12. after / before - Tanggal Setelah/Sebelum

```php
'start_date' => 'required|date|after:today',
'end_date' => 'required|date|after:start_date',
'birthdate' => 'required|date|before:today',
```

---

### 13. image / mimes - File Upload

```php
'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
'document' => 'required|mimes:pdf,doc,docx|max:5120', // Max 5MB
```

---

### 14. alpha / alpha_num - Hanya Huruf/Angka

```php
'username' => 'required|alpha_num',   // Huruf & angka saja
'name' => 'required|alpha',           // Huruf saja
```

---

### 15. url - Format URL

```php
'website' => 'nullable|url',
```

**Valid:** `https://example.com`, `http://google.com`

---

### 16. regex - Custom Pattern

```php
'phone' => 'required|regex:/^08[0-9]{8,11}$/', // 08xxxxxxxxxx
```

---

### 17. sometimes - Validasi Jika Ada

```php
'middle_name' => 'sometimes|required|max:100',
```

**Cek:** Hanya validasi jika field ada di request.

---

## 🎨 Bagian 4: Menampilkan Error Messages

### 1. Error untuk Satu Field

```blade
<input type="text" name="title" value="{{ old('title') }}">

@error('title')
    <small style="color: red;">{{ $message }}</small>
@enderror
```

**Output jika error:**
```
The title field is required.
```

---

### 2. Error untuk Semua Fields

```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

---

### 3. Check Jika Ada Error untuk Field

```blade
<input type="text" name="email" class="{{ $errors->has('email') ? 'error' : '' }}">
```

---

## 🔄 Bagian 5: Old Input (Preserve Data)

### Masalah: Data Hilang Saat Error

**Tanpa old():**
```blade
<input type="text" name="title">
```

**Jika validasi gagal → Input form jadi kosong!** User harus ketik ulang (annoying!)

---

### Solusi: Pakai old() Helper

```blade
<input type="text" name="title" value="{{ old('title') }}">
```

**Jika validasi gagal → Input tetap ada!** ✅

---

### old() dengan Default Value

```blade
{{-- Saat create --}}
<input type="text" name="title" value="{{ old('title') }}">

{{-- Saat edit --}}
<input type="text" name="title" value="{{ old('title', $task->title) }}">
```

**Logic:**
- Jika ada old input (dari error) → Pakai old input
- Jika tidak ada → Pakai `$task->title` (data asli)

---

### Contoh Form Lengkap dengan old()

```blade
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}">
        @error('title')
            <small style="color: red;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}">
        @error('email')
            <small style="color: red;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="priority">Priority</label>
        <select name="priority" id="priority">
            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
        </select>
    </div>

    <button type="submit">Submit</button>
</form>
```

---

## 💬 Bagian 6: Custom Error Messages

### Default Error Messages (Bahasa Inggris)

```php
$request->validate([
    'title' => 'required|max:255',
]);

// Error: "The title field is required."
// Error: "The title may not be greater than 255 characters."
```

---

### Custom Messages - Cara 1 (Inline)

```php
$request->validate([
    'title' => 'required|max:255',
    'email' => 'required|email',
], [
    // Custom messages
    'title.required' => 'Judul harus diisi!',
    'title.max' => 'Judul maksimal 255 karakter!',
    'email.required' => 'Email wajib diisi!',
    'email.email' => 'Format email tidak valid!',
]);
```

**Format:** `'field.rule' => 'Custom message'`

---

### Custom Messages - Cara 2 (Custom Attributes)

```php
$request->validate([
    'title' => 'required|max:255',
    'email' => 'required|email',
], [], [
    // Custom attribute names
    'title' => 'judul',
    'email' => 'alamat email',
]);

// Error: "Judul harus diisi." (bukan "The title field is required.")
```

---

### Custom Messages - Cara 3 (Language Files)

**File:** `lang/id/validation.php` (buat file ini jika belum ada)

```php
<?php

return [
    'required' => ':attribute harus diisi.',
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
    ],
    'email' => 'Format :attribute tidak valid.',
    'unique' => ':attribute sudah digunakan.',

    'attributes' => [
        'title' => 'judul',
        'email' => 'alamat email',
        'password' => 'kata sandi',
    ],
];
```

**Set locale di** `config/app.php`:
```php
'locale' => 'id',
```

---

## 📦 Bagian 7: Form Request Classes

### Kenapa Butuh Form Request?

**Problem:** Controller jadi bloated dengan validasi!

```php
public function store(Request $request)
{
    // 🤯 Validasi panjang di controller (not clean!)
    $validated = $request->validate([
        'title' => 'required|max:255',
        'slug' => 'required|unique:posts',
        'body' => 'required|min:10',
        'category_id' => 'required|exists:categories,id',
        'tags' => 'required|array',
        'tags.*' => 'exists:tags,id',
        'image' => 'required|image|max:2048',
    ]);

    // Logic create...
}
```

**Solusi:** Pindahkan validasi ke **Form Request class**! 🎯

---

### Buat Form Request

```bash
php artisan make:request StoreTaskRequest
```

**Output:**
```
INFO  Request [app/Http/Requests/StoreTaskRequest.php] created successfully.
```

---

### Edit Form Request

**File:** `app/Http/Requests/StoreTaskRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * 🔐 Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false; // ❌ Default: false (harus diubah!)
        return true; // ✅ Allow all users (untuk sekarang)
    }

    /**
     * ✅ Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'priority' => 'required|in:low,medium,high',
        ];
    }

    /**
     * 💬 Custom error messages (optional)
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul task harus diisi!',
            'title.max' => 'Judul maksimal 255 karakter!',
            'priority.required' => 'Priority harus dipilih!',
            'priority.in' => 'Priority tidak valid!',
        ];
    }

    /**
     * 🏷️ Custom attribute names (optional)
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'description' => 'deskripsi',
            'priority' => 'prioritas',
        ];
    }
}
```

---

### Pakai Form Request di Controller

**Before (Inline Validation):**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        // ...
    ]);

    Task::create($validated);
    return redirect()->route('tasks.index');
}
```

**After (Form Request):**
```php
use App\Http\Requests\StoreTaskRequest;

public function store(StoreTaskRequest $request) // 🎯 Type-hint Form Request!
{
    // Validasi sudah otomatis jalan!
    // Jika gagal → Auto redirect dengan errors

    // Ambil validated data
    $validated = $request->validated();

    Task::create($validated);
    return redirect()->route('tasks.index');
}
```

**Lebih clean!** Controller fokus ke business logic saja. 🎯

---

### Form Request untuk Update

```bash
php artisan make:request UpdateTaskRequest
```

```php
public function rules(): array
{
    return [
        'title' => 'required|max:255',
        'description' => 'nullable',
        'is_completed' => 'boolean',
    ];
}
```

**Controller:**
```php
public function update(UpdateTaskRequest $request, Task $task)
{
    $task->update($request->validated());
    return redirect()->route('tasks.index');
}
```

---

## 🎯 Bagian 8: Validation Rules Advanced

### 1. Conditional Validation

```php
public function rules(): array
{
    return [
        'payment_method' => 'required|in:credit_card,bank_transfer',
        'card_number' => 'required_if:payment_method,credit_card',
        'bank_name' => 'required_if:payment_method,bank_transfer',
    ];
}
```

**Logic:** `card_number` wajib jika `payment_method` = `credit_card`.

---

### 2. Validation dengan Closure

```php
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'age' => [
            'required',
            'integer',
            function ($attribute, $value, $fail) {
                if ($value < 17) {
                    $fail('Umur minimal 17 tahun untuk daftar SIM.');
                }
            },
        ],
    ];
}
```

---

### 3. Multiple Validation (OR Logic)

```php
'phone' => 'required_without:email', // Phone wajib jika email kosong
'email' => 'required_without:phone', // Email wajib jika phone kosong
```

User harus isi salah satu (phone OR email)!

---

### 4. Array Validation

```php
'tags' => 'required|array|min:1|max:5',
'tags.*' => 'required|string|distinct',
```

**Logic:**
- `tags` harus array
- Minimal 1, maksimal 5 items
- Setiap item harus string dan unique

---

## 📝 Latihan: Form Kontak dengan Validasi

### Latihan 1: Buat Form Kontak

**Task:** Buat form kontak dengan validasi lengkap.

**Fields:**
- name (required, max 100)
- email (required, email)
- subject (required, max 255)
- message (required, min 10)

**Step 1: Route**
```php
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
```

**Step 2: Controller**
```bash
php artisan make:controller ContactController
```

```php
public function create()
{
    return view('contact');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email',
        'subject' => 'required|max:255',
        'message' => 'required|min:10',
    ]);

    // Simpan ke database atau kirim email (nanti!)
    // Untuk sekarang, just redirect dengan success message
    return redirect()->route('contact.create')
                     ->with('success', 'Pesan berhasil dikirim!');
}
```

**Step 3: View** (`resources/views/contact.blade.php`)
```blade
@extends('layouts.app')

@section('content')
    <h1>📧 Kontak Kami</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('contact.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Nama *</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label>Subjek *</label>
            <input type="text" name="subject" value="{{ old('subject') }}">
            @error('subject')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label>Pesan *</label>
            <textarea name="message">{{ old('message') }}</textarea>
            @error('message')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <button type="submit" class="btn">Kirim Pesan</button>
    </form>
@endsection
```

---

## ⚠️ Troubleshooting

### Problem: 419 | Page Expired

**Penyebab:** Lupa `@csrf` di form.

**Solusi:**
```blade
<form method="POST">
    @csrf {{-- Jangan lupa! --}}
</form>
```

---

### Problem: Error messages tidak muncul

**Penyebab:** Tidak ada `@error()` directive di view.

**Solusi:**
```blade
@error('title')
    <small style="color: red;">{{ $message }}</small>
@enderror
```

---

### Problem: Old input tidak preserve

**Penyebab:** Tidak pakai `old()` helper.

**Solusi:**
```blade
<input type="text" name="title" value="{{ old('title') }}">
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **CSRF Protection**: `@csrf` token untuk security (WAJIB!)
- ✅ **Validation Rules**: 17+ rules (required, email, max, unique, dll)
- ✅ **Error Messages**: Tampilkan error per field atau semua
- ✅ **Old Input**: `old()` helper untuk preserve data saat error
- ✅ **Custom Messages**: Bahasa Indonesia & custom messages
- ✅ **Form Request Classes**: Validasi yang clean & reusable
- ✅ **Advanced Validation**: Conditional, closure, array validation

**Form sekarang aman dan user-friendly!** 🔐

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ Membuat **Blog CRUD lengkap** dengan categories
- ✅ Implement relationships (One-to-Many)
- ✅ Upload gambar untuk post thumbnail
- ✅ Pagination, search, dan filter
- ✅ Project yang lebih kompleks & real-world!

**Saatnya tackle project besar!** 📰

---

## 🔗 Referensi

- 📖 [Validation](https://laravel.com/docs/12.x/validation)
- 📖 [Form Requests](https://laravel.com/docs/12.x/validation#form-request-validation)
- 📖 [CSRF Protection](https://laravel.com/docs/12.x/csrf)

---

[⬅️ Bab 17: Praktik To-Do List](17-praktik-todo.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 19: Project Blog CRUD ➡️](19-project-blog.md)

---

<div align="center">

**Form & Validasi sudah dikuasai! Aplikasi jadi aman!** 📋🔐

**Lanjut ke Blog Project untuk praktik lebih kompleks!** 📰

</div>