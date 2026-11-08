<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    public function chunks()
    {
        return $this->hasMany(Chunk::class, 'doc_id');
    }
}

