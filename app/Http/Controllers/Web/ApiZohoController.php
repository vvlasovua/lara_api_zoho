<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiZohoController extends Controller
{
    protected $client_id ='1000.8D601O6GW9W9WY9G91GCW32R2Z2G4W';//Enter your Client ID here
    protected $client_secret = '44db94b40fb91d18745ba2ee1dfcc8383771d4dd08';//Enter your Client Secret here
    //protected $code = '1000.d49ff8ba7a73a61938947bbbcadf4906.9e24c08fd9aa767132080c4777d8f8c4';//Enter your Code here
    //protected $code = '1000.af4802e8fc3d9a0301997ff17c05664a.e20aa1eace294180d784c0748ea7aa68';//Enter your Code here
    protected $code = '1000.83cb5e228fd319915d2cf697f777a1e1.24b41ab06d617fb1970663676d4b1dcb';//Enter your Code here
    protected $base_acc_url = 'https://accounts.zoho.com';
    protected $service_url = 'https://creator.zoho.com';
    //protected $refresh_token = '1000.4f4b206a80c5e3e48c1cf3a55994a19c.c7c25dc7144da53c497b2d2ca834d1d0';
    //protected $refresh_token = '1000.407c44c4b5833158fd1101a677e3bf18.d5065223c912c85d864d396123303027';
    protected $refresh_token = '1000.482db21d9090135751b5231a6515bf42.ab3fb36de368a27bf9782cf14308d1f8';
    protected $access_token = '';

    public function __construct()
    {
        $this->access_token = $this->generate_access_token($this->get_access_token_url());
    }


    /*public function get_token_url(){
        //$token_url = $base_acc_url . '/oauth/v2/token?grant_type=authorization_code&client_id='. $client_id . '&client_secret='. $client_secret . '&redirect_uri=http://localhost&code=' . $code;
        return $this->base_acc_url . '/oauth/v2/token?grant_type=authorization_code&client_id='. $this->client_id . '&client_secret='. $this->client_secret . '&redirect_uri=http://localhost&code=' . $this->code;
    }*/

    public function get_access_token_url(){
        return $this->base_acc_url .  '/oauth/v2/token?refresh_token='.$this->refresh_token.'&client_id='.$this->client_id.'&client_secret='.$this->client_secret .'&grant_type=refresh_token';
    }

    /*public function generate_refresh_token($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }*/


    public function generate_access_token($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        //dd($result);
        return json_decode($result)->access_token;
    }

    public function get_user_by_id($user_id){
        $service_url = 'https://www.zohoapis.com/crm/v2/users/'.$user_id;
        $header = array(
            'Authorization: Zoho-oauthtoken ' . $this->access_token,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $service_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function get_all_users(){

        $curl_pointer = curl_init();

        $curl_options = array();
        $url = "https://www.zohoapis.com/crm/v2/users?";
        $parameters = array();
        $parameters["type"]="AllUsers";
        $parameters["page"]="1";
        $parameters["per_page"]="2";
        foreach ($parameters as $key=>$value){
            $url =$url.$key."=".$value."&";
        }
        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "GET";
        $headersArray = array();

        $headersArray[] = "Authorization". ":" . "Zoho-oauthtoken " . $this->access_token;
        //$headersArray[] = "If-Modified-Since".":"."2020-05-15T12:00:00+05:30";
        $curl_options[CURLOPT_HTTPHEADER]=$headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        curl_close($curl_pointer);
        list ($headers, $content) = explode("\r\n\r\n", $result, 2);
        if(strpos($headers," 100 Continue")!==false){
            list( $headers, $content) = explode( "\r\n\r\n", $content , 2);
        }
        $headerArray = (explode("\r\n", $headers, 50));
        $headerMap = array();
        foreach ($headerArray as $key) {
            if (strpos($key, ":") != false) {
                $firstHalf = substr($key, 0, strpos($key, ":"));
                $secondHalf = substr($key, strpos($key, ":") + 1);
                $headerMap[$firstHalf] = trim($secondHalf);
            }
        }
        $jsonResponse = json_decode($content, true);
        if ($jsonResponse == null && $responseInfo['http_code'] != 204) {
            list ($headers, $content) = explode("\r\n\r\n", $content, 2);
            $jsonResponse = json_decode($content, true);
        }
        //var_dump($headerMap);
        //var_dump($jsonResponse);
        //var_dump($responseInfo['http_code']);

        return $jsonResponse;

    }

    public function get_list_records($module){
        $curl_pointer = curl_init();

        $curl_options = array();
        $url = "https://www.zohoapis.com/crm/v2/$module?";
        $parameters = array();
        $parameters["page"]="1";
        $parameters["per_page"]="20";
        $parameters["sort_by"]="Email";
        $parameters["sort_order"]="desc";
        $parameters["include_child"]="false";


        foreach ($parameters as $key=>$value){
            $url =$url.$key."=".$value."&";
        }
        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "GET";
        $headersArray = array();
        $headersArray[] = "Authorization". ":" . "Zoho-oauthtoken " . $this->access_token;
        //$headersArray[] = "If-Modified-Since".":"."2021-10-12T17:59:50+05:30";
        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        curl_close($curl_pointer);
        list ($headers, $content) = explode("\r\n\r\n", $result, 2);
        if(strpos($headers," 100 Continue")!==false){
            list( $headers, $content) = explode( "\r\n\r\n", $content , 2);
        }
        $headerArray = (explode("\r\n", $headers, 50));
        $headerMap = array();
        foreach ($headerArray as $key) {
            if (strpos($key, ":") != false) {
                $firstHalf = substr($key, 0, strpos($key, ":"));
                $secondHalf = substr($key, strpos($key, ":") + 1);
                $headerMap[$firstHalf] = trim($secondHalf);
            }
        }
        $jsonResponse = json_decode($content, true);
        if ($jsonResponse == null && $responseInfo['http_code'] != 204) {
            list ($headers, $content) = explode("\r\n\r\n", $content, 2);
            $jsonResponse = json_decode($content, true);
        }
        //var_dump($headerMap);
        //var_dump($jsonResponse);
        //var_dump($responseInfo['http_code']);

        return $jsonResponse;

    }

    public function get_record_by_id($module, $record_id){
        $service_url = 'https://www.zohoapis.com/crm/v2/'.$module.'/'.$record_id;
        $header = array(
            'Authorization: Zoho-oauthtoken ' . $this->access_token,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $service_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function create_record($module, array $data){
        $curl = curl_init();

        $header = array(
            'Authorization: Zoho-oauthtoken ' . $this->access_token,
            'Content-Type: application/json'
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.zohoapis.com/crm/v2/'.$module,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        return json_decode($result, true);
    }



}
