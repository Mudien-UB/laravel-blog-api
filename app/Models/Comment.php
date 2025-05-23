<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        "content",
        "user_id",
        "blog_id",
        "parent_id",
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function blog(){
        return $this->belongsTo(Blog::class);
    }

    public function parent(){
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(){
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
