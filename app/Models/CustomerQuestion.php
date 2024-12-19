<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'question',
        'answer',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class,"customer_id","id");
    }
    public function admin() {
        return $this->belongsTo(Admin::class,"admin_id","id");
    }

}
