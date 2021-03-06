<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowModel extends Model
{
    use HasFactory;

    public function getConcert()
    {
        return $this->hasMany(ConcertModel::class);
    }
}
