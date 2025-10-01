# Bab 21: Authentication 🔐

[⬅️ Bab 20: Database Relationships](20-relationships.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 22: Authorization ➡️](22-authorization.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami konsep authentication vs authorization
- ✅ Bisa install dan setup Laravel Breeze
- ✅ Implement register, login, logout functionality
- ✅ Protecting routes dengan middleware auth
- ✅ Akses data user yang sedang login
- ✅ Customize authentication flow

---

## 🎯 Analogi Sederhana: Authentication = KTP ke Satpam

### Authentication vs Authorization

**Authentication** = **"Siapa kamu?"** (Verifikasi identitas)
```
👤 Kamu datang ke gedung kantor
🛡️ Satpam: "Boleh lihat KTP?"
👤 Kasih KTP
🛡️ Satpam: "OK, ini benar Budi" ✅
→ AUTHENTICATION: Verifikasi kamu adalah Budi
```

**Authorization** = **"Kamu boleh ngapain?"** (Verifikasi hak akses)
```
✅ Sudah masuk gedung (authenticated)
🛡️ Satpam: "Kamu boleh ke lantai 3, tapi TIDAK boleh ke lantai 5"
→ AUTHORIZATION: Verifikasi hak akses kamu
```

**Di Laravel:**
- **Authentication**: Login dengan email & password
- **Authorization**: User biasa vs Admin (nanti di Bab 22)

---

## 📚 Bagian 1: Laravel Breeze

### Apa itu Laravel Breeze?

**Laravel Breeze** = Starter kit authentication Laravel yang simple & minimal.

**Features:**
- ✅ Register
- ✅ Login
- ✅ Logout
- ✅ Password reset
- ✅ Email verification
- ✅ Profile management

**Stack:** Blade + Tailwind CSS (simple & clean!)

---

### Install Laravel Breeze

#### Step 1: Install via Composer

```bash
composer require laravel/breeze --dev
```

**Output:**
```
INFO  Discovering packages.

laravel/breeze installed successfully.
```

---

#### Step 2: Install Breeze

```bash
php artisan breeze:install blade
```

**Prompt:**
```
Which Breeze stack would you like to install?
  blade
  livewire
  react
  vue
  api

> blade
```

**Pilih:** `blade` (paling simple untuk pemula!)

**Output:**
```
INFO  Breeze scaffolding installed successfully.

Please execute the "npm install" && "npm run dev" commands to build your assets.
```

---

#### Step 3: Install NPM Dependencies

```bash
npm install
npm run dev
```

**Output:**
```
VITE v5.0.0  ready in 500 ms

➜  Local:   http://localhost:5173/
➜  Network: use --host to expose
```

**Biarkan `npm run dev` jalan di terminal!** Jangan di-close.

---

#### Step 4: Migrate Database

```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.

2014_10_12_000000_create_users_table .................. DONE
2014_10_12_100000_create_password_reset_tokens_table .. DONE
2019_08_19_000000_create_failed_jobs_table ............ DONE
2019_12_14_000001_create_personal_access_tokens_table . DONE
```

**Tables created:**
- `users` → Data user (name, email, password)
- `password_reset_tokens` → Token untuk reset password
- `failed_jobs` → Log failed jobs (advanced)
- `personal_access_tokens` → API tokens (advanced)

---

#### Step 5: Test Authentication

**Jalankan server:**
```bash
php artisan serve
```

**Buka browser:** `http://localhost:8000`

**Klik "Register"** → Isi form → Submit

**✅ Authentication berhasil diinstall!**

---

## 🗺️ Bagian 2: Authentication Flow

### Files yang Dibuat Breeze

**Routes:** `routes/auth.php`
```php
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create']);
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
});
```

**Controllers:** `app/Http/Controllers/Auth/*`
- `RegisteredUserController` → Register
- `AuthenticatedSessionController` → Login & Logout
- `PasswordResetLinkController` → Forgot password
- Dan lainnya...

**Views:** `resources/views/auth/*`
- `login.blade.php` → Login form
- `register.blade.php` → Register form
- `forgot-password.blade.php` → Forgot password form

**Middleware:** `auth`, `guest` (sudah disetup otomatis!)

---

### Register Flow

```
1. User buka /register
2. Isi form: Name, Email, Password, Confirm Password
3. Submit form → POST /register
4. Validate input
5. Hash password dengan bcrypt
6. Insert ke table users
7. Auto login user
8. Redirect ke /dashboard
```

**Code:** `app/Http/Controllers/Auth/RegisteredUserController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|confirmed|min:8',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), // Hash password!
    ]);

    Auth::login($user); // Auto login

    return redirect(RouteServiceProvider::HOME); // Redirect to dashboard
}
```

---

### Login Flow

```
1. User buka /login
2. Isi form: Email, Password
3. Submit form → POST /login
4. Validate credentials
5. Cek email & password di database
6. Jika cocok → Login user (create session)
7. Redirect ke /dashboard
8. Jika tidak cocok → Back dengan error message
```

**Code:**

```php
public function store(LoginRequest $request)
{
    $request->authenticate(); // Validate credentials

    $request->session()->regenerate(); // Regenerate session (security!)

    return redirect()->intended(RouteServiceProvider::HOME);
}
```

**Method `authenticate()`:**
```php
public function authenticate()
{
    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
```

**`Auth::attempt()`** → Cek email & password, jika cocok → login!

---

### Logout Flow

```
1. User klik Logout
2. Submit form → POST /logout
3. Logout user (hapus session)
4. Redirect ke homepage
```

**Code:**

```php
public function destroy(Request $request)
{
    Auth::logout(); // Logout user

    $request->session()->invalidate(); // Invalidate session
    $request->session()->regenerateToken(); // Regenerate CSRF token

    return redirect('/');
}
```

---

## 🔐 Bagian 3: Protecting Routes

### Middleware `auth`

**Middleware auth** = Satpam yang cek: "Kamu sudah login belum?"

**Jika belum login** → Redirect ke `/login`
**Jika sudah login** → Boleh akses route

---

### Cara 1: Middleware di Route

```php
// Route hanya bisa diakses jika sudah login
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

// Atau pakai name
Route::get('/dashboard', [DashboardController::class, 'index'])
     ->name('dashboard')
     ->middleware('auth');
```

---

### Cara 2: Middleware Group

```php
// Semua routes di dalam group harus login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::get('/settings', [SettingsController::class, 'index']);
});
```

---

### Cara 3: Middleware di Controller

```php
class PostController extends Controller
{
    public function __construct()
    {
        // Semua method harus login, kecuali index & show
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index() // Public (no auth)
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function create() // Protected (need auth)
    {
        return view('posts.create');
    }

    public function store(Request $request) // Protected
    {
        // ...
    }
}
```

---

### Middleware `guest`

**Middleware guest** = Kebalikan `auth`

**Jika sudah login** → Redirect ke `/dashboard`
**Jika belum login** → Boleh akses route

```php
// Route hanya bisa diakses jika BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create']);
    Route::get('/register', [RegisterController::class, 'create']);
});
```

**Gunanya:** User yang sudah login tidak perlu lihat halaman login/register lagi!

---

## 👤 Bagian 4: Akses Data User

### auth() Helper

```php
// Get currently authenticated user
$user = auth()->user();

echo $user->name;
echo $user->email;

// Check if user is authenticated
if (auth()->check()) {
    echo "User sudah login!";
} else {
    echo "User belum login!";
}

// Get user ID
$userId = auth()->id();
```

---

### Auth Facade

```php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$userId = Auth::id();

if (Auth::check()) {
    echo "Logged in!";
}
```

---

### Di Blade View

```blade
@auth
    {{-- User sudah login --}}
    <p>Welcome, {{ auth()->user()->name }}!</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endauth

@guest
    {{-- User belum login --}}
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Register</a>
@endguest
```

---

### Di Controller

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);

    // Tambahkan user_id otomatis dari user yang login
    $validated['user_id'] = auth()->id();

    Post::create($validated);

    return redirect()->route('posts.index');
}
```

---

## 🎨 Bagian 5: Customize Authentication

### Custom Redirect After Login

**File:** `app/Providers/RouteServiceProvider.php`

```php
public const HOME = '/dashboard'; // Default: /dashboard

