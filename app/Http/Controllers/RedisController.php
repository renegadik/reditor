<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisService;

class RedisController extends Controller
{
    protected RedisService $redis;
    
    public function __construct(RedisService $redis){
        $this->redis = $redis;
    }

    public function home() {
        $data = $this->redis->get_all_keys();
        return view('home', ['keys' => $data]);
    }

    public function show($key) {
        $data = $this->redis->get_by_key($key);
        return view('show', $data);
    }

    public function create() {
        return view('create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'key'   => 'required|string',
            'type'  => 'required|string|in:string,list,set,hash,zset',
            'value' => 'required',
        ]);

        $key = $validated['key'];
        $type = $validated['type'];
        $value = $validated['value'];

        $this->redis->create_key($type, $key, $value);

        return redirect()->route('home');
    }

    public function delete(Request $request) {
        $validated = $request->validate([
            'key'   => 'required|string',
        ]);

        $key = $validated['key'];
        
        $this->redis->delete_key($key);
        return redirect()->route('home');
    }


}
