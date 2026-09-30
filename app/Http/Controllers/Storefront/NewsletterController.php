<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $exists = Subscriber::where('email', $request->email)->exists();

        if ($exists) {
            return back()->with('info', 'Email Anda sudah terdaftar.');
        }

        Subscriber::create(['email' => $request->email]);

        return back()->with('success', 'Berhasil berlangganan! Cek email Anda untuk penawaran menarik.');
    }
}
