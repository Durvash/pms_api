<?php

function getFormatedDate($date)
{
    $return_val = '--';
    $dateToTime = strtotime($date);

    if($date && $dateToTime > 0)
    {
        $return_val = date(DATE_FORMAT, $dateToTime);
    }
    
    return $return_val;
    
    /* $CURRENT_TIME_ZONE = date_default_timezone_get();
    $CONVERT_TIME_ZONE = 'UTC';
    $dt = new DateTime($date, new DateTimeZone($CURRENT_TIME_ZONE));
    $dt->setTimezone(new DateTimeZone($CONVERT_TIME_ZONE));
    $dt->format(DATE_FORMAT);
    // var_dump($dt);die;
    return $dt->date;
    */
}

function getFormatedDateTime($date)
{
    $return_val = '--';
    $dateToTime = strtotime($date);

    if($date && $dateToTime > 0)
    {
        $return_val = date(DATE_TIME_FORMAT, $dateToTime);
    }
    
    return $return_val;
}

function getCurrentDate()
{
    return date('Y-m-d');
}

function getCurrentDateTime()
{
    return date('Y-m-d H:i:s');
}

function getFormatedCurrentDate()
{
    return $this->getFormatedDate(date('Y-m-d'));
}

function getFormatedCurrentDateTime()
{
    return $this->getFormatedDateTime(date('Y-m-d H:i:s'));
}

function pr($data, $exit = '')
{
    echo '<pre>';
    print_r($data);
    if($exit) exit;
}

function encryption($string, $action='e')
{
    $secret_key = 'pms-123-trying-456-my-789-first-123-fullstack-456-project';
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $key2 = substr(hash('sha256', $secret_key), 0, 16);

    if($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $key2));
    } else if( $action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $key2);
    }

    return $output;
}

function generateToken()
{
    $string = getCurrentDateTime();
    return encryption($string);
}

function removeHtmlTags($string)
{
    return strip_tags($string);
}

function getHeaderValue($key)
{
    $header_data = getallheaders();
    return (isset($header_data[$key])) ? $header_data[$key] : '';
}

function slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}
