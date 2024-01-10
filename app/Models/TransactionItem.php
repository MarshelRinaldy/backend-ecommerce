<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'users_id',
        'products_id',
        'transactions_id',
        'quantity',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class, 'transactions_id', 'id');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'products_id');
    }
    //(Product::class, 'id', 'products_id')
    //yaang 'id' merupakan yang dituju atau foreignKey, sedangkan products_id merupkaan id lokalKey nya
}
