<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('debug_cache')) {
    function debug_cache($key)
    {
        $CI =& get_instance();
        $data = $CI->cache->get($key);

        if ($data === false) {
            echo "<script>console.log('Debug Cache: MISS untuk key {$key}');</script>";
        } else {
            $json = json_encode($data);
            echo "<script>console.log('Debug Cache: HIT untuk key {$key} => {$json}');</script>";
        }
    }
}
