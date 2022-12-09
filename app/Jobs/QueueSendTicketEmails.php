<?php

namespace App\Jobs;

use App\Mail\TicketCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QueueSendTicketEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $email;
    protected $mailData;
    public function __construct($email, $mailData)
    {
        $this->email = $email;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->email)->send(new TicketCreate($this->mailData));
        } catch (\Throwable $th) {
            Log::info(__METHOD__ . ' e_code' . $th->getCode() .
                '  e_message' . $th->getMessage()
                . ' e_line' . $th->getLine()
                . ' e_file' . $th->getFile());
        }
    }
}
