# 🏗️ Laravel Migration - Code Examples

> **Chapter 13: Migration Dasar**
> Contoh kode untuk berbagai use case migration di Laravel

---

## 🎯 Apa yang Dipelajari?

Folder ini berisi contoh-contoh migration untuk:

✅ **Basic Table Creation** - Create simple tables
✅ **Column Types** - String, integer, text, boolean, etc.
✅ **Indexes** - Primary, unique, index, foreign keys
✅ **Relationships** - Foreign key constraints
✅ **Modify Tables** - Add/drop columns, rename, etc.
✅ **Pivot Tables** - Many-to-many relationships
✅ **Best Practices** - Naming conventions, rollback safety

---

## 📁 Struktur File

```
code-examples/13-migration/
├── 01_create_users_table.php          # Basic table
├── 02_create_posts_table.php          # Table with foreign key
├── 03_create_categories_table.php     # Simple lookup table
├── 04_create_tags_table.php           # Another lookup table
├── 05_create_post_tag_table.php       # Pivot table (many-to-many)
├── 06_add_columns_to_posts.php        # Modify existing table
├── 07_create_comments_table.php       # Polymorphic example
└── README.md                          # File ini
```

---

## 🚀 Cara Menggunakan

### Generate Migration

```bash
# Basic migration
php artisan make:migration create_posts_table

# With model
php artisan make:model Post -m

# Add column to existing table
php artisan make:migration add_slug_to_posts_table

# Complete package (Model + Migration + Controller + Seeder + Factory)
php artisan make:model Post -mcrsf
```

### Run Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Rollback all + re-run all
php artisan migrate:refresh

# Fresh (drop all tables + migrate)
php artisan migrate:fresh

# Fresh + seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

---

## 📝 Code Examples

### 01. Basic Table - Users

```php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

**File:** `01_create_users_table.php`

---

### 02. Table with Foreign Key - Posts

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('body');
        $table->boolean('is_published')->default(false);

        // Foreign key to users table
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');

        $table->timestamps();
    });
}
```

**File:** `02_create_posts_table.php`

---

### 03. Lookup Table - Categories

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->timestamps();
    });
}
```

**File:** `03_create_categories_table.php`

---

### 04. Pivot Table - Post Tags (Many-to-Many)

```php
public function up(): void
{
    Schema::create('post_tag', function (Blueprint $table) {
        $table->id();

        $table->foreignId('post_id')
              ->constrained()
              ->onDelete('cascade');

        $table->foreignId('tag_id')
              ->constrained()
              ->onDelete('cascade');

        $table->timestamps();

        // Prevent duplicate entries
        $table->unique(['post_id', 'tag_id']);
    });
}
```

**File:** `05_create_post_tag_table.php`

---

### 05. Modify Existing Table - Add Columns

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->integer('views')->default(0)->after('body');
        $table->boolean('is_featured')->default(false);
        $table->softDeletes(); // adds deleted_at column
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn(['views', 'is_featured']);
        $table->dropSoftDeletes();
    });
}
```

**File:** `06_add_columns_to_posts.php`

---

## 🎨 Column Types

### String & Text

```php
$table->string('name');              // VARCHAR(255)
$table->string('name', 100);         // VARCHAR(100)
$table->text('description');         // TEXT
$table->mediumText('body');          // MEDIUMTEXT
$table->longText('content');         // LONGTEXT
```

### Numeric

```php
$table->integer('votes');            // INTEGER
$table->bigInteger('views');         // BIGINT
$table->tinyInteger('status');       // TINYINT
$table->decimal('price', 8, 2);      // DECIMAL(8,2)
$table->float('rating', 8, 2);       // FLOAT
$table->double('amount');            // DOUBLE
```

### Boolean

```php
$table->boolean('is_published');     // BOOLEAN (TINYINT 0/1)
$table->boolean('is_active')->default(true);
```

### Dates & Times

```php
$table->date('birth_date');          // DATE
$table->time('start_time');          // TIME
$table->dateTime('published_at');    // DATETIME
$table->timestamp('created_at');     // TIMESTAMP
$table->timestamps();                // created_at + updated_at
$table->softDeletes();               // deleted_at (for soft delete)
```

### Other Types

```php
$table->json('options');             // JSON
$table->enum('status', ['draft', 'published', 'archived']);
$table->uuid('id');                  // UUID
$table->ipAddress('visitor');        // IP address
$table->macAddress('device');        // MAC address
```

---