// Atau custom logic
protected function redirectTo($request)
{
    if (auth()->user()->is_admin) {
        return '/admin/dashboard';
    }

    return '/dashboard';
}
```

---

### Custom Login Validation

**File:** `app/Http/Requests/Auth/LoginRequest.php`

```php
public function rules()
{
    return [
        'email' => 'required|string|email',
        'password' => 'required|string',
        'captcha' => 'required|captcha', // Tambah captcha
    ];
}
```

---

### Remember Me

**Sudah dihandle Breeze secara default!**

**Login form:**
```blade
<label>
    <input type="checkbox" name="remember">
    Remember me
</label>
```

**Controller:**
```php
Auth::attempt($credentials, $request->boolean('remember'));
```

**Gunanya:** User tidak perlu login lagi dalam 2 minggu (cookie expires).

---

## 💡 Bagian 6: Implement Auth di Blog

### Step 1: Add user_id to Posts

**Migration:**
```bash
php artisan make:migration add_user_id_to_posts_table
```

```php
public function up()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
```

**Run migration:**
```bash
php artisan migrate
```

---

### Step 2: Update Model Post

```php
class Post extends Model
{
    protected $fillable = [
        'user_id', // Add this
        'category_id',
        'title',
        // ...
    ];

    /**
     * 🔗 Post belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### Step 3: Update Model User

```php
class User extends Authenticatable
{
    // ... existing code ...

    /**
     * 🔗 User has many Posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

---

### Step 4: Protect Routes

```php
// Public routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Protected routes (hanya untuk user yang login)
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
```

---

### Step 5: Auto-assign user_id

**Controller:**
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);

    $validated['user_id'] = auth()->id(); // Auto-assign current user!

    Post::create($validated);

    return redirect()->route('posts.index')
                     ->with('success', 'Post created!');
}
```

---

### Step 6: Display Author

**View:** `resources/views/posts/show.blade.php`

```blade
<p><strong>Author:</strong> {{ $post->user->name }}</p>
<p><strong>Published:</strong> {{ $post->created_at->diffForHumans() }}</p>
```

**View:** `resources/views/posts/index.blade.php`

```blade
<small>By {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}</small>
```

---

## 📝 Latihan

### Latihan 1: My Posts

**Task:** Buat halaman "My Posts" yang hanya tampilkan posts milik user yang login.

**Hint:**
```php
// Route
Route::get('/my-posts', [PostController::class, 'myPosts'])->middleware('auth');

// Controller
public function myPosts()
{
    $posts = auth()->user()->posts()->latest()->paginate(10);
    return view('posts.my-posts', compact('posts'));
}
```

---

### Latihan 2: Only Author Can Edit

**Task:** User hanya bisa edit/delete post milik sendiri.

**Hint:**
```php
// Controller edit()
public function edit(Post $post)
{
    // Check if user is the author
    if ($post->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    return view('posts.edit', compact('post'));
}
```

**Better way:** Pakai Policy (nanti di Bab 22)!

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Laravel Breeze**: Install & setup authentication
- ✅ **Authentication Flow**: Register, Login, Logout
- ✅ **Protecting Routes**: Middleware `auth` & `guest`
- ✅ **Akses User Data**: `auth()->user()`, `auth()->check()`
- ✅ **Customize Auth**: Redirect, validation, remember me
- ✅ **Implement di Blog**: user_id, relationships, protect routes

**Aplikasi sekarang punya sistem user!** 🔐✅

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ **Authorization** (hak akses user)
- ✅ Gates untuk simple authorization
- ✅ Policies untuk model authorization
- ✅ User hanya bisa edit/delete post sendiri
- ✅ Role-based access (Admin vs User)

**Dari "Siapa kamu?" ke "Kamu boleh ngapain?"** 🛡️

---

## 🔗 Referensi

- 📖 [Laravel Breeze](https://laravel.com/docs/12.x/starter-kits#laravel-breeze)
- 📖 [Authentication](https://laravel.com/docs/12.x/authentication)
- 📖 [Middleware](https://laravel.com/docs/12.x/middleware)

---

[⬅️ Bab 20: Database Relationships](20-relationships.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 22: Authorization ➡️](22-authorization.md)

---

<div align="center">

**Authentication dikuasai! User management sudah bisa!** 🔐✅

**Lanjut ke Authorization untuk hak akses yang lebih detail!** 🛡️

</div>