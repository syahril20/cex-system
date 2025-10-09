<?php

if (!function_exists('env_set_value')) {
    /**
     * Update atau tambahkan value di file .env
     *
     * @param string $key   Nama variabel ENV
     * @param string $value Nilai baru
     * @return bool
     */
    function env_set_value($key, $value)
    {
        $env_path = FCPATH . '/.env'; // sesuaikan lokasi .env kamu
        if (!file_exists($env_path)) {
            log_message('error', '[ENV] File .env tidak ditemukan di: ' . $env_path);
            return false;
        }

        $contents = file_get_contents($env_path);
        $pattern = "/^{$key}=.*/m";

        // escape karakter khusus
        $value = str_replace('"', '\"', $value);

        if (preg_match($pattern, $contents)) {
            // update baris yang sudah ada
            $contents = preg_replace($pattern, "{$key}=\"{$value}\"", $contents);
        } else {
            // tambahkan baris baru
            $contents .= PHP_EOL . "{$key}=\"{$value}\"";
        }

        if (file_put_contents($env_path, $contents) === false) {
            log_message('error', "[ENV] Gagal menulis file .env untuk key: {$key}");
            return false;
        }

        log_message('debug', "[ENV] Berhasil update {$key} di file .env");
        return true;
    }
}
