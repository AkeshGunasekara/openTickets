<?php

namespace App\Http\Controllers\Auth;
use DB;
use App\Http\Controllers\Controller;
use App\Jobs\QueueSendAccountCreateEmail;
use App\Mail\AccountCreate;
use App\Models\Ticket;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
       try {
        Log::info(__METHOD__); 
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'contact' => 'required|string|min:10',
            // 'detail' => 'required|string',
            // // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $password = Str::random(8);
         
        $user =User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'contact' => $request->contact,
        ]);
        
        event(new Registered($user));
        $check = Auth::login($user);

        $mailData = [
            'title' => 'Your Open-Ticket account is created successfully.',
            'pass' => $password
        ]; 
        dispatch(new QueueSendAccountCreateEmail($request->email, $mailData));
        Ticket::createNewTicket($user->id,  $request->email, $request->detail);
        return redirect(RouteServiceProvider::HOME);
       } catch (\Throwable $th) {
         Log::info(__METHOD__.' error '.$th->getMessage(). ' e_file '. $th->getFile(). ' e_line '.$th->getLine());
         DB::rollback();
       }
    }
}
