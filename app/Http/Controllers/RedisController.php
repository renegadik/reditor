<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{

    private $types_list = [0 => 'none', 1 => 'string', 2 => 'set', 3 => 'list', 4 => 'zset', 5 => 'hash'];

    private function get_by_key($key) {
        $type = Redis::type($key);
        $ttl = Redis::ttl($key);

        $value = null;

        if ($type === 1) {
            $value = Redis::get($key);
        } elseif ($type === 2) {
            $value = Redis::smembers($key);
        } elseif ($type === 3) {
            $value = Redis::lrange($key, 0, -1);
        } elseif ($type === 4) {
            $value = Redis::zrange($key, 0, -1, ['withscores' => true]);
        } elseif ($type === 5) {
            $value = Redis::hgetall($key);
        }

        return [
            'key' => $key,
            'type' => $this->types_list[$type] ?? 'unknown',
            'type_id' => $type,
            'value' => $value,
            'ttl' => $ttl == -1 ? 'entiti' : $ttl,
        ];
    }


    public function home()
    {
        $all_keys = Redis::keys('*');
        $data = [];

        foreach ($all_keys as $key) {
            $temp_data = $this->get_by_key($key);
            if (!is_string($temp_data['value'])) {
                $temp_data['value'] = 'array';
            }
            $data[] = $temp_data;
        }

        return view('home', ['keys' => $data]);
    }

    public function show($key) {
        $data = $this->get_by_key($key);
        return view('show', $data);
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key'   => 'required|string',
            'type'  => 'required|string|in:string,list,set,hash,zset',
            'value' => 'required',
        ]);

        $key = $validated['key'];
        $type = $validated['type'];
        $value = $validated['value'];

        if (in_array($type, ['list', 'set', 'zset', 'hash'])) {
            $value = json_decode($value, true);
        }

        Redis::del($key);

        if ($type === 'string') {
            Redis::set($key, $value);
        } elseif ($type === 'list') {
            Redis::rpush($key, ...$value);
        } elseif ($type === 'set') {
            Redis::sadd($key, ...$value);
        } elseif ($type === 'hash') {
            Redis::hmset($key, $value);
        } elseif ($type === 'zset') {
            foreach ($value as $member => $score) {
                Redis::zadd($key, $score, $member);
            }
        }

        return redirect()->route('home');
    }

    public function delete(Request $request)
    {
        $request->validate(['key' => 'required|string']);
        Redis::del($request->key);
        return redirect()->route('home');
    }
}
