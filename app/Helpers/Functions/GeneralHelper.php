<?php

namespace App\Helpers\Functions;

class GeneralHelper
{
    /**
     * Checks if the URL is the root URL of the application
     *
     * @return bool
     */
    public static function urlIsRoot($url)
    {
        return $url === config('app.url');
    }
}