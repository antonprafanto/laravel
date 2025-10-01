# Bab 24: Session & Flash Messages 📝

[⬅️ Bab 23: Middleware](23-middleware.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 25: Debugging & Error Handling ➡️](25-debugging.md)

---

## 🎯 Learning Objectives

- ✅ Memahami konsep Session
- ✅ Menguasai Flash Messages untuk feedback user
- ✅ Implement success/error/warning messages
- ✅ Customize flash message styling

---

## 🎯 Analogi: Session = Sticky Notes

**Session** = **Sticky notes** yang nempel di mejamu selama kamu di kantor.

```
🏢 Kamu masuk kantor (login)
📝 Sticky note: "Nama: Budi, Role: Admin"
   ↓
🚶 Kamu pindah ruangan (buka halaman lain)
📝 Sticky note masih nempel! (session persist)
   ↓
🚪 Kamu pulang kantor (logout)
📝 Sticky note dibuang! (session destroyed)
```

**Flash Message** = **Post-it yang hilang setelah dibaca sekali**

```
✅ "Post berhasil dihapus!" (flash message)
🔄 Refresh page
❌ Message hilang! (flash = 1x show only)
```

---

## 📚 Bagian 1: Session Basics

### Store Data di Session

```php
// Store
session(['key' => 'value']);
session(['user_preference' => 'dark_mode']);

// Atau via request
$request->session()->put('cart', [1, 2, 3]);
```

---

### Retrieve Data

```php
// Get
$value = session('key');
$preference = session('user_preference', 'default'); // With default

// Check exists
if (session()->has('cart')) {
    $cart = session('cart');
}
```

---

### Remove Data

```php
// Forget specific key
session()->forget('cart');

// Flush all
session()->flush();
```

---

## ✨ Bagian 2: Flash Messages

### Flash Data (1x Show Only)

```php
// Controller
public function store(Request $request)
{
    Post::create($validated);

    // Flash message (hilang setelah 1x redirect)
    return redirect()->route('posts.index')
                     ->with('success', 'Post berhasil ditambahkan!');
}
```

---

### Display Flash di View

**Layout:** `resources/views/layouts/app.blade.php`

```blade
<div class="container">
    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Warning Message --}}
    @if (session('warning'))
        <div class="alert alert-warning">
            ⚠️ {{ session('warning') }}
        </div>
    @endif

    {{-- Info Message --}}
    @if (session('info'))
        <div class="alert alert-info">
            ℹ️ {{ session('info') }}
        </div>
    @endif

    @yield('content')
</div>
```

**CSS:**
```css
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    animation: fadeIn 0.3s;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}
.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
```

---

### Flash di CRUD Operations

**CREATE:**
```php
public function store(Request $request)
{
    Post::create($validated);
    return redirect()->route('posts.index')
                     ->with('success', '✅ Post berhasil ditambahkan!');
}
```

**UPDATE:**
```php
public function update(Request $request, Post $post)
{
    $post->update($validated);
    return redirect()->route('posts.show', $post)
                     ->with('success', '✅ Post berhasil diupdate!');
}
```

**DELETE:**
```php
public function destroy(Post $post)
{
    $post->delete();
    return redirect()->route('posts.index')
                     ->with('success', '✅ Post berhasil dihapus!');
}
```

**ERROR:**
```php
public function destroy(Post $post)
{
    try {
        $post->delete();
        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil dihapus!');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Gagal menghapus post: ' . $e->getMessage());
    }
}
```

---

## 🎨 Bagian 3: Advanced Flash Messages

### Multiple Flash Messages

```php
return redirect()->route('posts.index')
                 ->with([
                     'success' => 'Post berhasil dihapus!',
                     'info' => '10 posts tersisa.',
                 ]);
```

**View:**
```blade
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif
```

---

### Flash with Array Data

```php
$errors = [
    'Image too large',
    'Invalid format',
    'Title is required',
];

return redirect()->back()->with('errors_list', $errors);
```

**View:**
```blade
@if (session('errors_list'))
    <div class="alert alert-danger">
        <ul>
            @foreach (session('errors_list') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

---

### Auto-Dismiss Flash Message (JavaScript)

```blade
@if (session('success'))
    <div class="alert alert-success" id="flash-message">
        ✅ {{ session('success') }}
    </div>

    <script>
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            const message = document.getElementById('flash-message');
            if (message) {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 300);
            }
        }, 3000);
    </script>
@endif
```

---

## 📝 Component Flash Message (Reusable)

**File:** `resources/views/components/flash-message.blade.php`

```blade
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <strong>✅ Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>❌ Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <strong>⚠️ Warning!</strong> {{ session('warning') }}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <strong>ℹ️ Info!</strong> {{ session('info') }}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
    </div>
@endif
```

**Use in layout:**
```blade
<div class="container">
    <x-flash-message />

    @yield('content')
</div>
```

**Much cleaner!** ✨

---

## 📖 Summary

- ✅ **Session**: Store data across requests (like sticky notes)
- ✅ **Flash Messages**: 1x show messages after redirect
- ✅ **Types**: success, error, warning, info
- ✅ **Implementation**: `with()`, `session()`, `@if (session())`
- ✅ **Best Practices**: Component reusability, auto-dismiss, styling

**Flash messages improve UX drastically!** 📝✅

---

[⬅️ Bab 23: Middleware](23-middleware.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 25: Debugging & Error Handling ➡️](25-debugging.md)