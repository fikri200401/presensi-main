<?php

namespace App\Http\Controllers;

class InfoPageController extends Controller
{
    public function documentation()
    {
        return view('info.documentation');
    }

    public function itSupport()
    {
        return view('info.it-support');
    }

    public function systemStatus()
    {
        return view('info.system-status');
    }

    public function privacyPolicy()
    {
        return view('info.privacy-policy');
    }

    public function userGuide()
    {
        return view('info.user-guide');
    }
}
