<?php


namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Lcobucci\JWT\Parser as JwtParser;

class Helper
{
    public static function assets($path, $secure = null) {
        return app('url')->asset($path.'/asset', $secure);
    }

    public static function clean($string) {
        if(empty($string))
            return $string;

        $string = trim($string); // Replaces all spaces with no s.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    static function formatPhoneNumber($phone)
    {
        $phone = str_replace('+', '', $phone);
        if (strlen($phone) > 2)
        {
            $phone = substr($phone, 2);
        }
        return $phone;
    }

    static function dated_by($name, $date)
    {
        $text = '';
        if(!empty($name))
        {
            $text .= '<b>' . $name . '</b><br/>';
        }
        if (!empty($date)) {
            $text .= self::dateTime($date);
        }
        return $text;
    }

    static function dateTime($timestamp)
    {
        if (empty($timestamp)) return '';
        $localTimeZone = self::getUserLocalTime();
        $timestampFormat = 'Y-m-d H:i:s';
        return Carbon::createFromFormat($timestampFormat, $timestamp, 'UTC')
            ->setTimezone($localTimeZone)
            ->format('d M Y h:i  A');
    }

    public static function generateSlugReference($name) {
        $randString = substr( time().mt_rand(111, 999) , 8);
        $slug = self::clean(str_replace(' ', '-', $name));
        return (string) $randString."_".strtolower($slug);
    }

    public static function isImageValid($url,$image_placeholder){
        $image = $image_placeholder;
        // Remove all illegal characters from a url
        $image_url = filter_var($url, FILTER_SANITIZE_URL);

        // Validate url
        if (filter_var($image_url, FILTER_VALIDATE_URL)) {
            $url = trim( $image_url );
            $image = ( strpos( $url, 'http://' ) === 0 || strpos( $url, 'https://' ) === 0 ) &&
                filter_var(
                    $url,
                    FILTER_VALIDATE_URL
//                    FILTER_FLAG_SCHEME_REQUIRED  || FILTER_FLAG_HOST_REQUIRED
                ) !== false && Helper::isUrlValid($url) ? $url : $image_placeholder;
        }
        return $image;
    }

    public static function isUrlValid( $url)
    {
        $result = false;
        try{
            list($status) = get_headers($url);
            $headers = @get_headers($url);
            if (strpos($status, '404') !== FALSE) {
                $result = false;
            }
            else
            {
                $result= true;
            }
        }catch( \Exception $e)
        {
            $result = false;
        } finally {
            return $result;
        }
    }

    public static function convertSecondsIntoMilliseconds($seconds)
    {
        return $seconds * 1000;
    }

    public static function getLocalizedColumn($column)
    {
//        if( config('app.locale') == Constant::CHECKOUT_LANGUAGES['English'] ) {
            return $column;
//        } else {
//            return $column.'_'.config('app.locale');
//        }
    }

    public static function convertMinuteIntoMilliseconds($minutes)
    {
        $value = "{$minutes}:00";

        list($minutes, $seconds) = explode(':', $value);

        return $seconds * 1000 + $minutes * 60000;
    }

    public static function getAssetURL($path)
    {
        return env("AWS_URL") . "/" . $path;
    }

    public static function apiRequest($method, $url, $queryParams = [], $body = [], $headers = [], $contentType = 'json', $returnWithStatusCode = false, $extras = [])
    {
        $response = [];

        try
        {
            if (is_array($queryParams) && count($queryParams) > 0)
            {
                $url .= '?' . http_build_query($queryParams);
            }

            //$headers["VGS-Log-Request"] = "all";

            $payload = [
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => $extras['timeout'] ?? config('app.guzzle_timeout'),
                'connect_timeout' => $extras['connect_timeout'] ?? config('app.guzzle_connect_timeout'),
            ];

            if($method != 'GET'){
                $payload[$contentType] = $body;
            }

            if( isset( $extras['verify'] ) )
            {
                $payload['verify'] = $extras['verify'];
            }

            if( isset( $extras['proxy'] ) && env('PROXY') )
            {
                $proxyUserName = $extras['proxy']['USER_NAME'];
                $proxyPwd = $extras['proxy']['PASSWORD'];

                //$certification_path = openssl_get_privatekey( $extras['proxy']['CERT'] );
                $payload['curl'] = [
                    CURLOPT_PROXY        => $extras['proxy']['URL'],
                    CURLOPT_PROXYPORT    => $extras['proxy']['PORT'],
                    CURLOPT_PROXYUSERPWD => "$proxyUserName:$proxyPwd",
                ];

                $payload['verify'] = false; //$certification_path;
            }

            $client = new Client();
            $curlResponse = $client->request($method, $url, $payload );
            if( $returnWithStatusCode )
            {
                $response['code'] =  $curlResponse->getStatusCode();
                $response['content'] =  json_decode($curlResponse->getBody()->getContents(), true);
            }
            else
            {
                $response = json_decode($curlResponse->getBody()->getContents(), true);
            }
        }
        catch (RequestException $e)
        {
            AppException::log($e);
        }
        catch (\Exception $e)
        {
            AppException::log($e);
        }
        finally
        {
            return $response;
        }
    }

    public static function getUserIP($request)
    {
        $ip = null;
        if ($_SERVER && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $request->getClientIp();
        }

        return explode(',', $ip)[0];
    }


    public static function obfuscateString($str)
    {
        return substr($str, 0, min(5, strlen($str) - 5)) .
          str_repeat('*', max(6, strlen($str) - 6)) .
          substr($str, strlen($str) - 2, min(2, strlen($str) - 2));
    }

    static function uploadJSON($fileContent, $destinationPath, $fileName)
    {
        Storage::disk('s3')->put(
          $destinationPath . '/' . $fileName,
          json_encode($fileContent, JSON_UNESCAPED_UNICODE),
          'public'
        );
    }

    public static function randomDigits()
    {
        return mt_rand(100000, 999999);
    }


    public static function getUserLocalTime()
    {
        return Constant::timezone;
    }

    public static function utcToUserLocalTimeZone($timestamp)
    {
        if (empty($timestamp)) return '';
        $localTimeZone = Helper::getUserLocalTime();
        $timestampFormat = 'Y-m-d H:i:s';
        return Carbon::createFromFormat($timestampFormat, $timestamp, 'UTC')
          ->setTimezone($localTimeZone)
          ->format('F d, Y');
//        ->format('d M Y h:i  A');
    }


    public static function getAssetPath( $fileName )
    {
        return env('ASSETS_BASE_PATH','https://bsecure-dev.s3-eu-west-1.amazonaws.com/dev/assets')."/".$fileName;
    }


    static function getProductImagePlaceholder(){
        return  env('AWS_URL')."/". env('PRODUCT_IMAGE_PLACEHOLDER', 'react_app/assets/product_image_placeholder.jpg');
    }

    static function getAssetFullPath($fileName)
    {
        return env('AWS_URL') . "/" . env('ENV_FOLDER') . $fileName;
    }

    static function callApi($endPoint, $requestData, $merchantAppCreds, $headers=[], $logException = true)
    {
        $http = new Client();

        $parameters["headers"] = $headers;

//        $merchantAccessToken = Helper::getMerchantAccessToken($merchantAppCreds);
//        if(isset($merchantAccessToken['access_token']))
//        {
            if(array_key_exists('client_id', $merchantAppCreds)) {
                $clientIdHeader = $merchantAppCreds['client_id'];
                if(array_key_exists('store_slug', $merchantAppCreds)){
                    $clientIdHeader = $merchantAppCreds['client_id'].':'.$merchantAppCreds['store_slug'];
                }
//            $parameters["headers"]["Authorization"] = 'Bearer  ' . $merchantAccessToken['access_token'];
                $parameters["headers"]["x-client-id"] = base64_encode($clientIdHeader);
                $parameters["headers"]["x-client-token"] = base64_encode($merchantAppCreds['client_secret']);
            }

            $parameters["form_params"] = $requestData;

            try
            {
                $response = $http->post($endPoint, $parameters);
                return json_decode((string)$response->getBody(), true);
            }
            catch (RequestException $e)
            {
                if( $logException )
                {
                    AppException::log($e);
                }

                if ($e->hasResponse())
                {
                    $response = json_decode($e->getResponse()->getBody(), true);
                    $message = isset($response['message']) ? $response['message'] : '';
                    $status = $e->getResponse()->getStatusCode();
                    return ['error' => true, 'message' => $message, 'status' => $status];
                }
            }
//        }
//        else
//        {
//            return ['error' => true, 'message' => $merchantAccessToken['message']];
//        }
    }

    static function getMerchantAccessToken($data)
    {
        $http = new Client();
        $authUrl = env('APP_URL') . Constant::API_ENDPOINTS['oauth'];

        $response = $http->post($authUrl, [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $data['client_id'],
                'client_secret' => $data['client_secret'],
                'scope'         => "",
            ],
            'http_errors' => false
        ]);

        $result = json_decode((string)$response->getBody("access_token"), true);
        if (isset($result['status']) && $result['status'] == Http::$Codes[Http::SUCCESS])
        {
            $access_token = isset($result['body']['access_token']) ? $result['body']['access_token'] : null;
            return ['access_token' => $access_token];
        }
        else
        {
            return ['message' => $result['message'] ?? ''];
        }
    }

