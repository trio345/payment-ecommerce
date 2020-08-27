<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'transaction_id', 'payment_type', 'gross_amount', 'transaction_time','transaction_status', 'cash', 'change'
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
    
}
