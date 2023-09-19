<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadershipModel extends Model
{
    use HasFactory;
    protected $table = 'leadership';
    protected $fillable = [
        'id',
        'periode',
        'created_at',
        'updated_at'
    ];
}