    public static function getImgixImage($image, $withAssetPath=true, $width="", $height = "")
    {
        if(empty($image))
        {
            return "";
        }
        else
        {
            $basePath = (($withAssetPath) ? env('ASSETS_BASE_PATH',) : env('IMGIX_BASE_PATH','https://bsecure-dev-images.imgix.net'));
            $imageUrl = $basePath.'/'.$image.'?auto=compress';

            if($width != "")
            {
                $imageUrl .= "&w=$width";
            }
            if($height != "")
            {
                $imageUrl .= "&h=$height";
            }

            return $imageUrl;
        }
    }

    public static function moveToTop(&$array, $key) {
//        $temp = array($key => $array[$key]);
//        unset($array[$key]);
//        $array = $temp + $array;
//        return (array) $array;

        $last_key     = key($array);
        $last_value   = array_pop($array);
        return array_merge(array($last_key => $last_value), $array);
    }

    public static function moveToBottom(&$array, $key) {
        $value = $array[$key];
        unset($array[$key]);
        $array[$key] = $value;
        return $array;
    }

    public static function generateAccessToken($request, $store) {
        $scheme = (strtolower(config('app.APP_ENV')) == "local") ? "http" : "https";
        $url = "{$scheme}://".config('app.API_DOMAIN')."/v1/passport/token";

        $tokenRequest = Request::create($url, 'POST', $request->all());
        $response = app()->handle($tokenRequest);

        if ($response->getStatusCode() == Http::$Codes[Http::SUCCESS]) {
            $responseBody = json_decode($response->getContent(), true);

            $token = $responseBody['access_token'];
            $id = app(JwtParser::class)->parse($token)->claims()->get('jti');

            $storeSlug = optional($store)->store_slug;
            AccessToken::addIdentifierToAccessToken($id, $storeSlug );

            return [
                'status' => Http::$Codes[Http::SUCCESS],
                'result' => $responseBody,
                'message' => ''
            ];
        }
        return [
            'status' => Http::$Codes[Http::BAD_REQUEST],
            'result' => '',
            'message' => __('messages.general.failed')
        ];
    }

