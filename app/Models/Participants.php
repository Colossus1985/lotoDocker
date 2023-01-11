<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participants extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo(Groups::class, 'id');
    }

    protected $fillable = [
        'firstName', 
        'lastName', 
        'nameGroup', 
        'pseudo',
        'email',
        'tel',
        'amount',
        'totalAmount',
        'id_group',
    ];
}
