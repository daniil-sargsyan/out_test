<?php

class Request
{
    public function curlRequest($url, $method, $params, $totals = null, $toggle = 'json')
    {
        $response = self::request($url, $method, $params, $toggle);
        if ($toggle === 'xml') return $response;
        if (!isset($totals)) {
            $fullResponse = $response['result'];
        } else {
            $fullResponse = $response['total'];
        }
        while (isset($response['next'])) {
            sleep(0.1);
            $params["start"] = $response['next'];
            $response = self::request($url, $method, $params, $toggle);
            if (!isset($totals)) {
                $fullResponse = array_merge($response['result'], $fullResponse);
            } else {
                $fullResponse = array_merge($response['total'], $fullResponse);
            }
        }

        return $fullResponse;
    }

    /**
     * @param $url
     * @param $method
     * @param $params
     * @param $toggle
     * @return bool|mixed|string
     */
    public static function request($url, $method, $params, $toggle = '')
    {
        $curl = curl_init();
        $url = $url . $method . '' . $toggle;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: app/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        return $response;
    }

    function callMethod($url, $method, $params, $totals = null, $toggle = 'json')
    {
        global $not_to_die;
        $response = $this->curlRequest($url, $method, $params, $toggle);

        if ($toggle === 'xml') return $response;
        if (!isset($totals)) {
            $fullResponse = $response['result'];
        } else {
            $fullResponse = $response['total'];
        }

        while (isset($response['next'])) {
            sleep(1);
            $params["start"] = $response['next'];
            $response = $this->curlRequest($url, $method, $params);
            if (!isset($totals)) {
                $fullResponse = array_merge($response['result'], $fullResponse);
            } else {
                $fullResponse = array_merge($response['total'], $fullResponse);
            }
        }

        return $fullResponse;
    }

    public static function executeHTTPRequest($queryUrl, array $params = array()) {
        $result = array();
        $queryData = http_build_query($params);

        $curl = curl_init();
        $curlopt = array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        );
        if (count($queryData) == 0){
            unset($curlopt[CURLOPT_POST]);
            unset($curlopt[CURLOPT_POSTFIELDS]);
        }
        curl_setopt_array($curl,$curlopt);
        $curlResult = curl_exec($curl);
        curl_close($curl);

        if ($curlResult != '') $result = json_decode($curlResult, true);

        return $result;
    }
}