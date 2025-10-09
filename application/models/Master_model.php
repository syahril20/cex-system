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

    // ============================================================
    // TOKEN MANAGEMENT
    // ============================================================
    private function get_token($force_refresh = false)
    {
        if (!$force_refresh && $this->token) {
            return $this->token;
        }

        echo "<script>console.log('TOKEN: ', " . json_encode($this->token) . ');</script>';

        $token = $this->generate_token($this->username, $this->secret_key);
        if ($token) {
            $this->token = $token;
            return $this->token;
        }

        log_message('error', 'Failed to generate new API token!');
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
            return null;

        } catch (RequestException $e) {
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', '[CEX] Token Request Error: ' . $msg);
            return null;
        } catch (\Exception $e) {
            log_message('error', '[CEX] General Error (generate_token): ' . $e->getMessage());
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
                    return [];
                }
                return $this->send_request($path, false);
            }

            if (isset($result['status']) && $result['status'] == 200) {
                return $result['data'];
            } else {
                return [];
            }

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException send_request).');
                    return [];
                }
                return $this->send_request($path, false);
            }
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'GET Request Error: ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (send_request): ' . $e->getMessage());
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
                    return [];
                }
                return $this->get_tracking($airwaybill, false);
            }

            return (isset($result['status']) && $result['status'] == 200)
                ? $result
                : [];

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException get_tracking).');
                    return [];
                }
                return $this->get_tracking($airwaybill, false);
            }
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'Tracking Request Error: ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (get_tracking): ' . $e->getMessage());
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

    public function upload_shipment_image($airwaybill, $file_path, $retry = true)
    {
        try {
            $client = new Client(['timeout' => 20]);
            $url = $this->api_base_url . 'v2/service/shipment/upload_shipment_image';
            $token = $this->get_token();

            if (!$token) {
                log_message('error', 'No valid token for upload_shipment_image()');
                return [];
            }

            $multipart = [
                ['name' => 'airwaybill', 'contents' => $airwaybill],
                ['name' => 'filename', 'contents' => fopen($file_path, 'r'), 'filename' => basename($file_path)]
            ];

            $response = $client->request('POST', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'],
                'multipart' => $multipart
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($retry && isset($result['status']) && (int) $result['status'] === 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (upload_shipment_image).');
                    return [];
                }
                return $this->upload_shipment_image($airwaybill, $file_path, false);
            }

            return (isset($result['status']) && $result['status'] == 200) ? $result : [];

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException upload_shipment_image).');
                    return [];
                }
                return $this->upload_shipment_image($airwaybill, $file_path, false);
            }

            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'Upload Shipment Image Error: ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (upload_shipment_image): ' . $e->getMessage());
            return [];
        }
    }

    // ============================================================
    // INTERNAL POST HELPER
    // ============================================================
    private function post_request($path, $data, $retry = true)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = $this->api_base_url . $path;
            $token = $this->get_token();

            if (!$token) {
                log_message('error', 'No valid token for POST ' . $path);
                return [];
            }

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($retry && isset($result['status']) && (int) $result['status'] === 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (post_request ' . $path . ').');
                    return [];
                }
                return $this->post_request($path, $data, false);
            }

            return (isset($result['status']) && $result['status'] == 200) ? $result : [];

        } catch (RequestException $e) {
            if ($retry && $e->hasResponse() && $e->getResponse()->getStatusCode() == 401) {
                $this->get_token(true);
                if (!$this->token) {
                    log_message('error', 'Token refresh failed (RequestException post_request ' . $path . ').');
                    return [];
                }
                return $this->post_request($path, $data, false);
            }

            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            log_message('error', 'POST Request Error (' . $path . '): ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error (post_request ' . $path . '): ' . $e->getMessage());
            return [];
        }
    }
}
