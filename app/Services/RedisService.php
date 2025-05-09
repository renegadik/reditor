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
        if (empty($value)) {
            return;
        }
        
        if (in_array($type, [1, 2, 3, 4, 5])) {
            $value = json_decode($value, true);
        }

        if ($type === 1) {
            Redis::set($key, $value);
        } elseif ($type === 3) {
            Redis::rpush($key, ...$value);
        } elseif ($type === 2) {
            Redis::sadd($key, ...$value);
        } elseif ($type === 4) {
            Redis::hmset($key, $value);
        } elseif ($type === 5) {
            foreach ($value as $member => $score) {
                Redis::zadd($key, $score, $member);
            }
        }
        return true;
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
            $this->create_key(3, $key, json_encode($list));
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
            $this->create_key($type, $key, json_encode($array));
        }
    
    }



}
