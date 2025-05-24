<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'description',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_topic', 'topic_id', 'book_id');
    }
}
