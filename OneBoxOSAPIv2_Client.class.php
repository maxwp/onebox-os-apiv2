<?php
class OneBoxOSAPIv2_Client {

    public function request($method, $urlEndpoint, $paramArray, $printLog = 'auto') {
        return $this->_request($method, $urlEndpoint, $paramArray, $printLog, $this->getToken());
    }

    private function _request($method, $urlEndpoint, $paramArray, $printLog = 'auto', $token = false) {
        $method == strtoupper($method);
        if (!$method) {
            $method = 'GET';
        }

        $url = $this->_boxURL.'/'.$urlEndpoint;
        if ($method == 'GET') {
            $url .= '?'.http_build_query($paramArray);
            $paramArray = false;
        }

        if ($printLog == 'auto') {
            $printLog = $this->_printLog;
        }

        if ($printLog) {
            print "Request ".$url."\n";
            if ($paramArray) {
                print_r($paramArray);
            }
        }

        $headerArray = [];
        if ($token) {
            $headerArray[] = 'token: '.$token;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($paramArray) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramArray));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TCP_FASTOPEN, 1);
        //curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // verbose log
        /*curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);*/

        // exec
        $result = curl_exec($ch);

        $result = trim($result);
        if (!$result) {
            throw new OneBoxOSAPIv2_Exception('Empty responce');
        }

        $json = json_decode($result, true);
        if (!$json) {
            throw new OneBoxOSAPIv2_Exception('Invalid json responce');
        }

        if ($printLog) {
            print "Responce:\n";
            print_r($json);
        }

        $status = @$json['status'];
        if (!isset($json['status'])) {
            throw new OneBoxOSAPIv2_Exception('Invalid API responce, please go to support');
        }

        if ($status == 1) {
            return $json['dataArray'];
        }

        throw new OneBoxOSAPIv2_Exception($json['errorArray']);
    }

    public static function Init($boxURL, $login, $restAPIPassword, $printLog = false) {
        if (!$boxURL) {
            throw new OneBoxOSAPIv2_Exception('Empty OneBox URL');
        }
        if (!$login) {
            throw new OneBoxOSAPIv2_Exception('Empty login');
        }
        if (!$restAPIPassword) {
            throw new OneBoxOSAPIv2_Exception('Empty restapipassword');
        }

        self::$_Instance = new self($boxURL, $login, $restAPIPassword, $printLog);
        return self::$_Instance;
    }

    private function __construct($boxURL, $login, $restAPIPassword, $printLog = false) {
        $this->_boxURL = $boxURL;
        $this->_login = $login;
        $this->_restAPIPassword = $restAPIPassword;
        $this->_printLog = $printLog;
    }

    public function getToken() {
        if ($this->_token) {
            return $this->_token;
        }

        // авторизируемся и получаем токен
        $paramArray = [];
        $paramArray['login'] = $this->_login;
        $paramArray['restapipassword'] = $this->_restAPIPassword;
        $a = $this->_request('POST', 'api/v2/get/token/', $paramArray);
        $this->_token = $a['token'];
        return $this->_token;
    }

    /**
     * @return mixed
     * @throws OneBoxOSAPIv2_Exception
     */
    public static function Get() {
        if (!self::$_Instance) {
            throw new OneBoxOSAPIv2_Exception('APIv2 not initialized, call Init() fist');
        }

        return self::$_Instance;
    }

    private static $_Instance;

    private $_boxURL;

    private $_login;

    private $_restAPIPassword;

    private $_printLog = false;

    private $_token;

}