<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        // In production: persist to a `settings` key-value table or config
        return back()->with('success', 'Settings updated successfully.');
    }
}
