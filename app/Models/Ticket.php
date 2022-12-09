<?php

namespace App\Models;

use DB;
use App\Jobs\QueueSendTicketEmails;
use App\Mail\TicketCreate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDO;

class Ticket extends Model
{

    use HasFactory;
    protected $table = 'tickets';

    protected $fillable = [
        'ticket_id', 'customer_id', 'detail', 'status',
        'is_open', 'reply', 'replied_by', 'replied_time'
    ];

    protected $attributes = [
        'status' => false,
        'is_open' => false
    ];


    private static function generateRandomString($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $findExist = Ticket::where(['ticket_id' => $randomString])->get();
        if (!$findExist->isEmpty()) {
            self::generateRandomString();
        }
        return $randomString;
    }


    public static function createNewTicket($ticketFor, $customerMail, $problem)
    {
        try {
            $ticketId = self::generateRandomString();
            $ticket = [
                'ticket_id' => $ticketId,
                'customer_id' => $ticketFor,
                'detail' => $problem
            ];

            $create = Ticket::create($ticket);
            if ($create) {
                $mailData = [
                    'title' => 'Your Open-Ticket is created successfully.',
                    'ticketId' => $ticketId
                ];
                dispatch(new QueueSendTicketEmails($customerMail, $mailData));
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            self::rollback();
            Log::info(__METHOD__ . ' e_code' . $th->getCode() .
                '  e_message' . $th->getMessage()
                . ' e_line' . $th->getLine()
                . ' e_file' . $th->getFile());
        }
    }

    public static function findTicketById($objId, $ticketId)
    {
        $finding = self::where(['id' => $objId, 'ticket_id' => $ticketId])->orderBy('created_at', 'DESC')->get();
        return self::getTickets(false, $finding);
    }



    public static function getTickets($pageNum, $onHand = false, $myTickets = false)
    {
        $response = [];
        $tickets = $onHand;
        if (!$onHand) {
            $tickets = self::orderBy('created_at', 'DESC')->paginate($pageNum + 6);
        }
        if ($myTickets) {
            $tickets = self::where(['customer_id' => Auth::user()->id])->orderBy('created_at', 'DESC')->paginate($pageNum + 6);
        }
        foreach ($tickets as $thisTicket) {

            $tempArray = Customer::getCustomerById($thisTicket->customer_id);
            $tempArray['ticketObjId'] = $thisTicket->id;
            $tempArray['ticketId'] = $thisTicket->ticket_id;
            $tempArray['detail'] = $thisTicket->detail;
            $tempArray['status'] = $thisTicket->status ? true : false;
            $tempArray['isOpen'] = $thisTicket->is_open ? true : false;
            $tempArray['reply'] = $thisTicket->reply;
            $tempArray['repliedBy'] = isset($thisTicket->replied_by) ? Customer::getCustomerById($thisTicket->replied_by)['customerName'] : '-';
            $tempArray['repliedTime'] = isset($thisTicket->replied_time) ? self::timeConvert($thisTicket->replied_time, true) : '-';
            $tempArray['created'] = self::timeConvert($thisTicket->created_at);
            array_push($response, $tempArray);
        }

        return $response;
    }

    public static function searchByKey($searchKey)
    {
        $finding = self::join('users', 'users.id', 'tickets.customer_id')
            ->orWhere('tickets.ticket_id', 'like', '%' . $searchKey . '%')
            ->orWhere('tickets.detail', 'like', '%' . $searchKey . '%')
            ->orWhere('users.name', 'like', '%' . $searchKey . '%')
            ->orWhere('users.email', 'like', '%' . $searchKey . '%')
            ->orWhere('users.contact', 'like', '%' . $searchKey . '%')
            ->select('users.*', 'tickets.*', 'users.id as user_id', 'tickets.id as tick_id')
            ->get();

        return self::getTickets(false, $finding);
    }

    public static function updateTicketById($id, $type, $data)
    {
        try {
            $response = $updating = [];

            switch (true) {
                case ($type == 'markOpen'):
                    $updating['is_open'] = true;
                    break;
                case ($type == 'reply'):
                    $updating = [
                        'is_open' => true,
                        'reply' => $data->reply,
                        'replied_by' => Auth::user()->id,
                        'replied_time' => Carbon::now()->timestamp,
                    ];
                    break;
            }
            $doUpdate = self::where(['id' => $id])->update($updating);

            if ($doUpdate) {
                $response = [
                    'status' => true,
                    'message' => 'Ticket updated successfully'
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Ticket updating failed. try again later'
                ];
            }
            return $response;
        } catch (\Throwable $th) {
            self::rollback();
            Log::info(__METHOD__ . ' e_code' . $th->getCode() .
                '  e_message' . $th->getMessage()
                . ' e_line' . $th->getLine()
                . ' e_file' . $th->getFile());
        }
    }
 
    private static function timeConvert($timeFormat, $timestamp = false)
    {
        if ($timestamp) {
            return Carbon::createFromTimestamp($timeFormat)->setTimezone('Asia/Colombo')->format('d M Y h:i a');
        }
        return Carbon::parse($timeFormat)->setTimezone('Asia/Colombo')->format('d M Y h:i a');
    }
}
