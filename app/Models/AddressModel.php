<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "city",
        "country",
        "zip",
        "address",
        "reservation_token"
    ];
}
