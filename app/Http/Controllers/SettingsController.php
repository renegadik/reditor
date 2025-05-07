<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    protected SettingsService $settings;
    
    public function __construct(SettingsService $settings){
        $this->settings = $settings;
    }


    public function index() {
        $data = $this->settings->get_all_settings();
        return view('settings', ['settings' => $data]);
    }
    

    public function store(Request $request) {
        $validated = $request->validate([
            'language' => 'required|in:ru,en',
        ]);

        $this->settings->update_settings([
            'language' => $validated['language']
        ]);

        return redirect()->route('settings')->with('success', __('settings_language_message'));
    }
}