## 🔗 Foreign Keys

### Basic Foreign Key

```php
$table->foreignId('user_id')
      ->constrained()
      ->onDelete('cascade');
```

### Custom Foreign Key

```php
$table->foreignId('author_id')
      ->constrained('users')        // Reference users table
      ->onUpdate('cascade')
      ->onDelete('cascade');
```

### Drop Foreign Key

```php
$table->dropForeign(['user_id']);
// or
$table->dropConstrainedForeignId('user_id');
```

---

## 🎯 Indexes

### Primary Key

```php
$table->id();                        // Auto-increment primary key
// or
$table->bigIncrements('id');
```

### Unique Index

```php
$table->string('email')->unique();
// or
$table->unique('email');
```

### Regular Index

```php
$table->index('slug');
$table->index(['category_id', 'created_at']);
```

### Drop Index

```php
$table->dropUnique(['email']);
$table->dropIndex(['slug']);
```

---

## 💡 Best Practices

### 1. Always Implement down() Method

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        // ...
    });
}

public function down(): void
{
    Schema::dropIfExists('posts'); // For rollback
}
```

### 2. Foreign Keys in Correct Order

```php
// ✅ GOOD ORDER:
1. Create users table
2. Create categories table
3. Create posts table (references users & categories)
4. Create comments table (references posts)

// ❌ BAD ORDER:
1. Create posts table (references users - ERROR! users doesn't exist yet)
```

### 3. Use Descriptive Migration Names

```php
// ✅ GOOD:
2024_01_01_create_posts_table.php
2024_01_02_add_slug_to_posts_table.php
2024_01_03_add_foreign_key_to_comments_table.php

// ❌ BAD:
2024_01_01_posts.php
2024_01_02_update.php
```

### 4. Use Timestamps

```php
// ✅ GOOD:
$table->timestamps(); // created_at + updated_at

// Use them in Model:
$post->created_at->diffForHumans(); // "2 days ago"
```

### 5. Nullable Columns

```php
// Jika column boleh kosong
$table->string('phone')->nullable();
$table->text('bio')->nullable();
```

### 6. Default Values

```php
$table->integer('views')->default(0);
$table->boolean('is_published')->default(false);
$table->timestamp('published_at')->nullable();
```

---

## 🧪 Testing Migrations

```bash
# Check migration status
php artisan migrate:status

# Run migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback + re-run
php artisan migrate:refresh

# Fresh start (WARNING: deletes all data!)
php artisan migrate:fresh
```

---

## 🔥 Common Errors & Solutions

### Error 1: "Base table or view not found"

**Penyebab:** Migration order salah (foreign key sebelum table exists)

**Solusi:** Rename migration file agar sesuai urutan:
```bash
2024_01_01_create_users_table.php
2024_01_02_create_posts_table.php  # Bisa reference users
```

---

### Error 2: "Cannot add foreign key constraint"

**Penyebab:**
- Referenced table belum ada
- Referenced column type tidak match (int vs bigInt)

**Solusi:**
```php
// ✅ GOOD:
$table->foreignId('user_id')  // BIGINT UNSIGNED
      ->constrained();

// ❌ BAD:
$table->integer('user_id');   // INT (tidak match dengan id yang BIGINT)
```

---

### Error 3: "Syntax error or access violation: 1071 Specified key was too long"

**Penyebab:** String index terlalu panjang (MySQL)

**Solusi:** Di `AppServiceProvider`:
```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

---

## 📚 Referensi

- [Chapter 13: Migration Dasar](../../docs/13-migration.md)
- [Laravel Migration Docs](https://laravel.com/docs/12.x/migrations)
- [Schema Builder](https://laravel.com/docs/12.x/migrations#tables)

---

## 🎯 Latihan

### Latihan 1: Blog Database

Buat migrations untuk blog sederhana:
1. `users` table (name, email, password)
2. `categories` table (name, slug)
3. `posts` table (title, slug, body, user_id, category_id)
4. `comments` table (body, user_id, post_id)

### Latihan 2: E-commerce Database

Buat migrations untuk e-commerce:
1. `products` table (name, price, stock)
2. `orders` table (user_id, total)
3. `order_items` table (order_id, product_id, quantity, price)

### Latihan 3: Modify Tables

1. Add `views` column to posts table
2. Add `is_featured` boolean to posts
3. Add `softDeletes` to posts table

---

**Happy Migrating!** 🏗️✨

Migrations adalah version control untuk database! 🚀
