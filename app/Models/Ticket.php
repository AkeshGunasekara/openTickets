<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    
    use HasFactory;
    protected $collection = 'ticket';

    protected $fillable = [
        'ticket_id', 'customer_id', 'contact', 'detail', 'status',
        'is_open', 'reply', 'replied_by', 'replied_time'
    ];
 
    protected $attributes = [
        'status' => false,
        'is_open' => false
    ];
}
