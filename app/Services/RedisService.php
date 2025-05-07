<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisService {

    private function get_type_name($type) {
        $types = [0 => 'none', 1 => 'string', 2 => 'set', 3 => 'list', 4 => 'zset', 5 => 'hash'];
        return $types[$type];
    }

    public function get_by_key($key) {
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

        $data = [
            'key' => $key,
            'type' => $this->get_type_name($type) ?? 'unknown',
            'type_id' => $type,
            'value' => $value,
            'ttl' => $ttl == -1 ? 'entiti' : $ttl,
        ];

        return $data;
    }

    public function get_all_keys() {
        $all_keys = Redis::keys('*');
        $data = [];

        foreach ($all_keys as $key) {
            $temp_data = $this->get_by_key($key);
            if (!is_string($temp_data['value'])) {
                $temp_data['value'] = 'array';
            }
            $data[] = $temp_data;
        }
        return $data;
    }

    public function create_key($type, $key, $value) {

        if (in_array($type, ['list', 'set', 'zset', 'hash'])) {
            $value = json_decode($value, true);
        }

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
        return true;
    }

    public function delete_key($key) {
        return Redis::del($key);
    }

    public function delete_subkey($key, $type, $subvalue, $subKey) {
        switch ($type) {
            case 3:
                $list = Redis::lrange($key, 0, -1);
            
                if (isset($list[$subKey])) {
                    unset($list[$subKey]);
                    Redis::del($key);
                    foreach (array_values($list) as $item) {
                        Redis::rpush($key, $item);
                    }
                }
                break;

            case 2:
                Redis::srem($key, $subvalue);
                break;

            case 4:
                Redis::zrem($key, $subKey);
                break;

            case 5: 
                Redis::hdel($key, $subKey);
                break;
        }
    }

    public function add_subkey($type, $key, $new_key, $new_value) {
        switch ($type) {
            case 3: 
                $a = Redis::rpush($key, $new_value);
                break;

            case 2: 
                Redis::sadd($key, $new_value);
                break;

            case 4:
                Redis::zadd($key, 0, $new_value);
                break;

            case 5:
                Redis::hset($key, $new_key, $new_value);
                break;

            default:
                return redirect()->back()->with('error', 'Тип ключа не поддерживается для добавления');
        }
    }
}