    public static function getMerchantDirectory($merchant_id, $type)
    {
        return env($type) . "/$merchant_id/";
    }

    public static function formatNumber($number, $decimals = 2)
    {
        return (float)number_format((float)$number, $decimals, '.', '');
    }

    static function validationErrors($request, $rules, $messages = [])
    {
        if (is_array($request))
        {
            $formData = $request;
        }
        else
        {
            $formData = $request->all();
        }

        $validator = Validator::make($formData, $rules, $messages);

        if ($validator->fails())
        {
            return $validator->errors();
        }
        else
        {
            return false;
        }
    }

    static function uploadFile($file, $destinationPath, $fileName, $isFile = true)
    {

        if (config('app.upload_to_s3'))
        {
            if ($isFile)
            {
                $fileContent = file_get_contents($file);
            }
            else
            {
                $fileContent = $file;
            }
            Storage::disk('s3')->put($destinationPath . '/' . $fileName, $fileContent, 'public');
        }
        else
        {
            $file->move(public_path($destinationPath), $fileName);
        }
    }

    static function uploadFileToApp($file, $fileName, $filePath) {
        if (!File::exists(public_path($filePath))) {
            File::makeDirectory(public_path($filePath), 0775, true);
        }

        list($mime, $data)   = explode(';', $file);
        list(, $data)       = explode(',', $data);
        $data = base64_decode($data);

        $mime = explode(':',$mime)[1];
        $ext = explode('/',$mime)[1];
        $savePath = $filePath.$fileName.'.'.$ext;

        file_put_contents(public_path().'/'.$savePath, $data);
        return $savePath;
    }

    static function getMimeType($imagedata)
    {
        $pos  = strpos($imagedata, ';');
        $type = explode(':', substr($imagedata, 0, $pos))[1];
        return $type;
    }

    static function convertUrlToDataURI($image) {
        return base64_encode(file_get_contents($image));
    }
}
