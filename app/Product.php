<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'products';

    protected $fillable = [
        'name', 'price', 'description', 'category', 'stock', 'images'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

     public function order_items()
     {
         return $this->hasMany('App\OrderItem');
     }
    
}
