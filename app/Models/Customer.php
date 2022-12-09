<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Customer extends User
{
    protected $table = 'users';

    protected $fillable = [
        'contact'
    ];
   

    public static function getCustomers($pageNum, $withAdmin=false){
        if($withAdmin){
            return self::paginate($pageNum+1);
        }
        return self::where(['is_admin' => false])->paginate($pageNum+1);
    }

    public function getSimple(){
        return [
            'customerId' => $this->id,
            'customerName' => $this->name,
            'email' => $this->email,
            'contact' => $this->contact
        ];
    }

    public function getCustomerId(){
        return $this->id;
    }

    public static function checkIsAdmin(){
        return Auth::user()->is_admin ? true:false;
    }

    public static function getCustomerById($id){
        return self::find($id)->getSimple();
    }

    public static function getCustomerEmail($id){
        return self::find($id)->email;
    }
}
