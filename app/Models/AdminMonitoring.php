<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMonitoring extends Model
{
    /** @use HasFactory<\Database\Factories\AdminMonitoringFactory> */
    use HasFactory;

    protected $fillable = [
        "admin_id",
        "method",
        "type",
        "action",
    ];

    function admin() {
        return $this->belongsTo(Admin::class,"admin_id","id");
    }

}
