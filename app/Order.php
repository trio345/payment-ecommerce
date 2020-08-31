<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status', 'amount'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

     public function customers()
     {
        return $this->belongsTo('App\Customer');
     }

     public function order_details()
     {
         return $this->hasMany('App\OrderDetail', 'order_id');
     }

     public function payments()
     {
         return $this->hasOne('App\Payment');
     }

     

}
