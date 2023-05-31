<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fitur extends Model
{
    use HasFactory;

    protected $table = 'fiturs';
    protected $fillable = [
        'image', 'title', 'content',
    ];


    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('/img/' . $value),
        );
    }
}
