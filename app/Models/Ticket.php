<?php

namespace App\Models;

use App\Mail\TicketCreate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Ticket extends Model
{
    
    use HasFactory;
    protected $collection = 'tickets';

    protected $fillable = [
        'ticket_id', 'customer_id', 'detail', 'status',
        'is_open', 'reply', 'replied_by', 'replied_time'
    ];
 
    protected $attributes = [
        'status' => false,
        'is_open' => false
    ];


    private static function generateRandomString($length = 12) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $findExist = Ticket::where(['ticket_id' => $randomString])->get();
        if(!$findExist->isEmpty()){ self::generateRandomString();}
        return $randomString;
    }


    public static function createNewTicket($ticketFor, $customerMail, $problem){
        $ticketId = self::generateRandomString();
        $ticket = [
        'ticket_id' => $ticketId,
        'customer_id' => $ticketFor,
        'detail' => $problem
        ];
       
        $create = Ticket::create($ticket);
        if($create){
            $mailData = [
                'title' => 'Your Open-Ticket is created successfully.',
                'pass' => $ticketId
            ];
            Mail::to($customerMail)->send(new TicketCreate($mailData));
        }
    }   
}
