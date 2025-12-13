<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'status',
        'subtotal',
        'shipping',
        'total',
        'payment_method',
        'payment_status',
        'payment_bank',
        'payment_reference',
        'payment_proof_path',
        'tracking_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function statusLabels()
    {
        return [
            'pending' => 'Menunggu',
            'processing' => 'Sedang diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public static function statusColors()
    {
        return [
            'pending' => 'bg-gray-200 text-gray-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return static::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute()
    {
        return static::statusColors()[$this->status] ?? 'bg-gray-200 text-gray-800';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tracks()
    {
        return $this->hasMany(OrderTrack::class)->latest();
    }
}

