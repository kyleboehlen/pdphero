<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Create rules array
        $rules = [
            'name' => [
                'required', 'string', 'max:255'
            ],
            'password' => [
                'required', 'string', 'min:8', 'confirmed'
            ],
            'agree-tos' => [
                'required',
            ],
        ];

        // Add alpha email guard
        if(config('app.env') == 'testing')
        {
            // Get alpha emails
            $emails = config('test.alpha.emails');
            $rules['email'] = [
                'required', 'string', 'email', 'max:255', 'unique:users', Rule::in($emails),
            ];
        }
        else
        {
            $rules['email'] = [
                'required', 'string', 'email', 'max:255', 'unique:users'
            ];
        }

        return Validator::make($data, $rules, [
            'email.in' => 
                'Sorry, this email is not authorized to register yet! 
                 If you feel you\'re seeing this in error reach out to us at support@pdphero.com',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
