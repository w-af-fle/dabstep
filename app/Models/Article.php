<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public function user(){
        return belongsTo(User::class);
    }

    public function comment(){
        return $this->hasMany(comment::class);
    }
}
