<?php

/**
 * ============================================
 * ACCESSORS & MUTATORS
 * ============================================
 *
 * Accessors = Get attribute (retrieve data)
 * Mutators = Set attribute (store data)
 *
 * Gunakan untuk transform data saat get/set
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

// ============================================
// ACCESSORS (Laravel 9+ Syntax)
// ============================================

class Post extends Model
{
    protected $fillable = ['title', 'body', 'price'];

    /**
     * Accessor: Get title in UPPERCASE
     */
    protected function titleUppercase(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => strtoupper($attributes['title']),
        );
    }

    // Usage:
    // $post->title = "hello world";
    // echo $post->title_uppercase; // "HELLO WORLD"

    /**
     * Accessor: Get excerpt (first 100 chars)
     */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit($this->body, 100),
        );
    }

    // Usage:
    // echo $post->excerpt; // "This is the first 100 characters..."

    /**
     * Accessor: Format price as currency
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                'Rp ' . number_format($attributes['price'] ?? 0, 0, ',', '.'),
        );
    }

    // Usage:
    // $post->price = 50000;
    // echo $post->formatted_price; // "Rp 50.000"
}

// ============================================
// MUTATORS (Laravel 9+ Syntax)
// ============================================

class Post extends Model
{
    /**
     * Mutator: Auto-capitalize title when setting
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucfirst($value),
        );
    }

    // Usage:
    // $post->title = "hello world";
    // $post->save();
    // Database: "Hello world" (auto-capitalized!)

    /**
     * Mutator: Auto-generate slug from title
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) =>
                $value ?? Str::slug($attributes['title']),
        );
    }

    // Usage:
    // $post->title = "Laravel Tips";
    // $post->slug = null; // Or don't set at all
    // $post->save();
    // Database slug: "laravel-tips" (auto-generated!)

    /**
     * Mutator: Strip HTML tags from body
     */
    protected function body(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strip_tags($value),
        );
    }

    // Usage:
    // $post->body = "<p>Hello <script>alert('xss')</script></p>";
    // $post->save();
    // Database: "Hello" (HTML stripped!)
}

// ============================================
// OLD SYNTAX (Laravel 8 and below)
// ============================================

class Post extends Model
{
    /**
     * Accessor: get{Attribute}Attribute
     */
    public function getTitleUppercaseAttribute()
    {
        return strtoupper($this->title);
    }

    /**
     * Mutator: set{Attribute}Attribute
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst($value);
    }
}

// Both syntaxes work, tapi new syntax (Attribute) lebih clean!

// ============================================
// REAL-WORLD EXAMPLES
// ============================================

// Example 1: User Model
class User extends Model
{
    /**
     * Full name accessor
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }

    /**
     * Email mutator: Always lowercase
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
        );
    }

    /**
     * Password mutator: Auto-hash
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }
}

// Usage:
// $user->first_name = "John";
// $user->last_name = "Doe";
// echo $user->full_name; // "John Doe"

// $user->email = "USER@EXAMPLE.COM";
// $user->save();
// Database: "user@example.com" (lowercased!)

// $user->password = "secret123";
// $user->save();
// Database: "$2y$10$..." (hashed!)

// Example 2: Product Model
class Product extends Model
{
    /**
     * Price with tax
     */
    protected function priceWithTax(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $attributes['price'] * 1.11, // 11% tax
        );
    }

    /**
     * Discount price
     */
    protected function discountedPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $price = $attributes['price'];
                $discount = $attributes['discount_percent'] ?? 0;
                return $price - ($price * $discount / 100);
            },
        );
    }

    /**
     * Ensure price is never negative
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, $value),
        );
    }
}

// Usage:
// $product->price = 100000;
// echo $product->price_with_tax; // 111000

// $product->discount_percent = 20;
// echo $product->discounted_price; // 80000

// $product->price = -5000; // Trying to set negative
// $product->save();
// Database: 0 (mutator prevents negative!)

// ============================================
// CASTING vs ACCESSORS
// ============================================

// Casting: Simple type conversion
protected $casts = [
    'is_published' => 'boolean',
    'published_at' => 'datetime',
    'tags' => 'array',
];

// Accessor: Complex transformation
protected function statusLabel(): Attribute
{
    return Attribute::make(
        get: fn () => $this->is_published ? 'Published' : 'Draft',
    );
}

// When to use what?
// Casting: Simple types (bool, int, date, array, json)
// Accessor: Complex logic, formatted output

// ============================================
// BENEFITS
// ============================================

/*
✅ Data transformation centralized
✅ Reusable across application
✅ Clean controllers/views
✅ Type safety
✅ Consistent formatting
*/

// Example benefit:
// Without accessor (BAD):
// Everywhere in code:
echo strtoupper($post->title);
echo ucfirst($post->title);
// Inconsistent!

// With accessor (GOOD):
echo $post->title_uppercase;
// Consistent formatting everywhere!

echo "\n✅ Accessors & Mutators mastered!\n";
