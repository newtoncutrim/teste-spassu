<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'publisher',
        'edition',
        'year_of_publication',
        'price',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author', 'book_id', 'author_id');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'book_topic', 'book_id', 'topic_id');
    }
}
