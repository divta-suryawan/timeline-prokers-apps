<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProkersModel extends Model
{
    use HasFactory;
    protected $table = 'prokers';
    protected $fillable = [
        'id',
        'name',
        'start',
        'end',
        'status',
        'ket',
        'id_user',
        'id_leadership',
    ];
    public function leadership()
    {
        return $this->belongsTo(LeadershipModel::class, 'id_leadership');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getUser($id_user)
    {
        $data = $this->join('prokers', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.role')
            ->where('prokers.id_user', '=', $id_user)
            ->first();
        return $data;
    }

    public function getLeadership($id_leadership)
    {
        $data = $this->join('prokers', '=', 'leadership.id')
            ->select('leadership.id', 'leadership.periode')
            ->where('prokers.id_leadership', '=', $id_leadership)
            ->first();
        return $data;
    }
}
