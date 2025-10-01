<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'body',
        'image',
        'category_id',
        'user_id',
        'is_published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);

                // Ensure unique slug
                $count = 1;
                while (static::where('slug', $post->slug)->exists()) {
                    $post->slug = Str::slug($post->title) . '-' . $count;
                    $count++;
                }
            }
        });
    }

    /**
     * Relationship: Post belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Post belongs to many Tags (Many-to-Many)
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Relationship: Post belongs to User (author)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter only published posts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Filter draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope: Search posts by title or body
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get excerpt (first 150 characters)
     */
    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->body), 150);
    }

    /**
     * Get reading time (estimate)
     */
    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->body));
        $minutes = ceil($words / 200); // Average reading speed

        return $minutes . ' min read';
    }
}
