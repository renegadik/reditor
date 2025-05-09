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
            'type' => 'required|in:1,2,3,4,5', // string, set, list, hash, zset
            'key' =>  'required|string',
        ]);

        $key = $validated['key'];
        $type = (int)$validated['type'];

        $this->redis->create_key($request, $type, $key);
    
        return redirect()->route('home')->with('success', __('key_is_created'));
    }
    

    public function delete(Request $request) {
        $validated = $request->validate([
            'key'   => 'required|string',
        ]);

        $key = $validated['key'];
        
        $this->redis->delete_key($key);
        return redirect()->route('home');
    }

    public function update_key(Request $request) {
        $key = $request->input('key');
        $value = $request->input('value');

        $this->redis->update_key($key, $value);

        return redirect()->back()->with('success', __('key_is_update'));
    }

    public function delete_subkey(Request $request) {
        $request->validate([
            'key' => 'required|string',
            'sub_key' => 'required|string',
        ]);

        $key = $request->input('key');
        $sub_key = $request->input('sub_key');
        $sub_value = $request->input('sub_value');
        $type = Redis::type($key);

        $this->redis->delete_subkey($key, $type, $sub_value, $sub_key);

        return redirect()->back()->with('success', __('value_is_deleted'));
    }

    public function add_subkey(Request $request) {
        $key = $request->input('key');
        $new_key = $request->input('new_key');
        $new_value = $request->input('new_value');

        $type = Redis::type($key);

        $this->redis->add_subkey($type, $key, $new_key, $new_value);
    
        return redirect()->back()->with('success', __('value_is_added'));
    }

}
