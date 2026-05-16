<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delay extends Model
{
    use HasFactory;

    // Tell Laravel the exact name of your database table
    protected $table = 'samplevsp'; 
    
    // If your table doesn't have Laravel's default 'created_at' and 'updated_at' columns, add this:
    public $timestamps = false;

    // The columns we are allowed to interact with
    protected $fillable = [
        'eqpt', 
        'sub_eqpt', 
        'shop_code', 
        'delay_duration', 
        'cumulative_delay', 
        'delivery_date', 
        'continue_y_n'
    ];
}