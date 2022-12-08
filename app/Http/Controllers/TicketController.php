<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if(Customer::checkIsAdmin()){
                $findTickets = Ticket::getTickets($request->page);
            }else{
                $findTickets = Ticket::getMyTickets($request->page);
            }
            return response()->json([
                'status' => true,
                'date' =>  $findTickets
            ]);
        } catch (\Throwable $th) {
            $this->ReturnThrowable($th);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $response = [
                'status'=> false,
                'message' => 'Ticket-opening failed. try again later'
            ]; 
            $createNew = Ticket::createNewTicket(Auth::user()->id, Auth::user()->email, $request->detail);
            if($createNew){
                $response = [
                    'status'=> true,
                    'message' => 'New ticket-open successfuly'
                ];
            }
            return response()->json($response);
        } catch (\Throwable $th) {
            $this->ReturnThrowable($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($searching, Request $request)
    {
        try {
            $search = Ticket::searchByKey($searching);
            return response()->json([
                'status' => true,
                'data' => $search
            ]);
        } catch (\Throwable $th) {
            $this->ReturnThrowable($th); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       try {
          $type = $request->type;
          $doUpdate = Ticket::updateTicketById($id, $type, $request);
          return response()->json($doUpdate);
        } catch (\Throwable $th) {
            $this->ReturnThrowable($th); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function ReturnThrowable($th){
        Log::info(__METHOD__ . ' e_code' . $th->getCode() . 
            '  e_message' . $th->getMessage()
            . ' e_line' . $th->getLine()
            . ' e_file' . $th->getFile());
            return response()->json([
                'status' => false,
                'error_code' => 'fail',
                'e_code' => $th->getCode(),
                'e_message' => $th->getMessage(),
                'message' => 'Data loading failed. please try again later',
            ]);
    }
}
