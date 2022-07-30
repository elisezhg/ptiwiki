<?php

function getLogoutURL()
{
  $url = $_SERVER['REQUEST_URI'];
  $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'action=logout';
  return $url;
}

function getURLWithoutActionParam()
{
  $url = $_SERVER['REQUEST_URI'];
  $url = preg_replace('~(\?|&)action=[^&]*~', '', $url);
  return $url;
}
