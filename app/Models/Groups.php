<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->hasMany(Participants::class, 'id_group');
    }

    protected $fillable = [
        'nameGroup'
    ];
}