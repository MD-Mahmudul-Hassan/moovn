<?php

function to_lowercase($str)
{
    return strtolower($str);
}

function get_first_name($str)
{
    $name_arr = explode(' ', $str);
    return $name_arr[0];
}

function set_default_timezone($timezone)
{
    date_default_timezone_set($timezone);
}