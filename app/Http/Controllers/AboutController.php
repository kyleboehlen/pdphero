<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Faq;

class AboutController extends Controller
{
    public function index()
    {
        return view('about');
    }

    public function faqs()
    {
        $faqs = Faq::all();
        return view('faqs')->with(['faqs' => $faqs]);
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function tos()
    {
        return view('tos');
    }
}
