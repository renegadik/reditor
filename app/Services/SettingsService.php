<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class SettingsService
{
    protected string $redis_key = 'reditor_settings';

    private function create_settings() {
        $default_settings = [
            'language' => 'en',
        ];

        if (!Redis::exists($this->redis_key)) {
            Redis::hmset($this->redis_key, $default_settings);
        }
    }

    public function get_all_settings() {
        $this->create_settings();

        return Redis::hgetall($this->redis_key);
    }

    public function update_settings($data) {
        Redis::hmset($this->redis_key, $data);
    }

    
}
