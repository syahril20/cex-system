<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Master_model extends CI_Model
{
    private $api_base_url;
    private $token;

    public function __construct()
    {
        parent::__construct();
        $this->api_base_url = getenv('API_CEX_BASE_URL');
        $this->token = getenv('API_CEX_AUTH');
    }

    // Ambil rates
    public function get_rates()
    {
        return $this->send_request(getenv('API_CEX_RATES_ENDPOINT'));
    }

    // Ambil commodity
    public function get_commodity()
    {
        return $this->send_request(getenv('API_CEX_COMMODITY_ENDPOINT'));
    }

    // Fungsi umum untuk request GET
    private function send_request($path)
    {
        try {
            $client = new Client();
            $url = $this->api_base_url . $path;

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept'        => 'application/json'
                ]
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            return (isset($result['status']) && $result['status'] == 200)
                ? $result['data']
                : [];

        } catch (RequestException $e) {
            $msg = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();
            log_message('error', 'Guzzle Request Error: ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error: ' . $e->getMessage());
            return [];
        }
    }
}
