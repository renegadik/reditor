<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FrontendController extends Controller {

    public function home() {
        $response = Http::post(url('/api/get_all_keys'));
        $keys = $response->json();
        return view('home', ['keys' => $keys]);
    }

    public function show($key) {
        $response = Http::post(url('/api/get_key'), ['key' => $key]);
        $data = $response->json();
        return view('show', $data);
    }

    public function create() {
        return view('create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'key' => 'required|string',
            'type' => 'required|string|in:string,list,set,hash,zset',
            'value' => 'required',
        ]);

        $value = $validated['value'];
        if (in_array($validated['type'], ['list', 'set', 'zset', 'hash'])) {
            $value = json_decode($value, true);
        }

        Http::post(url('/api/create_key'), [
            'key' => $validated['key'],
            'type' => $validated['type'],
            'value' => $value,
        ]);

        return redirect()->route('home');
    }

    public function delete(Request $request) {
        $request->validate(['key' => 'required|string']);
        Http::post(url('/api/delete_key'), ['key' => $request->key]);
        return redirect()->route('home');
    }
}
