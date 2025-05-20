<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'plan_id', 'amount', 'platform', 'receipt', 'start_date', 'end_date', 'status'];
}
