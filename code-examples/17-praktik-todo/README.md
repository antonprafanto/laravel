# 📝 To-Do List Application - Complete Example

> **Chapter 17: Praktik To-Do List**
> Contoh kode lengkap untuk aplikasi To-Do List sederhana dengan Laravel

---

## 🎯 Fitur Aplikasi

✅ **CRUD Operations:**
- Create: Tambah task baru
- Read: Lihat semua tasks
- Update: Edit task yang sudah ada
- Delete: Hapus task

✅ **Additional Features:**
- Mark as completed/uncompleted
- Validation form
- Flash messages (success/error)
- Responsive design dengan Bootstrap

---

## 📋 Prerequisites

Pastikan sudah terinstall:
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Laravel 12.x

---

## 🚀 Instalasi & Setup

### 1. Clone atau Copy Project

```bash
# Jika ini folder terpisah, buat project Laravel baru
composer create-project laravel/laravel todo-app

# Atau copy files dari folder ini ke project Laravel Anda
```

### 2. Setup Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```sql
CREATE DATABASE todo_db;
```

### 3. Copy Files

Copy semua file dari folder `app/`, `database/`, `resources/`, dan `routes/` ke project Laravel Anda.

### 4. Run Migration

```bash
php artisan migrate
```

### 5. (Optional) Seed Sample Data

```bash
php artisan db:seed --class=TaskSeeder
```

### 6. Run Application

```bash
php artisan serve
```

Buka browser: http://localhost:8000/tasks

---

## 📁 Struktur File

```
code-examples/17-praktik-todo/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── TaskController.php       # Controller untuk CRUD tasks
│   └── Models/
│       └── Task.php                     # Model Task
│
├── database/
│   ├── migrations/
│   │   └── 2024_01_01_create_tasks_table.php
│   └── seeders/
│       └── TaskSeeder.php               # Dummy data
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # Layout utama
│       └── tasks/
│           ├── index.blade.php         # List semua tasks
│           ├── create.blade.php        # Form tambah task
│           └── edit.blade.php          # Form edit task
│
├── routes/
│   └── web.php                         # Definisi routes
│
└── README.md                           # File ini
```

---

## 🔗 Routes

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | /tasks | tasks.index | Tampilkan semua tasks |
| GET | /tasks/create | tasks.create | Form tambah task |
| POST | /tasks | tasks.store | Simpan task baru |
| GET | /tasks/{task}/edit | tasks.edit | Form edit task |
| PUT | /tasks/{task} | tasks.update | Update task |
| DELETE | /tasks/{task} | tasks.destroy | Hapus task |
| PATCH | /tasks/{task}/toggle | tasks.toggle | Toggle completed status |

> **Note:** Resource route `/tasks/{task}` (show) tidak diimplementasikan karena tidak diperlukan dalam tutorial basic. Detail task sudah ditampilkan di index page.

### Cek Routes

```bash
php artisan route:list --name=tasks
```

---

## 💡 Cara Pakai

### 1. Lihat Semua Tasks

Akses: http://localhost:8000/tasks

### 2. Tambah Task Baru

1. Klik tombol "Tambah Task Baru"
2. Isi form (title wajib, description optional)
3. Klik "Simpan"

### 3. Edit Task

1. Klik tombol "Edit" di task yang ingin diedit
2. Ubah title/description
3. Klik "Update"

### 4. Mark as Completed

Klik checkbox di sebelah task untuk toggle completed/uncompleted.

### 5. Hapus Task

Klik tombol "Hapus" → konfirmasi → task terhapus.

---

## 🧪 Testing Manual

### Test 1: Create Task

```
1. Buka /tasks/create
2. Isi title: "Belajar Laravel"
3. Submit
4. ✅ Harus muncul success message
5. ✅ Task muncul di list
```

### Test 2: Validation

```
1. Buka /tasks/create
2. JANGAN isi title (kosong)
3. Submit
4. ✅ Harus muncul error: "The title field is required"
```

### Test 3: Edit Task

```
1. Klik "Edit" pada task
2. Ubah title
3. Submit
4. ✅ Title berubah di list
```

### Test 4: Toggle Completed

```
1. Klik checkbox task
2. ✅ Task berubah jadi strikethrough (completed)
3. Klik lagi
4. ✅ Kembali normal (uncompleted)
```

### Test 5: Delete Task

```
1. Klik "Hapus"
2. ✅ Task hilang dari list
```

---

## 📝 Code Highlights

### Model Task

```php
// app/Models/Task.php
class Task extends Model
{
    protected $fillable = ['title', 'description', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];
}
```

### Controller TaskController

```php
// app/Http/Controllers/TaskController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
    ]);

    Task::create($validated);

    return redirect()->route('tasks.index')
                     ->with('success', 'Task berhasil ditambahkan!');
}
```

### Blade View

```blade
{{-- resources/views/tasks/index.blade.php --}}
@foreach($tasks as $task)
    <div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
        <h5>{{ $task->title }}</h5>
        <p>{{ $task->description }}</p>
    </div>
@endforeach
```

---

## 🔧 Troubleshooting

### Error: "Class 'Task' not found"

**Solusi:**
```php
// Tambahkan di controller
use App\Models\Task;
```

### Error: "SQLSTATE[42S02]: Base table or view not found"

**Solusi:**
```bash
php artisan migrate
```

### Task tidak muncul setelah dibuat

**Cek:**
1. Apakah $fillable sudah benar?
2. Apakah redirect ke tasks.index?
3. Cek database: `SELECT * FROM tasks;`

---

## 📚 Pelajaran yang Dipelajari

Dari project ini, kamu belajar:

✅ **Route resource** untuk CRUD
✅ **Controller methods** (index, create, store, edit, update, destroy)
✅ **Validation** dengan `$request->validate()`
✅ **Eloquent ORM** (create, update, delete)
✅ **Blade templates** dengan layout
✅ **Flash messages** untuk feedback
✅ **Form handling** dengan CSRF protection

---

## 🚀 Next Steps

Setelah menguasai To-Do app ini:

1. **Tambah fitur:**
   - Due date untuk tasks
   - Priority levels (high/medium/low)
   - Categories/tags
   - Search functionality

2. **Lanjut ke Chapter 19:**
   - Blog CRUD dengan image upload
   - Categories & tags (many-to-many)
   - Pagination & search
   - Authentication

3. **Improve UI:**
   - Gunakan Tailwind CSS
   - Tambah animations
   - Dark mode toggle

---

## 📖 Referensi

- [Chapter 17: Praktik To-Do List](../../docs/17-praktik-todo.md)
- [Laravel Validation Docs](https://laravel.com/docs/12.x/validation)
- [Eloquent ORM Docs](https://laravel.com/docs/12.x/eloquent)

---

**Happy Coding!** 💻✨

Jika ada pertanyaan atau issue, silakan buka issue di repository ini.
