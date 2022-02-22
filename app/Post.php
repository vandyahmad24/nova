<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Post extends Model
{
    use Actionable;
    protected $casts = [
        'published_at'=>'datetime',
        'published_until'=>'datetime'
    ];
    protected $fillable=[
        'is_published'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function Tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
