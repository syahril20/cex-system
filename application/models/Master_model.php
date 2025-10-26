<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Master_model extends CI_Model
{
    private $api_base_url;
    private $token;
    private $username;
    private $secret_key;

    public function __construct()
    {
        parent::__construct();
        $this->api_base_url = getenv('API_CEX_BASE_URL');
        $this->username = getenv('API_CEX_USERNAME');
        $this->secret_key = getenv('API_CEX_SECRET_KEY');
        $this->token = getenv('API_CEX_AUTH');
        $this->load->helper('env');
    }

    // Helper: mask sensitive fields in request bodies
    private function mask_sensitive($data)
    {
        try {
            if (is_array($data)) {
                $masked = [];
                foreach ($data as $k => $v) {
                    if (in_array(strtolower($k), ['secret_key', 'password', 'token', 'authorization', 'auth'])) {
                        $masked[$k] = '***MASKED***';
                    } else {
                        $masked[$k] = $this->mask_sensitive($v);
                    }
                }
                return $masked;
            } elseif (is_object($data)) {
                $arr = (array) $data;
                return $this->mask_sensitive($arr);
            } else {
                return $data;
            }
        } catch (\Exception $e) {
            return '[masking_error]';
        }
    }

    // Helper: unified debug logging for errors with url, body and response
    private function log_debug_error($label, $url = null, $body = null, $response = null)
    {
        $entry = [
            'label' => $label,
            'url' => $url,
            'body' => $this->mask_sensitive($body),
            'response' => $this->mask_sensitive($response),
            'time' => date('c')
        ];
        // Use debug level as requested
        log_message('debug', '[CEX DEBUG] ' . json_encode($entry));
    }

    // ============================================================
    // TOKEN MANAGEMENT
    // ============================================================
    private function get_token($force_refresh = false)
    {
        if (!$force_refresh && $this->token) {
            return $this->token;
        }

        $token = $this->generate_token($this->username, $this->secret_key);
        if ($token) {
            $this->token = $token;
            return $this->token;
        }

        log_message('error', 'Failed to generate new API token!');
        $this->log_debug_error('generate_token_failed', $this->api_base_url . 'v2/service/auth/generate_token', ['username' => $this->username], 'no_response');
        return null;
    }

    public function generate_token($username, $secret_key)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = $this->api_base_url . 'v2/service/auth/generate_token';

            $response = $client->request('POST', $url, [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => [
                    'username' => $username,
                    'secret_key' => $secret_key
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['status']) && $result['status'] == 200 && isset($result['data']['token'])) {
                $token = $result['data']['token'];

                // Simpan token baru ke .env
                $this->load->helper('env');
                env_set_value('API_CEX_AUTH', $token);

                return $token;
            }

            log_message('error', '[CEX] Invalid token response: ' . json_encode($result));
            $this->log_debug_error('generate_token_invalid_response', $url, ['username' => $username, 'secret_key' => '***MASKED***'], $result);
            return null;

        } catch (RequestException $e) {
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', '[CEX] Token Request Error: ' . $msg);
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null;
            $this->log_debug_error('generate_token_request_exception', isset($url) ? $url : $this->api_base_url . 'v2/service/auth/generate_token', ['username' => $username, 'secret_key' => '***MASKED***'], $responseBody ?? $msg);
            return null;
        } catch (\Exception $e) {
            log_message('error', '[CEX] General Error (generate_token): ' . $e->getMessage());
            $this->log_debug_error('generate_token_exception', isset($url) ? $url : $this->api_base_url . 'v2/service/auth/generate_token', ['username' => $username, 'secret_key' => '***MASKED***'], $e->getMessage());
            return null;
        }
    }


    // ============================================================
    // GENERIC GET REQUEST
    // ============================================================
    private function send_request($path, $retry = true)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = $this->api_base_url . $path;
            $token = $this->get_token();

            if (!$token) {
                log_message('error', 'No valid token available for GET request.');
                $this->log_debug_error('send_request_no_token', $url, null, 'no_token');
                return [];
            }

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($retry && isset($result['status']) && (int) $result['status'] === 401 || (int) $result['status'] === 0) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (send_request). Aborting retry.');
                    $this->log_debug_error('send_request_token_refresh_failed', $url, null, $result);
                    return [];
                }
                return $this->send_request($path, false);
            }

            if (isset($result['status']) && $result['status'] == 200) {
                return $result['data'];
            } else {
                // log debug of unsuccessful response
                $this->log_debug_error('send_request_non_200', $url, null, $result);
                return [];
            }

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException send_request).');
                    $resp = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
                    $this->log_debug_error('send_request_requestexception_token_refresh_failed', isset($url) ? $url : null, null, $resp);
                    return [];
                }
                return $this->send_request($path, false);
            }
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'GET Request Error: ' . $msg);
            $this->log_debug_error('send_request_request_exception', isset($url) ? $url : null, null, $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (send_request): ' . $e->getMessage());
            $this->log_debug_error('send_request_exception', isset($url) ? $url : null, null, $e->getMessage());
            return [];
        }
    }

    // ============================================================
    // PUBLIC API FUNCTIONS
    // ============================================================
    public function get_rates()
    {
        return $this->send_request(getenv('API_CEX_RATES_ENDPOINT'));
    }
    
        public function get_rates_id()
    {
        return $this->send_request(getenv('API_CEX_RATES_ENDPOINT'));
    }

    public function get_commodity()
    {
        return $this->send_request(getenv('API_CEX_COMMODITY_ENDPOINT'));
    }

    // ============================================================
    // TRACKING
    // ============================================================
    public function get_tracking($airwaybill, $retry = true)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = $this->api_base_url . getenv('API_CEX_TRACKING_ENDPOINT');
            $token = $this->get_token();

            if (!$token) {
                log_message('error', 'No valid token for get_tracking()');
                $this->log_debug_error('get_tracking_no_token', $url, ['airwaybill' => $airwaybill], 'no_token');
                return [];
            }

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => ['airwaybill' => $airwaybill]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            echo "<script>console.log('Debugssss: ', " . json_encode($result) . ');</script>';

            if (($retry && isset($result['status']) && (int) $result['status'] === 401) || (int) $result['status'] === 0) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (get_tracking).');
                    $this->log_debug_error('get_tracking_token_refresh_failed', $url, ['airwaybill' => $airwaybill], $result);
                    return [];
                }
                return $this->get_tracking($airwaybill, false);
            }

            return (isset($result['status']) && $result['status'] == 200)
                ? $result
                : (function () use ($url, $airwaybill, $result) {
                    $this->log_debug_error('get_tracking_non_200', $url, ['airwaybill' => $airwaybill], $result);
                    return [];
                })();

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException get_tracking).');
                    $resp = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
                    $this->log_debug_error('get_tracking_requestexception_token_refresh_failed', isset($url) ? $url : null, ['airwaybill' => $airwaybill], $resp);
                    return [];
                }
                return $this->get_tracking($airwaybill, false);
            }
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'Tracking Request Error: ' . $msg);
            $this->log_debug_error('get_tracking_request_exception', isset($url) ? $url : null, ['airwaybill' => $airwaybill], $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (get_tracking): ' . $e->getMessage());
            $this->log_debug_error('get_tracking_exception', isset($url) ? $url : null, ['airwaybill' => $airwaybill], $e->getMessage());
            return [];
        }
    }

    public function get_latest_tracking_status($airwaybill)
    {
        $result = $this->get_tracking($airwaybill);
        if (empty($result) || empty($result['trackings'])) {
            return null;
        }

        $trackings = $result['trackings'];
        usort($trackings, function ($a, $b) {
            return strtotime($b['date'] . ' ' . $b['time']) <=> strtotime($a['date'] . ' ' . $a['time']);
        });
        return $trackings[0]['status'] ?? null;
    }

    public function get_all_trackings($airwaybill)
    {
        $result = $this->get_tracking($airwaybill);
        return !empty($result['trackings']) ? $result['trackings'] : [];
    }

    // ============================================================
    // SHIPMENT
    // ============================================================
    public function create_shipment($data, $retry = true)
    {
        return $this->post_request('/v2/service/shipment/create', $data, $retry);
    }

    public function create_shipment_with_code($code, $data, $retry = true)
    {
        return $this->post_request('/v2/service/shipment/create/' . urlencode($code), $data, $retry);
    }

    public function set_outbound($airwaybill, $retry = true)
    {
        return $this->post_request('/v2/service/shipment/set_outbound', ['airwaybill' => $airwaybill], $retry);
    }

    // public function upload_shipment_image($airwaybill, $file_path, $retry = true)
    // {
    //     try {
    //         // validate file
    //         if (!file_exists($file_path) || !is_readable($file_path)) {
    //             log_message('error', 'upload_shipment_image: file not found or not readable: ' . $file_path);
    //             $this->log_debug_error('upload_shipment_image_file_missing', null, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], 'file_missing');
    //             return [];
    //         }

    //         $client = new Client(['timeout' => 20, 'allow_redirects' => true]);
    //         $url = $this->api_base_url . 'v2/service/shipment/upload_shipment_image';
    //         $token = $this->get_token();

    //         if (!$token) {
    //             log_message('error', 'No valid token for upload_shipment_image()');
    //             $this->log_debug_error('upload_shipment_image_no_token', $url, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], 'no_token');
    //             return [];
    //         }

    //         // prepare headers; DO NOT set Content-Type for multipart (Guzzle will add boundary)
    //         $headers = [
    //             'Authorization' => "Bearer $token",
    //             'Accept' => 'application/json'
    //         ];
    //         // include CI session cookie if present (to mimic curl's Cookie header)
    //         if (!empty($_COOKIE['ci_session'])) {
    //             $headers['Cookie'] = 'ci_session=' . $_COOKIE['ci_session'];
    //         }

    //         // multipart fields: airwaybill as plain field, filename as uploaded file
    //         $multipart = [
    //             ['name' => 'airwaybill', 'contents' => $airwaybill],
    //             ['name' => 'filename', 'contents' => fopen($file_path, 'r'), 'filename' => basename($file_path)]
    //         ];

    //         // helper to build a curl-like debug command that matches the provided example
    //         $build_curl_debug = function ($url, $headers, $multipart) {
    //             try {
    //                 $cmd = "curl --location " . escapeshellarg($url);
    //                 foreach ($headers as $hk => $hv) {
    //                     $cmd .= " \\\n  --header " . escapeshellarg($hk . ': ' . $hv);
    //                 }
    //                 foreach ($multipart as $part) {
    //                     if (isset($part['filename']) && isset($part['contents']) && is_resource($part['contents']) && isset($part['filename'])) {
    //                         // file part
    //                         $cmd .= " \\\n  --form " . escapeshellarg($part['name'] . '=@' . $part['filename']);
    //                     } else {
    //                         // normal field; include quotes to match example
    //                         $val = is_string($part['contents']) ? $part['contents'] : json_encode($part['contents']);
    //                         $cmd .= " \\\n  --form " . escapeshellarg($part['name'] . '="' . $val . '"');
    //                     }
    //                 }
    //                 return $cmd;
    //             } catch (\Exception $e) {
    //                 return '[failed_to_build_curl_debug]';
    //             }
    //         };

    //         $curl_debug = $build_curl_debug($url, $headers, $multipart);
    //         log_message('debug', '[CEX CURL] ' . $curl_debug);

    //         $response = $client->request('POST', $url, [
    //             'headers' => $headers,
    //             'multipart' => $multipart
    //         ]);

    //         $result = json_decode($response->getBody()->getContents(), true);

    //         if ($retry && isset($result['status']) && (int) $result['status'] === 401) {
    //             $this->get_token(true);
    //             if (!$this->token) {
    //                 log_message('error', 'Token refresh failed (upload_shipment_image).');
    //                 $this->log_debug_error('upload_shipment_image_token_refresh_failed', $url, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], ['response' => $result, 'curl' => $curl_debug]);
    //                 return [];
    //             }
    //             return $this->upload_shipment_image($airwaybill, $file_path, false);
    //         }

    //         return (isset($result['status']) && $result['status'] == 200) ? $result : (function () use ($url, $airwaybill, $file_path, $result, $curl_debug) {
    //             $this->log_debug_error('upload_shipment_image_non_200', $url, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], ['response' => $result, 'curl' => $curl_debug]);
    //             log_message('debug', '[CEX CURL] ' . $curl_debug);
    //             return [];
    //         })();

    //     } catch (RequestException $e) {
    //         // attempt to build curl debug if not already built
    //         if (!isset($curl_debug)) {
    //             $curl_debug = '[no_curl_debug]';
    //         }

    //         if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
    //             $this->get_token(true);
    //             if (!$this->token) {
    //                 log_message('error', 'Token refresh failed (RequestException upload_shipment_image).');
    //                 $resp = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
    //                 $this->log_debug_error('upload_shipment_image_requestexception_token_refresh_failed', isset($url) ? $url : null, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], ['error' => $resp, 'curl' => $curl_debug]);
    //                 return [];
    //             }
    //             return $this->upload_shipment_image($airwaybill, $file_path, false);
    //         }

    //         $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
    //         log_message('error', 'Upload Shipment Image Error: ' . $msg);
    //         $this->log_debug_error('upload_shipment_image_request_exception', isset($url) ? $url : null, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], ['error' => $msg, 'curl' => $curl_debug]);
    //         return [];
    //     } catch (\Exception $e) {
    //         if (!isset($curl_debug)) {
    //             $curl_debug = '[no_curl_debug]';
    //         }
    //         log_message('error', 'General Error (upload_shipment_image): ' . $e->getMessage());
    //         $this->log_debug_error('upload_shipment_image_exception', isset($url) ? $url : null, ['airwaybill' => $airwaybill, 'filename' => basename($file_path)], ['error' => $e->getMessage(), 'curl' => $curl_debug]);
    //         return [];
    //     }
    // }

    public function upload_shipment_image($airwaybill, $file_path, $retry = true)
    {
        try {
            // --- 1️⃣ Validasi file ---
            if (!file_exists($file_path) || !is_readable($file_path)) {
                log_message('error', 'upload_shipment_image: file not found or unreadable: ' . $file_path);
                $this->log_debug_error('upload_shipment_image_file_missing', null, [
                    'airwaybill' => $airwaybill,
                    'filename'   => basename($file_path)
                ], 'file_missing');
                return [];
            }

            // --- 2️⃣ Inisialisasi client & token ---
            $client = new Client(['timeout' => 20, 'allow_redirects' => true]);
            $url    = $this->api_base_url . 'v2/service/shipment/upload_shipment_image';
            $token  = $this->get_token();

            if (!$token) {
                log_message('error', 'No valid token for upload_shipment_image()');
                $this->log_debug_error('upload_shipment_image_no_token', $url, [
                    'airwaybill' => $airwaybill,
                    'filename'   => basename($file_path)
                ], 'no_token');
                return [];
            }

            // --- 3️⃣ Headers (biarkan Guzzle handle multipart boundary) ---
            $headers = [
                'Authorization' => "Bearer $token",
                'Accept'        => 'application/json'
            ];

            if (!empty($_COOKIE['ci_session'])) {
                $headers['Cookie'] = 'ci_session=' . $_COOKIE['ci_session'];
            }

            // --- 4️⃣ Multipart data ---
            $multipart = [
                ['name' => 'airwaybill', 'contents' => $airwaybill],
                ['name' => 'filename', 'contents' => fopen($file_path, 'r'), 'filename' => basename($file_path)]
            ];

            // --- 5️⃣ Buat cURL debug string (untuk log) ---
            $build_curl_debug = function ($url, $headers, $multipart) {
                try {
                    $cmd = "curl --location " . escapeshellarg($url);
                    foreach ($headers as $hk => $hv) {
                        $cmd .= " \\\n  --header " . escapeshellarg($hk . ': ' . $hv);
                    }
                    foreach ($multipart as $part) {
                        if (isset($part['filename'])) {
                            $cmd .= " \\\n  --form " . escapeshellarg($part['name'] . '=@' . $part['filename']);
                        } else {
                            $val = is_string($part['contents']) ? $part['contents'] : json_encode($part['contents']);
                            $cmd .= " \\\n  --form " . escapeshellarg($part['name'] . '="' . $val . '"');
                        }
                    }
                    return $cmd;
                } catch (\Exception $e) {
                    return '[failed_to_build_curl_debug]';
                }
            };

            $curl_debug = $build_curl_debug($url, $headers, $multipart);
            log_message('debug', '[CEX CURL] ' . $curl_debug);

            // --- 6️⃣ Kirim request ---
            $response = $client->request('POST', $url, [
                'headers'   => $headers,
                'multipart' => $multipart
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // --- 7️⃣ Jika 401 atau signature verification failed → refresh token & retry sekali ---
            $should_refresh = $retry && isset($result['status']) && (
                (int)$result['status'] === 401 ||
                ((int)$result['status'] === 0 && isset($result['msg']) && stripos($result['msg'], 'signature') !== false)
            );

            if ($should_refresh) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (upload_shipment_image)');
                    $this->log_debug_error('upload_shipment_image_token_refresh_failed', $url, [
                        'airwaybill' => $airwaybill,
                        'filename'   => basename($file_path)
                    ], ['response' => $result, 'curl' => $curl_debug]);
                    return [];
                }
                return $this->upload_shipment_image($airwaybill, $file_path, false);
            }

            // --- 8️⃣ Validasi hasil response ---
            if (isset($result['status']) && (int)$result['status'] === 200) {
                return $result;
            }

            $this->log_debug_error('upload_shipment_image_non_200', $url, [
                'airwaybill' => $airwaybill,
                'filename'   => basename($file_path)
            ], ['response' => $result, 'curl' => $curl_debug]);
            return [];

        } catch (RequestException $e) {
            if (!isset($curl_debug)) $curl_debug = '[no_curl_debug]';

            // --- Retry jika 401 atau body mengandung signature verification failed ---
            if ($retry) {
                $respBody = null;
                if ($e->hasResponse()) {
                    try {
                        $respBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                    } catch (\Exception $ex) {
                        $respBody = $e->getResponse()->getBody()->getContents();
                    }
                }
                $is401 = $e->hasResponse() && $e->getResponse()->getStatusCode() == 401;
                $isSignatureErr = false;
                if (is_array($respBody) && isset($respBody['status']) && (int)$respBody['status'] === 0 && isset($respBody['msg'])) {
                    $isSignatureErr = stripos($respBody['msg'], 'signature') !== false;
                } elseif (is_string($respBody)) {
                    $isSignatureErr = stripos($respBody, 'signature') !== false;
                }

                if ($is401 || $isSignatureErr) {
                    $this->get_token(true);
                    if (!$this->token) {
                        $resp = $e->hasResponse() ? ($e->getResponse()->getBody()->getContents()) : $e->getMessage();
                        log_message('error', 'Token refresh failed (RequestException upload_shipment_image)');
                        $this->log_debug_error('upload_shipment_image_requestexception_token_refresh_failed', $url ?? null, [
                            'airwaybill' => $airwaybill,
                            'filename'   => basename($file_path)
                        ], ['error' => $resp, 'curl' => $curl_debug]);
                        return [];
                    }
                    return $this->upload_shipment_image($airwaybill, $file_path, false);
                }
            }

            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'Upload Shipment Image Error: ' . $msg);
            $this->log_debug_error('upload_shipment_image_request_exception', $url ?? null, [
                'airwaybill' => $airwaybill,
                'filename'   => basename($file_path)
            ], ['error' => $msg, 'curl' => $curl_debug]);
            return [];

        } catch (\Exception $e) {
            if (!isset($curl_debug)) $curl_debug = '[no_curl_debug]';
            log_message('error', 'General Error (upload_shipment_image): ' . $e->getMessage());
            $this->log_debug_error('upload_shipment_image_exception', $url ?? null, [
                'airwaybill' => $airwaybill,
                'filename'   => basename($file_path)
            ], ['error' => $e->getMessage(), 'curl' => $curl_debug]);
            return [];
        }
    }

    /**
     * Placeholder: ambil token dari session/config/cache
     */
    // private function get_token($refresh = false)
    // {
    //     // TODO: implement ambil token dari cache/db
    //     // contoh hardcoded:
    //     if ($refresh || !$this->token) {
    //         $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhY2NvdW50IjoiQ0EyNjI3OSIsImdlbmVyYXRlRGF0ZSI6IjIwMjUtMTAtMTkgMTM6MjI6MDYiLCJleHBpcmVkRGF0ZSI6IjIwMjUtMTAtMjYgMTM6MjI6MDYifQ.W69YYz5GaB2ZsyrZGaQT7YVmAEw-XYddZG9xzP_of1g';
    //     }
    //     return $this->token;
    // }

    /**
     * Logging helper (supaya gak error walau belum ada implementasi asli)
     */
    // private function log_debug_error($context, $url = null, $meta = [], $extra = [])
    // {
    //     $msg = strtoupper($context) . ': ' . json_encode([
    //         'url'   => $url,
    //         'meta'  => $meta,
    //         'extra' => $extra
    //     ]);
    //     log_message('error', $msg);
    // }


    // ============================================================
    // INTERNAL POST HELPER
    // ============================================================
    private function post_request($path, $data, $retry = true)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = $this->api_base_url . $path;
            $token = $this->get_token();

            // prepare headers for both request and debug curl
            $headers = [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];

            // helper to build a safe/masked curl command for debugging
            $build_curl_debug = function ($method, $url, $headers, $body) {
                // mask sensitive header/body values using existing mask helper expectations
                try {
                    // Headers masking: mask_sensitive expects associative arrays
                    $masked_headers = $this->mask_sensitive($headers);
                    $masked_body = $this->mask_sensitive($body);

                    $cmd = 'curl -X ' . strtoupper($method) . ' ' . escapeshellarg($url);
                    foreach ($masked_headers as $hk => $hv) {
                        $cmd .= ' -H ' . escapeshellarg($hk . ': ' . $hv);
                    }
                    if (!empty($masked_body)) {
                        $jsonBody = json_encode($masked_body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $cmd .= ' --data-raw ' . escapeshellarg($jsonBody);
                    }
                    $cmd .= ' --compressed';
                    return $cmd;
                } catch (\Exception $e) {
                    return '[failed_to_build_curl_debug]';
                }
            };

            if (!$token) {
                log_message('error', 'No valid token for POST ' . $path);
                $curl_debug = $build_curl_debug('POST', $url, $headers, $data);
                $this->log_debug_error('post_request_no_token', $url, $data, ['error' => 'no_token', 'curl' => $curl_debug]);
                log_message('debug', '[CEX CURL] ' . $curl_debug);
                return [];
            }

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'json' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // If response indicates invalid signature or auth (status 401) refresh token and retry
            $should_refresh = $retry && isset($result['status']) && (
                (int)$result['status'] === 401 ||
                ((int)$result['status'] === 0 && isset($result['msg']) && stripos($result['msg'], 'signature') !== false)
            );

            if ($should_refresh) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (post_request ' . $path . ').');
                    $curl_debug = $build_curl_debug('POST', $url, $headers, $data);
                    $this->log_debug_error('post_request_token_refresh_failed', $url, $data, ['response' => $result, 'curl' => $curl_debug]);
                    log_message('debug', '[CEX CURL] ' . $curl_debug);
                    return [];
                }
                return $this->post_request($path, $data, false);
            }

            if (isset($result['status']) && $result['status'] == 200) {
                return $result;
            } else {
                // log debug of unsuccessful response and include curl
                $curl_debug = $build_curl_debug('POST', $url, $headers, $data);
                $this->log_debug_error('post_request_non_200', $url, $data, ['response' => $result, 'curl' => $curl_debug]);
                log_message('debug', '[CEX CURL] ' . $curl_debug);
                return [];
            }

        } catch (RequestException $e) {
            // Build curl debug for logging
            $headers_for_debug = isset($headers) ? $headers : [
                'Authorization' => isset($token) ? 'Bearer ' . $token : 'Bearer ***MASKED***',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
            $curl_debug = isset($build_curl_debug) ? $build_curl_debug('POST', $url ?? ($this->api_base_url . $path), $headers_for_debug, $data) : '[no_curl_debug]';

            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException post_request ' . $path . ').');
                    $resp = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
                    $this->log_debug_error('post_request_requestexception_token_refresh_failed', isset($url) ? $url : null, $data, ['error' => $resp, 'curl' => $curl_debug]);
                    log_message('debug', '[CEX CURL] ' . $curl_debug);
                    return [];
                }
                return $this->post_request($path, $data, false);
            }

            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'POST Request Error (' . $path . '): ' . $msg);
            $this->log_debug_error('post_request_request_exception', isset($url) ? $url : null, $data, ['error' => $msg, 'curl' => $curl_debug]);
            log_message('debug', '[CEX CURL] ' . $curl_debug);
            return [];
        } catch (\Exception $e) {
            $headers_for_debug = isset($headers) ? $headers : [
                'Authorization' => isset($token) ? 'Bearer ' . $token : 'Bearer ***MASKED***',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
            $curl_debug = isset($build_curl_debug) ? $build_curl_debug('POST', $url ?? ($this->api_base_url . $path), $headers_for_debug, $data) : '[no_curl_debug]';

            log_message('error', 'General Error (post_request ' . $path . '): ' . $e->getMessage());
            $this->log_debug_error('post_request_exception', isset($url) ? $url : null, $data, ['error' => $e->getMessage(), 'curl' => $curl_debug]);
            log_message('debug', '[CEX CURL] ' . $curl_debug);
            return [];
        }
    }
}
