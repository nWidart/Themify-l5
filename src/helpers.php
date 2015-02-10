<?php

if (! function_exists('theme_url')) {
    function theme_url()
    {
        return url() . '/' . app('themify')->assetsPath();
    }
}

if (! function_exists('theme_secure_url')) {
    function theme_secure_url()
    {
        return url(null, null, true) . '/' . app('themify')->assetsPath();
    }
}
