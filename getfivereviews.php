<?php

class GetFiveStarsSimpleApi {

    const URL = 'https://getfivestars.com/api';
    const TIMEOUT = 30; // 30 seconds timeout

    /**
     * Init
     * 
     * @param string $clientId
     * @param string $privateKey
     */

    function __construct($clientId, $privateKey) {
        $this->clientId = $clientId;
        $this->privateKey = $privateKey;
    }

    /**
     * Do HTTP API request
     * 
     * @param string $resource
     * @param array[string] $request
     * 
     * @return string JSON response
     */
    function doRequest($resource, $request) {
        $request['clientId'] = $this->clientId;
        $request['hash'] = $this->signRequest($request, $this->privateKey);
        return $this->doJsonPost($resource, $request);
    }

    /**
     * JSON POST HTTP request
     * 
     * @param string $resource
     * @param array[string] $request
     * 
     * @return string JSON response
     */
    protected function doJsonPost($resource, $request) {
        $ch = curl_init(self::URL . $resource);

        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($request)
        ));

        return curl_exec($ch);
    }

    /**
     * Sign request with private key
     * 
     * @param array[string] $request
     * @param string $privateKey
     * 
     * @return string Hash sign
     */
    protected function signRequest($request, $privateKey) {
        ksort($request);

        $control = $privateKey;
        foreach ($request as $key => $value) {
            $control .= $key . $value;
        }

        return hash('sha256', $control);
    }

}


    $privateKey = 'x';
    $clientId = 'y';

    $api = new GetFiveStarsSimpleApi($clientId, $privateKey);
    if(isset($_GET['from'])){
        $from=$_GET['from'];
        if(isset($_GET['to'])){
            $to=$_GET['to'];
        }
        else{
            $to=date("Y-m-d");
        }
        $response = json_decode($api->doRequest('/feedbacks/get', array("businessId"=>"x","from"=>$from,"to"=>$to)));
        print_r('from: <strong>'.$from."</strong> to: <strong> ".$to."</strong><br><br>");
    }
    else{
        $from=date("Y-m-d");
        $response = json_decode($api->doRequest('/feedbacks/get', array("businessId"=>"x","from"=>$from)));
        print_r('from: <strong>'.$from."</strong><br><br>");
    }

    for($i=1;$i<=$response->count;$i++){
        $recommend='recommend'.$i;
        $authorEmail='authorEmail'.$i;
        $authorName='authorName'.$i;
        if($response->$recommend>=9){
        print_r($response->$authorEmail.", ");
        }
    }
    
