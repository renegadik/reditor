<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

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
            'ttl' => $ttl == -1 ? __('infinity') : $ttl,
        ];

        return $data;
    }

    public function get_all_keys() {
        $all_keys = Redis::keys('*');
        $data = [];

        foreach ($all_keys as $key) {
            $temp_data = $this->get_by_key($key);
            if (!is_string($temp_data['value'])) {
                $temp_data['value'] = json_encode($temp_data['value'], JSON_UNESCAPED_UNICODE);
            }
            if ($temp_data['key'] != 'reditor_settings') {
                $data[] = $temp_data;
            }
        }
        return $data;
    }

    public function delete_key($key) {
        return Redis::del($key);
    }

    public function add_subkey($type, $key, $new_key, $new_value) {
        if ($type === 2) {
            Redis::sadd($key, $new_value);
        } 
        elseif ($type == 3) {
            Redis::rpush($key, $new_value);
        } 
        elseif ($type == 4) {
            Redis::zadd($key, 0, $new_value);
        } 
        elseif ($type == 5) {
            Redis::hset($key, $new_key, $new_value);
        }
    }

    
    public function delete_subkey($key, $type, $sub_value, $sub_key) {
        if ($type === 2) {
            Redis::srem($key, $sub_value);
        } 
        elseif ($type === 3) {
            $list = $this->get_by_key($key)['value'];
            unset($list[$sub_key]);
            $this->delete_key($key);
            $this->create_key_for_update(3, $key, $list);
        } 
        elseif ($type === 4) {
            Redis::zrem($key, $sub_key);
        }
        elseif ($type === 5) {
            Redis::hdel($key, $sub_key);
        }
    }

    public function update_key($key, $value) {
        $type = Redis::type($key);
        if ($type === 1) {
            Redis::set($key, $value);
        } else {
            $array = $this->get_by_key($key)['value'];
            $index = array_key_first($value);
            $array[$index] = $value[$index];
            $this->delete_key($key);
            $this->create_key_for_update($type, $key, $array);
        }
    }

    public function create_key_for_update($type, $key, $value) {
        if (empty($value)) {
            return;
        }

        if ($type === 1) {
            Redis::set($key, $value);
        } elseif ($type === 3) {
            Redis::rpush($key, ...$value);
        } elseif ($type === 2) {
            Redis::sadd($key, ...$value);
        } elseif ($type === 4) {
            foreach ($value as $member => $score) {
                Redis::zadd($key, $score, $member);
            }
        } elseif ($type === 5) {
            Redis::hmset($key, $value);
        }
        return true;
    }

    public function create_key(Request $request, $type, $key) {
        if ($type === 1) {
            $request->validate(['value' => 'required|string']);
            Redis::set($key, $request->input('value'));
        }
        elseif ($type === 2) {
            $request->validate(['fields' => 'required|array']);
            Redis::sadd($key, ...$request->input('fields'));
        } 
        elseif ($type === 3) {
            $request->validate(['fields' => 'required|array']);
            Redis::rpush($key, ...$request->input('fields'));
        }
        elseif ($type === 4) {
            $request->validate([
                'fields' => 'required|array',
                'fields.*.key' => 'required|string',
                'fields.*.value' => 'required|numeric',
            ]);
            foreach ($request->input('fields') as $pair) {
                Redis::zadd($key, $pair['value'], $pair['key']);
            }
        }
        elseif ($type === 5) {
            $request->validate([
                'fields' => 'required|array',
                'fields.*.key' => 'required|string',
                'fields.*.value' => 'required',
            ]);
            $fields = [];
            foreach ($request->input('fields') as $pair) {
                $fields[$pair['key']] = $pair['value'];
            }
            Redis::hmset($key, $fields);
        }
    }

}
