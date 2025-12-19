<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    // Hàm xử lý gửi form (nếu sau này bạn muốn làm chức năng gửi mail)
    public function send(Request $request)
    {
        // Validate và gửi mail ở đây...
        return back()->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất!');
    }
}