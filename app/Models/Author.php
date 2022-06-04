<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use mysql_xdevapi\Table;

class Author extends Model
{
    use HasFactory;

    protected $table='authors';
    protected $fillable=[
        'first_name',
        'last_name',
        'book_id'
    ];
    public $withCount = ['comments', 'likes','reads'];
    protected $primaryKey = "id";
    public $timestamps = true;

    public function book(): HasMany
    {
        return $this->hasMany(Books::class, 'book_id');
    }
    public function write(): HasMany
    {
        return $this->hasMany(Written::class, 'written_id');
    }

}
