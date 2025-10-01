<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relationship: Tag belongs to many Posts (Many-to-Many)
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Get posts count for this tag
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }
}
