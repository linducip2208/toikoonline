<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('storefront.contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $contact = ContactMessage::create($data);

        $admins = User::role(['super_admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            $admins->each->notify(new ContactMessageReceived($contact));
        }

        return back()->with('success', 'Pesan Anda terkirim. Tim kami akan menghubungi Anda maksimal 1x24 jam.');
    }
}
