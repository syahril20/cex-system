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
                    'Accept' => 'application/json'
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

    public function get_latest_tracking_status($airwaybill)
    {
        $result = $this->get_tracking($airwaybill);

        if (empty($result) || empty($result['trackings'])) {
            return null;
        }

        $trackings = $result['trackings'];


        // Urutkan berdasarkan date + time terbaru
        usort($trackings, function ($a, $b) {
            $datetimeA = strtotime($a['date'] . ' ' . $a['time']);
            $datetimeB = strtotime($b['date'] . ' ' . $b['time']);
            return $datetimeB <=> $datetimeA; // descending
        });


        // Ambil status terbaru
        return $trackings[0]['status'] ?? null;
    }


    public function get_tracking($airwaybill)
    {
        try {
            $client = new Client();
            $url = $this->api_base_url . getenv('API_CEX_TRACKING_ENDPOINT');
            // Pastikan API_CEX_TRACKING_ENDPOINT = '/v2/service/trackings' di .env

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'airwaybill' => $airwaybill
                ]
            ]);


            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            return (isset($result['status']) && $result['status'] == 200)
                ? $result
                : [];

        } catch (RequestException $e) {
            $msg = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();
            log_message('error', 'Tracking Request Error: ' . $msg);
            return [];
        } catch (\Exception $e) {
            log_message('error', 'General Error: ' . $e->getMessage());
            return [];
        }
    }



}
