<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'order_items';


    protected $fillable = [
        'order_id', 'product_id', 'quantity'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

     public function orders()
     {
         return $this->belongsTo('App\Order');
     }

     public function products()
     {
         return $this->belongsTo('App\Product');
     }
    

}
