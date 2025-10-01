# Bab 23: Middleware 🚪

[⬅️ Bab 22: Authorization](22-authorization.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 24: Session & Flash Messages ➡️](24-session-flash.md)

---

## 🎯 Learning Objectives

- ✅ Memahami konsep Middleware
- ✅ Menguasai middleware bawaan Laravel
- ✅ Bisa membuat custom middleware
- ✅ Paham middleware groups & global middleware
- ✅ Implement logging & tracking dengan middleware

---

## 🎯 Analogi: Middleware = Satpam Gedung

**Middleware** = **Satpam di pintu gedung** yang cek setiap orang sebelum masuk.

```
👤 Request datang ke aplikasi
   ↓
🛡️ MIDDLEWARE 1: Satpam cek KTP (auth)
   ├── ❌ Tidak punya KTP → Tolak! (redirect login)
   └── ✅ Punya KTP → Lanjut!
   ↓
🛡️ MIDDLEWARE 2: Satpam cek suhu badan (health check)
   ├── ❌ Suhu > 37°C → Tolak!
   └── ✅ Suhu normal → Lanjut!
   ↓
🛡️ MIDDLEWARE 3: Satpam catat nama di buku tamu (logging)
   ↓
🏢 Masuk ke Controller (aplikasi)
   ↓
📤 Response kembali
   ↓
🛡️ MIDDLEWARE (Response): Satpam cek bawaan (compress response)
   ↓
👤 Response sampai ke user
```

**Middleware** = Filter request SEBELUM & SESUDAH masuk controller!

---

## 📚 Bagian 1: Middleware Bawaan Laravel

### 1. auth - Authentication

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

**Function:** Cek user sudah login atau belum.
- ✅ Sudah login → Lanjut
- ❌ Belum login → Redirect ke `/login`

---

### 2. guest - Guest Only

```php
Route::get('/login', [LoginController::class, 'create'])
     ->middleware('guest');
```

**Function:** Cek user belum login.
- ✅ Belum login → Lanjut
- ❌ Sudah login → Redirect ke `/dashboard`

---

### 3. verified - Email Verification

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified']);
```

**Function:** Cek email sudah diverifikasi.

---

### 4. throttle - Rate Limiting

```php
Route::get('/api/posts', function () {
    return Post::all();
})->middleware('throttle:60,1'); // Max 60 requests per 1 menit
```

**Function:** Limit request untuk prevent spam/DDoS.

---

## 🔧 Bagian 2: Membuat Custom Middleware

### Step 1: Generate Middleware

```bash
php artisan make:middleware LogRequests
```

**Output:**
```
INFO  Middleware [app/Http/Middleware/LogRequests.php] created successfully.
```

---

### Step 2: Edit Middleware

**File:** `app/Http/Middleware/LogRequests.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    /**
     * 📝 Log setiap request yang masuk
     */
    public function handle(Request $request, Closure $next)
    {
        // BEFORE: Sebelum request ke controller
        Log::info('Request:', [
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user' => auth()->id() ?? 'guest',
        ]);

        // Proses request
        $response = $next($request);

        // AFTER: Setelah controller proses
        Log::info('Response:', [
            'status' => $response->status(),
        ]);

        return $response;
    }
}
```

---

### Step 3: Register Middleware

**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    // Alias middleware
    $middleware->alias([
        'log.requests' => \App\Http\Middleware\LogRequests::class,
    ]);
})
```

---

### Step 4: Use Middleware

```php
// Di route
Route::get('/posts', [PostController::class, 'index'])
     ->middleware('log.requests');

// Di controller
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('log.requests');
    }
}
```

---

## 💡 Contoh Custom Middleware

### 1. CheckAge Middleware

```php
class CheckAge
{
    public function handle(Request $request, Closure $next, int $minAge = 18)
    {
        $age = $request->input('age');

        if ($age < $minAge) {
            return redirect('/')->with('error', 'Umur minimal ' . $minAge . ' tahun!');
        }

        return $next($request);
    }
}
```

**Use with parameter:**
```php
Route::get('/adult-content', function () {
    //...
})->middleware('check.age:21'); // Min age 21
```

---

### 2. ForceHttps Middleware

```php
class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
```

**Use:** Force HTTPS di production.

---

### 3. CheckMaintenanceMode Middleware

```php
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $isMaintenanceMode = cache()->get('maintenance_mode', false);

        if ($isMaintenanceMode && ! auth()->user()?->is_admin) {
            abort(503, 'We are currently under maintenance.');
        }

        return $next($request);
    }
}
```

---

## 📦 Bagian 3: Middleware Groups

### Default Groups

**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    // Web group (untuk web routes)
    $middleware->web(append: [
        \App\Http\Middleware\LogRequests::class,
    ]);

    // API group (untuk API routes)
    $middleware->api(append: [
        'throttle:60,1',
    ]);
})
```

**Web group** otomatis apply ke semua routes di `routes/web.php`:
- Session
- Cookie encryption
- CSRF protection
- dll

**API group** otomatis apply ke semua routes di `routes/api.php`:
- Throttle
- JSON responses
- No sessions

---

### Custom Group

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('admin', [
        'auth',
        \App\Http\Middleware\IsAdmin::class,
        \App\Http\Middleware\LogRequests::class,
    ]);
})
```

**Use:**
```php
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
});
```

---

## 📝 Latihan: Activity Logger

### Task: Log User Activity

**Middleware:** `app/Http/Middleware/LogActivity.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Skip logging untuk routes tertentu
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return $next($request);
    }
}
```

**Migration:**
```bash
php artisan make:migration create_activity_logs_table
```

```php
Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('action');
    $table->text('url');
    $table->string('ip');
    $table->text('user_agent')->nullable();
    $table->timestamp('created_at');
});
```

---

## 📖 Summary

- ✅ **Middleware**: Filter request before/after controller
- ✅ **Built-in**: auth, guest, verified, throttle
- ✅ **Custom Middleware**: make:middleware, handle() method
- ✅ **Middleware Groups**: web, api, custom groups
- ✅ **Use Cases**: Logging, authentication, rate limiting, custom logic

**Middleware membuat aplikasi lebih aman & trackable!** 🚪✅

---

[⬅️ Bab 22: Authorization](22-authorization.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 24: Session & Flash Messages ➡️](24-session-flash.md)