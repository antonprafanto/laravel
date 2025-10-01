# 💻 Code Examples - Laravel Tutorial

> Contoh kode siap pakai untuk semua chapter tutorial Laravel

---

## 📚 Daftar Code Examples

Folder ini berisi **runnable code examples** yang terpisah dari dokumentasi markdown. Setiap folder berisi project atau contoh kode yang bisa langsung dijalankan.

---

## 🎯 Priority 1: Complete Projects

### [17. To-Do List Application](17-praktik-todo/)

**Chapter 17: Praktik To-Do List**

Mini project untuk belajar CRUD dasar:
- ✅ Create, Read, Update, Delete tasks
- ✅ Mark as completed/uncompleted
- ✅ Form validation
- ✅ Flash messages
- ✅ Responsive UI dengan Bootstrap

**Difficulty:** ⭐⭐ Beginner

**Time:** 1-2 jam

[📖 View README](17-praktik-todo/README.md)

---

### [19. Blog CRUD Application](19-project-blog/)

**Chapter 19: Project Blog CRUD Lengkap**

Complete blog application dengan fitur advanced:
- ✅ Posts dengan image upload
- ✅ Categories (One-to-Many)
- ✅ Tags (Many-to-Many)
- ✅ Authentication dengan Breeze
- ✅ Authorization dengan Policies
- ✅ Pagination & Search
- ✅ Slug auto-generation

**Difficulty:** ⭐⭐⭐⭐ Intermediate

**Time:** 4-6 jam

[📖 View README](19-project-blog/README.md)

---

## 🎯 Priority 2: Eloquent Demos

### [15. Eloquent ORM Examples](15-model-eloquent/)

**Chapter 15: Model & Eloquent Dasar**

Contoh kode untuk operasi Eloquent:
- ✅ CRUD operations
- ✅ Query Builder (where, orderBy, limit)
- ✅ Mass assignment
- ✅ Route model binding
- ✅ Query scopes (local & global)
- ✅ Accessors & Mutators
- ✅ Soft deletes

**Difficulty:** ⭐⭐⭐ Intermediate

**Time:** 2-3 jam

[📖 View README](15-model-eloquent/README.md)

---

## 🎯 Priority 3: Migration Examples

### [13. Migration Examples](13-migration/)

**Chapter 13: Migration Dasar**

Contoh migration untuk berbagai use case:
- ✅ Basic table creation
- ✅ Column types (string, text, integer, boolean, etc.)
- ✅ Foreign keys & relationships
- ✅ Indexes (unique, index, composite)
- ✅ Pivot tables (many-to-many)
- ✅ Modify existing tables
- ✅ Soft deletes

**Difficulty:** ⭐⭐ Beginner

**Time:** 1-2 jam

[📖 View README](13-migration/README.md)

---

## 🚀 Cara Menggunakan

### Quick Start

Setiap folder punya **README.md lengkap** dengan:
1. ✅ Setup instructions
2. ✅ File structure explanation
3. ✅ Code highlights & best practices
4. ✅ Testing guide
5. ✅ Troubleshooting tips

### Untuk Complete Projects (17, 19)

```bash
# 1. Buat project Laravel baru
composer create-project laravel/laravel my-app

# 2. Copy files dari code-examples/XX ke project
cp -r code-examples/17-praktik-todo/* my-app/

# 3. Setup database
# Edit .env, create database

# 4. Run migrations
php artisan migrate

# 5. (Optional) Seed data
php artisan db:seed

# 6. Start server
php artisan serve
```

### Untuk Eloquent & Migration Examples (13, 15)

Baca file PHP langsung atau copy-paste ke project Anda.

---

## 📊 Progress Tracker

Track pembelajaran Anda:

- [ ] **Chapter 13:** Migration Examples
- [ ] **Chapter 15:** Eloquent ORM
- [ ] **Chapter 17:** To-Do List Project (Mini)
- [ ] **Chapter 19:** Blog CRUD Project (Major)

---

## 💡 Learning Path

### Path 1: Pemula Absolut

```
1. Baca docs/01-pengenalan.md s/d docs/12-database.md
2. Praktik migration examples (13-migration/)
3. Praktik Eloquent examples (15-model-eloquent/)
4. Build To-Do List (17-praktik-todo/)
5. Build Blog CRUD (19-project-blog/)
```

### Path 2: Sudah Tahu PHP Dasar

```
1. Skip ke docs/07-mvc.md
2. Langsung praktik To-Do List (17-praktik-todo/)
3. Pelajari Eloquent (15-model-eloquent/)
4. Build Blog CRUD (19-project-blog/)
```

### Path 3: Sudah Tahu Framework Lain

```
1. Skim docs/01-pengenalan.md s/d docs/06-routing.md
2. Focus on Eloquent (15-model-eloquent/)
3. Langsung build Blog CRUD (19-project-blog/)
```

---

## 🎯 Project Complexity Comparison

| Project | Lines of Code | Files | Tables | Time | Difficulty |
|---------|---------------|-------|--------|------|------------|
| To-Do List | ~500 | 8 | 1 | 1-2h | ⭐⭐ |
| Blog CRUD | ~2000 | 25+ | 5 | 4-6h | ⭐⭐⭐⭐ |

---

## 🔥 What You'll Learn

### From To-Do List (17)

- ✅ Basic MVC pattern
- ✅ CRUD operations
- ✅ Form validation
- ✅ Flash messages
- ✅ Blade templates
- ✅ Resource routes

### From Blog CRUD (19)

- ✅ Image upload & storage
- ✅ One-to-Many relationships
- ✅ Many-to-Many relationships
- ✅ Authentication (Breeze)
- ✅ Authorization (Policies)
- ✅ Pagination
- ✅ Search functionality
- ✅ Eager loading (N+1 solution)

### From Eloquent Examples (15)

- ✅ Query Builder mastery
- ✅ Mass assignment
- ✅ Scopes (local & global)
- ✅ Accessors & Mutators
- ✅ Soft deletes

### From Migration Examples (13)

- ✅ Table design
- ✅ Foreign key constraints
- ✅ Indexes for performance
- ✅ Rollback safety
- ✅ Database version control

---

## 📖 Referensi

- [📚 Main Tutorial](../README.md) - Kembali ke daftar isi
- [📖 Laravel Docs](https://laravel.com/docs/12.x) - Official documentation
- [🎥 Laracasts](https://laracasts.com) - Video tutorials

---

## 💬 Need Help?

### Troubleshooting

1. **Read README.md di setiap folder** - Pasti ada troubleshooting section
2. **Check Laravel version** - Tutorial ini untuk Laravel 12.x
3. **Check PHP version** - Minimal PHP 8.2
4. **Database credentials** - Pastikan .env benar

### Common Issues

**Error: "Class not found"**
```php
// Tambahkan di file:
use App\Models\Post;
```

**Error: "Base table or view not found"**
```bash
php artisan migrate
```

**Error: "Route not defined"**
```bash
php artisan route:list
# Cek apakah route sudah ada
```

---

## 🎉 Selamat Belajar!

**Tips:**
1. 💻 Ketik kode sendiri (jangan copy-paste semua!)
2. 🐛 Debug error sendiri dulu sebelum liat solusi
3. 🔄 Build ulang project dari nol (muscle memory!)
4. 📝 Modifikasi & tambahkan fitur sendiri
5. 🚀 Build portfolio projects!

---

**Happy Coding!** 💻✨

Dari pemula ke Laravel developer yang confident! 🚀
