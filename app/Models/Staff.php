<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'staff_id',
        'role',
        'position',
        'salary',
        'phone',
        'photo',
        'notes',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canViewAllSalaries()
    {
        return $this->role === 'owner';
    }
}
