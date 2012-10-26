<?php
class Helpers
{
    public static function isActive($item_url)
    {
        $url = Environment::getHttpRequest()->getUri()->getAbsoluteUri();
        $url_arr = parse_url($url);
        $item_url_arr = parse_url($item_url);
        $url_without_params = $url_arr['host'].$url_arr['path'];
        if(isset($item_url_arr['path']))
            $item_url_without_params = $item_url_arr['host'].$item_url_arr['path'];
        else $item_url_without_params = $item_url_arr['host'];
        return $url_without_params == $item_url_without_params;
    }
}

?>