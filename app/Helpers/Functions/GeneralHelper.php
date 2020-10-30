<?php

if(!function_exists('buildSocialUrl'))
{
    /**
     * Builds a social url based on the social's config array
     *
     * @return bool
     */

    function buildSocialUrl($social_config_array)
    {
        $url = $social_config_array['url'];
        
        // Check each value in the array to see if it belongs in the url
        foreach($social_config_array as $key => $value)
        {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }
}

if(!function_exists('urlIsLogin'))
{
    /**
     * Checks if the URL is the login URL of the application
     *
     * @return bool
     */
    function urlIsLogin($url)
    {
        return $url === url(route('login'));
    }
}

if(!function_exists('urlIsRoot'))
{
    /**
     * Checks if the URL is the root URL of the application
     *
     * @return bool
     */
    function urlIsRoot($url)
    {
        return $url === config('app.url');
    }
}