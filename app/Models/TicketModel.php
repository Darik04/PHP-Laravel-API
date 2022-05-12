<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "code",
        "row",
        "seat",
        "show"
    ];
}
