<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

// Mail
use App\Mail\SupportRequest;

// Requests
use App\Http\Requests\Support\SubmitEmailRequest;

class SupportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    
    public function showEmailForm()
    {
        return view('support.email-form');
    }

    public function submitEmailForm(SubmitEmailRequest $request)
    {
        $user = $request->user();
        $message = $request->get('message');

        Mail::send(new SupportRequest($user, $message));

        return redirect()->route('support.email.form')->withErrors([
            'success' => "We have sent your request to PDPHero support, they will reply to you at $user->email shortly :)",
        ]);
    }
}
