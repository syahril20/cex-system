<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Cache Driver Configuration
|--------------------------------------------------------------------------
|
| adapter: driver utama cache (file, apc, memcached, redis, dummy)
| backup : driver cadangan kalau adapter utama gagal
|
*/

$config['adapter'] = 'file';
$config['backup'] = 'dummy';

/*
| 'file' driver settings
*/
$config['cache_path'] = APPPATH . 'cache/'; // default: application/cache/
$config['key_prefix'] = ''; // kalau mau prefix tambahan
