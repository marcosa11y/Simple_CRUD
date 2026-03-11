<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // ensure a default status is available on new models; this helps tests and
    // client code rely on the "pending" default even before the record is
    // refreshed from the database.
    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = ['user_id', 'total', 'status'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
