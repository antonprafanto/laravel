# Bab 17: Praktik To-Do List 📝

[⬅️ Bab 16: Eloquent Lanjutan](16-eloquent-lanjutan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 18: Form & Validasi ➡️](18-form-validasi.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Bisa membuat aplikasi To-Do List sederhana dari nol
- ✅ Praktik langsung membuat Migration, Model, dan CRUD
- ✅ Memahami workflow development Laravel dari planning sampai testing
- ✅ Bisa connecting route, controller, dan view untuk CRUD operations
- ✅ Lebih percaya diri sebelum tackle project yang lebih kompleks
- ✅ Paham bagaimana semua konsep yang sudah dipelajari bekerja bersama

---

## 🎯 Analogi Sederhana: To-Do List = Sticky Notes

**To-Do List** seperti **kertas sticky notes** di meja kamu:

```
📋 Daftar Tugas Hari Ini:
   ├── ✅ Belajar Laravel (DONE)
   ├── 📝 Buat aplikasi To-Do (IN PROGRESS)
   └── ⏰ Meeting jam 3 (PENDING)
```

Kita akan buat aplikasi simpel untuk:
- 📝 **Tambah** task baru
- 👀 **Lihat** semua tasks
- ✏️ **Edit** task
- ✅ **Mark** task sebagai complete
- 🗑️ **Hapus** task

**Simple, tapi mencakup semua CRUD operations!** 🚀

---

## 📚 Bagian 1: Planning & Setup

### Apa yang Akan Kita Buat?

**Aplikasi To-Do List** dengan fitur:
- List semua tasks
- Tambah task baru
- Edit task
- Mark task sebagai complete/incomplete
- Hapus task

**Stack:**
- Laravel 12.x
- MySQL/SQLite
- Blade templates
- No JavaScript (pure server-side)

---

### Database Design

**Table:** `tasks`

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT UNSIGNED | Primary key |
| title | VARCHAR(255) | Judul task |
| description | TEXT NULL | Deskripsi task (optional) |
| is_completed | BOOLEAN | Status complete (default: false) |
| created_at | TIMESTAMP | Kapan task dibuat |
| updated_at | TIMESTAMP | Kapan task terakhir diupdate |

**Simple!** Hanya 1 tabel saja. 🎯

---

### Setup Project

**Pastikan sudah punya Laravel project:**
```bash
# Jika belum punya project
composer create-project laravel/laravel todo-app
cd todo-app

# Jalankan server
php artisan serve
```

**Konfigurasi Database:**

**File:** `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=
```

**Buat database** di phpMyAdmin/HeidiSQL dengan nama `todo_app`.

---

## 🔧 Bagian 2: Migration

### Step 1: Buat Migration

```bash
php artisan make:migration create_tasks_table
```

**Output:**
```
INFO  Migration [database/migrations/2025_01_20_create_tasks_table.php] created successfully.
```

---

### Step 2: Edit Migration

**File:** `database/migrations/xxxx_create_tasks_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🏗️ Buat tabel tasks
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // 🔑 Primary key
            $table->string('title'); // 📝 Judul task (wajib)
            $table->text('description')->nullable(); // 📄 Deskripsi (optional)
            $table->boolean('is_completed')->default(false); // ✅ Status complete
            $table->timestamps(); // 📅 created_at, updated_at
        });
    }

    /**
     * 🗑️ Hapus tabel tasks
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

---

### Step 3: Jalankan Migration

```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.

2025_01_20_create_tasks_table ......................... 10ms DONE
```

**Cek di database:** Tabel `tasks` sudah dibuat! ✅

---

## 📦 Bagian 3: Model

### Step 1: Buat Model

```bash
php artisan make:model Task
```

**Output:**
```
INFO  Model [app/Models/Task.php] created successfully.
```

---

### Step 2: Edit Model

**File:** `app/Models/Task.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * 🔐 Mass assignment protection
     * Kolom yang boleh di-fill via create() atau update()
     */
    protected $fillable = [
        'title',
        'description',
        'is_completed',
    ];

    /**
     * 🎯 Cast is_completed jadi boolean (otomatis)
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];
}
```

**Penjelasan:**
- `$fillable` → Kolom yang boleh di-mass assign (security!)
- `$casts` → Cast `is_completed` jadi boolean (0/1 → false/true)

---

## 🧪 Bagian 4: Testing via Tinker

### Step 1: Buka Tinker

```bash
php artisan tinker
```

---

### Step 2: CREATE - Buat Task Baru

```php
>>> use App\Models\Task;

>>> Task::create([
...     'title' => 'Belajar Laravel',
...     'description' => 'Belajar Laravel dari nol sampai bisa!',
... ]);
=> App\Models\Task {#...
     id: 1,
     title: "Belajar Laravel",
     description: "Belajar Laravel dari nol sampai bisa!",
     is_completed: false,
     created_at: "2025-01-20 10:00:00",
     updated_at: "2025-01-20 10:00:00",
   }

>>> Task::create([
...     'title' => 'Buat To-Do App',
...     'description' => 'Praktik membuat aplikasi To-Do List',
... ]);

>>> Task::create([
...     'title' => 'Meeting dengan tim',
... ]);
```

**3 tasks berhasil dibuat!** ✅

---

### Step 3: READ - Lihat Semua Tasks

```php
>>> $tasks = Task::all();
=> Illuminate\Database\Eloquent\Collection {#...
     all: [
       App\Models\Task {#...
         id: 1,
         title: "Belajar Laravel",
         ...
       },
       App\Models\Task {#...
         id: 2,
         title: "Buat To-Do App",
         ...
       },
       App\Models\Task {#...
         id: 3,
         title: "Meeting dengan tim",
         ...
       },
     ],
   }

>>> $tasks->count();
=> 3
```

---

### Step 4: READ - Lihat Task Tertentu

```php
>>> $task = Task::find(1);
=> App\Models\Task {#...
     id: 1,
     title: "Belajar Laravel",
     is_completed: false,
   }

>>> $task->title;
=> "Belajar Laravel"

>>> $task->is_completed;
=> false
```

---

### Step 5: UPDATE - Mark Task sebagai Complete

```php
>>> $task = Task::find(1);

>>> $task->update(['is_completed' => true]);
=> true

>>> $task->is_completed;
=> true

>>> $task->fresh(); // Refresh dari database
=> App\Models\Task {#...
     id: 1,
     title: "Belajar Laravel",
     is_completed: true,
     ...
   }
```

---

### Step 6: UPDATE - Edit Title

```php
>>> $task = Task::find(2);

>>> $task->update([
...     'title' => 'Buat To-Do App (Updated)',
...     'description' => 'Praktik CRUD lengkap!',
... ]);
=> true
```

---

### Step 7: DELETE - Hapus Task

```php
>>> $task = Task::find(3);

>>> $task->delete();
=> true

>>> Task::count();
=> 2
```

---

### Step 8: Query - Filter Tasks

```php
>>> // Tasks yang completed
>>> Task::where('is_completed', true)->get();

>>> // Tasks yang belum completed
>>> Task::where('is_completed', false)->get();

>>> // Tasks terbaru dulu
>>> Task::latest()->get();
```

**CRUD via Tinker berhasil!** Keluar dari Tinker: `Ctrl+C` atau ketik `exit`

---

## 🗺️ Bagian 5: Routes

### Edit Routes

**File:** `routes/web.php`

```php
<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// 🏠 Homepage (redirect ke tasks)
Route::get('/', function () {
    return redirect('/tasks');
});

// 📋 CRUD Routes untuk Tasks
Route::resource('tasks', TaskController::class);
```

**Penjelasan:**
- `Route::resource()` → Otomatis buat 7 routes untuk CRUD!

**Cek routes yang dibuat:**
```bash
php artisan route:list --name=tasks
```

**Output:**
```
GET       /tasks              tasks.index    → List semua tasks
GET       /tasks/create       tasks.create   → Form tambah task
POST      /tasks              tasks.store    → Simpan task baru
GET       /tasks/{task}       tasks.show     → Detail task
GET       /tasks/{task}/edit  tasks.edit     → Form edit task
PUT/PATCH /tasks/{task}       tasks.update   → Update task
DELETE    /tasks/{task}       tasks.destroy  → Hapus task
```

**7 routes otomatis!** 🎯

---

## 🎮 Bagian 6: Controller

### Step 1: Buat Controller

```bash
php artisan make:controller TaskController --resource
```

**Output:**
```
INFO  Controller [app/Http/Controllers/TaskController.php] created successfully.
```

**Flag `--resource`** → Buat controller dengan 7 method CRUD otomatis!

---

### Step 2: Edit Controller

**File:** `app/Http/Controllers/TaskController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * 📋 Display a listing of the tasks.
     */
    public function index()
    {
        // 📚 Ambil semua tasks, terbaru dulu
        $tasks = Task::latest()->get();

        // 🎨 Return view dengan data tasks
        return view('tasks.index', compact('tasks'));
    }

    /**
     * 📝 Show the form for creating a new task.
     */
    public function create()
    {
        // 🎨 Return view form tambah task
        return view('tasks.create');
    }

    /**
     * 💾 Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validasi input (nanti di Bab 18 kita bahas detail)
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // 🌱 Buat task baru
        Task::create($validated);

        // 🔄 Redirect ke index dengan flash message
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil ditambahkan!');
    }

    /**
     * 👀 Display the specified task.
     */
    public function show(Task $task)
    {
        // 🎨 Return view detail task
        return view('tasks.show', compact('task'));
    }

    /**
     * ✏️ Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // 🎨 Return view form edit task
        return view('tasks.edit', compact('task'));
    }

    /**
     * 🔄 Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        // ✅ Validasi input
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'is_completed' => 'boolean',
        ]);

        // ✏️ Update task
        $task->update($validated);

        // 🔄 Redirect ke index dengan flash message
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil diupdate!');
    }

    /**
     * 🗑️ Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        // 🗑️ Hapus task
        $task->delete();

        // 🔄 Redirect ke index dengan flash message
        return redirect()->route('tasks.index')
                         ->with('success', 'Task berhasil dihapus!');
    }
}
```

**Penjelasan:**
- `index()` → List semua tasks
- `create()` → Form tambah task
- `store()` → Simpan task baru
- `show()` → Detail task
- `edit()` → Form edit task
- `update()` → Update task
- `destroy()` → Hapus task

**Route Model Binding:** Parameter `Task $task` otomatis inject model! 🎯

---

## 🎨 Bagian 7: Views (Blade Templates)

### Setup: Buat Folder Views

```bash
# Di terminal / manual buat folder
mkdir resources/views/tasks
```

---

### 1. Layout Master

**File:** `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List App</title>

    <!-- 🎨 Simple CSS (inline untuk kesederhanaan) -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; font-weight: bold; }
        .completed { text-decoration: line-through; color: #888; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- 📋 Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📄 Content Section --}}
        @yield('content')
    </div>
</body>
</html>
```

---

### 2. Index Page (List Tasks)

**File:** `resources/views/tasks/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>📋 To-Do List</h1>
        <a href="{{ route('tasks.create') }}" class="btn">➕ Tambah Task</a>
    </div>

    @if ($tasks->isEmpty())
        <p style="text-align: center; color: #888; padding: 40px;">
            Belum ada task. Yuk tambah task pertama! 🚀
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>
                            <strong class="{{ $task->is_completed ? 'completed' : '' }}">
                                {{ $task->title }}
                            </strong>
                            @if ($task->description)
                                <br>
                                <small style="color: #666;">{{ Str::limit($task->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if ($task->is_completed)
                                <span style="color: green;">✅ Selesai</span>
                            @else
                                <span style="color: orange;">⏰ Belum</span>
                            @endif
                        </td>
                        <td>{{ $task->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" class="btn" style="padding: 5px 10px; font-size: 12px;">👀 Lihat</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn" style="padding: 5px 10px; font-size: 12px;">✏️ Edit</a>

                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin mau hapus task ini?')">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
```

---

### 3. Create Page (Form Tambah Task)

**File:** `resources/views/tasks/create.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <h1>➕ Tambah Task Baru</h1>

    <form action="{{ route('tasks.store') }}" method="POST" style="margin-top: 20px;">
        @csrf

        <div class="form-group">
            <label for="title">Judul Task *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
            @error('title')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Deskripsi (Optional)</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">💾 Simpan</button>
            <a href="{{ route('tasks.index') }}" class="btn" style="background: #6c757d;">↩️ Batal</a>
        </div>
    </form>
@endsection
```

---

### 4. Edit Page (Form Edit Task)

**File:** `resources/views/tasks/edit.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <h1>✏️ Edit Task</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST" style="margin-top: 20px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Judul Task *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required>
            @error('title')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Deskripsi (Optional)</label>
            <textarea name="description" id="description">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <small style="color: red;">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_completed" value="1" {{ $task->is_completed ? 'checked' : '' }}>
                <span>✅ Tandai sebagai selesai</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">💾 Update</button>
            <a href="{{ route('tasks.index') }}" class="btn" style="background: #6c757d;">↩️ Batal</a>
        </div>
    </form>
@endsection
```

---

### 5. Show Page (Detail Task)

**File:** `resources/views/tasks/show.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>👀 Detail Task</h1>
        <a href="{{ route('tasks.index') }}" class="btn" style="background: #6c757d;">↩️ Kembali</a>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 class="{{ $task->is_completed ? 'completed' : '' }}">
            {{ $task->title }}
        </h2>

        @if ($task->description)
            <p style="margin-top: 15px; color: #666; line-height: 1.6;">
                {{ $task->description }}
            </p>
        @endif

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p>
                <strong>Status:</strong>
                @if ($task->is_completed)
                    <span style="color: green;">✅ Selesai</span>
                @else
                    <span style="color: orange;">⏰ Belum Selesai</span>
                @endif
            </p>
            <p style="margin-top: 10px;">
                <strong>Dibuat:</strong> {{ $task->created_at->format('d F Y, H:i') }}
                <small style="color: #888;">({{ $task->created_at->diffForHumans() }})</small>
            </p>
            <p style="margin-top: 10px;">
                <strong>Terakhir Diupdate:</strong> {{ $task->updated_at->format('d F Y, H:i') }}
                <small style="color: #888;">({{ $task->updated_at->diffForHumans() }})</small>
            </p>
        </div>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="{{ route('tasks.edit', $task) }}" class="btn">✏️ Edit Task</a>

        <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin mau hapus task ini?')">🗑️ Hapus Task</button>
        </form>
    </div>
@endsection
```

---

## 🚀 Bagian 8: Testing Aplikasi

### Step 1: Jalankan Server

```bash
php artisan serve
```

**Buka browser:** `http://localhost:8000`

---

### Step 2: Test CRUD Operations

**✅ CREATE:**
1. Klik "➕ Tambah Task"
2. Isi form: Title "Belajar Laravel", Description "Belajar dari nol!"
3. Klik "💾 Simpan"
4. ✅ Task baru muncul di list!

**✅ READ:**
1. Lihat list tasks di homepage
2. Klik "👀 Lihat" untuk lihat detail
3. ✅ Detail task muncul!

**✅ UPDATE:**
1. Klik "✏️ Edit" di task
2. Ubah title dan centang "Tandai sebagai selesai"
3. Klik "💾 Update"
4. ✅ Task terupdate dan ada strikethrough!

**✅ DELETE:**
1. Klik "🗑️ Hapus" di task
2. Confirm popup "Yakin mau hapus?"
3. ✅ Task terhapus dari list!

**Aplikasi To-Do List berhasil!** 🎉

---

## 📖 Summary: Workflow Development Laravel

**Dari Planning sampai Production:**

```
1️⃣ PLANNING
   └── Design database (tabel & kolom)

2️⃣ MIGRATION
   └── php artisan make:migration create_tasks_table
   └── Edit migration file
   └── php artisan migrate

3️⃣ MODEL
   └── php artisan make:model Task
   └── Set $fillable dan $casts

4️⃣ TESTING (Tinker)
   └── Test CRUD via Tinker
   └── Pastikan model & database jalan

5️⃣ ROUTES
   └── Route::resource('tasks', TaskController::class)

6️⃣ CONTROLLER
   └── php artisan make:controller TaskController --resource
   └── Implement 7 method CRUD

7️⃣ VIEWS
   └── Buat layout master
   └── Buat views untuk index, create, edit, show

8️⃣ TESTING (Browser)
   └── Test semua fitur di browser
   └── Fix bugs jika ada

9️⃣ REFINEMENT
   └── Tambah styling
   └── Tambah validasi (Bab 18)
   └── Tambah fitur extra
```

**Workflow ini berlaku untuk SEMUA aplikasi Laravel!** 🔄

---

## 📝 Latihan: Extend Fitur

### Latihan 1: Tambah Kolom `due_date`

**Task:** Tambah kolom `due_date` (tanggal deadline) ke tabel tasks.

**Hint:**
```bash
# Buat migration
php artisan make:migration add_due_date_to_tasks_table

# Edit migration
$table->date('due_date')->nullable()->after('description');

# Run migration
php artisan migrate
```

**Update:**
- Model: Tambah `'due_date'` ke `$fillable`
- Views: Tambah input date di form create/edit
- Views index: Tampilkan due date

---

### Latihan 2: Tambah Kolom `priority`

**Task:** Tambah kolom `priority` (low, medium, high).

**Hint:**
```php
// Migration
$table->enum('priority', ['low', 'medium', 'high'])->default('medium');

// Model
protected $fillable = [..., 'priority'];

// View: Tambah dropdown priority
<select name="priority">
    <option value="low">🟢 Low</option>
    <option value="medium">🟡 Medium</option>
    <option value="high">🔴 High</option>
</select>
```

---

### Latihan 3: Filter Tasks

**Task:** Tambah filter untuk tampilkan hanya tasks yang completed atau pending.

**Hint:**
```php
// Routes
Route::get('/tasks/completed', [TaskController::class, 'completed']);
Route::get('/tasks/pending', [TaskController::class, 'pending']);

// Controller
public function completed()
{
    $tasks = Task::where('is_completed', true)->latest()->get();
    return view('tasks.index', compact('tasks'));
}

public function pending()
{
    $tasks = Task::where('is_completed', false)->latest()->get();
    return view('tasks.index', compact('tasks'));
}

// View: Tambah link filter
<a href="{{ route('tasks.index') }}">Semua</a> |
<a href="{{ url('/tasks/completed') }}">Completed</a> |
<a href="{{ url('/tasks/pending') }}">Pending</a>
```

---

### Latihan 4: Soft Deletes

**Task:** Implement soft deletes untuk tasks.

**Hint:**
```bash
# Migration
php artisan make:migration add_soft_deletes_to_tasks_table
$table->softDeletes();
php artisan migrate

# Model
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
}

# Controller: Tambah method untuk trash
public function trash()
{
    $tasks = Task::onlyTrashed()->get();
    return view('tasks.trash', compact('tasks'));
}

public function restore($id)
{
    Task::withTrashed()->find($id)->restore();
    return redirect()->route('tasks.index');
}
```

---

## ⚠️ Troubleshooting

### Problem: "Route [tasks.index] not defined"

**Penyebab:** Routes belum didefinisikan atau salah nama.

**Solusi:**
```bash
# Cek routes
php artisan route:list --name=tasks

# Pastikan ada di web.php
Route::resource('tasks', TaskController::class);
```

---

### Problem: View not found

**Penyebab:** File view belum dibuat atau salah lokasi.

**Solusi:**
```
Pastikan struktur folder:
resources/views/
├── layouts/
│   └── app.blade.php
└── tasks/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php
```

---

### Problem: "Add [title] to fillable property"

**Penyebab:** `$fillable` belum diset di Model.

**Solusi:**
```php
// app/Models/Task.php
protected $fillable = ['title', 'description', 'is_completed'];
```

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Form & Validasi lengkap dengan Laravel
- ✅ Validation rules (required, max, min, email, dll)
- ✅ Custom error messages
- ✅ Form Request classes
- ✅ Client-side validation

**Form yang aman dan user-friendly!** 📋

---

## 🔗 Referensi

- 📖 [Controllers: Resource Controllers](https://laravel.com/docs/12.x/controllers#resource-controllers)
- 📖 [Routing: Route Model Binding](https://laravel.com/docs/12.x/routing#route-model-binding)
- 📖 [Views: Blade Templates](https://laravel.com/docs/12.x/blade)

---

[⬅️ Bab 16: Eloquent Lanjutan](16-eloquent-lanjutan.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 18: Form & Validasi ➡️](18-form-validasi.md)

---

<div align="center">

**To-Do List App berhasil dibuat! CRUD lengkap sudah dikuasai!** 📝✅

**Lanjut ke Form & Validasi untuk aplikasi yang lebih aman!** 🔐

</div>