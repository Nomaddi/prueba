<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    public function tags(){
        return $this->belongsToMany(Tag::class, 'contacto_tag', 'contacto_id', 'tag_id');
    }
}
