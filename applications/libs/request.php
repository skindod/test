<?php

/**
 * Class to handle request of post and get. The data are filtered before it is returned
 */
class request
{
    private $method;

    function __construct()
    {
        // Detect the request method
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    function request($method, $key = '')
    {
        $request_data = array();

        if ($method == 'get') {

            $request_data = $_GET;
        }
        else if ($method == 'post') {

            $request_data = json_decode(file_get_contents('php://input'), true);

            if (!is_array($request_data)) {
                $request_data = $_POST;
            }
        }

        if ($key == '')
        {
            return $request_data;
        }
        else if (isset($request_data[$key])) 
        {
            if(is_array($request_data[$key]))
            {
                //return filter_var_array($request_data[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                //return filter_var_array($request_data[$key], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data_array = $request_data[$key];
                $data_array_cleaned = array();
                foreach($data_array as $key => $value)
                    $data_array_cleaned[$key] = $this->htmlpurifier($value);
                return $data_array_cleaned;
            }
            else
            {
                //return filter_var($request_data[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                //return filter_var($request_data[$key], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                return $this->htmlpurifier($request_data[$key]);
            }
        }
    }

    function get($key = '')
    {
        return $this->request('get', $key);
    }

    function post($key = '')
    {
        return $this->request('post', $key);
    }
    
    function is_exist($type = 'get', $key = '')
    {
        if($type == 'get')
        {
            if($key != '' && isset($_GET[$key]))
                return true;
            else
                return false;
        }
        else if($type == 'post')
        {
            if($key != '' && isset($_POST[$key]))
                return true;
            else
                return false;
        }
        else
            return false;
    }
    
    /**
     * Function taken from http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
     * this is to prevent xss attacks
     * 
     * @param string $data String to clean
     * @return string Cleaned string
     */
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }
    
    function htmlpurifier($data)
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        return htmlspecialchars($purifier->purify($data));
    }
}

?>