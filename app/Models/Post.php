<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saves()
    {
        return $this->belongsToMany(User::class, 'posts_saves');
    }

    public function scopeSearch($query)
    {
        if (!request()->filled('query')) {
            return $query;
        }
        $key = request('query');
        return $query->where('word', 'like', "%{$key}%")
            ->orWhere('meaning', 'like', "%{$key}%")
            ->orWhere('body', 'like', "%{$key}%");
    }
}
