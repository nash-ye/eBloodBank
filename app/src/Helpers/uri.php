<?php

/**
 * @return bool
 * @since 1.0
 */
function isHTTPS()
{
    return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
}

/**
 * @return string
 * @since 1.0
 */
function getHomeURL($format = 'absolute')
{
    $url = '';

    if (empty($format)) {
        $format = 'absolute';
    }

    switch (strtolower($format)) {

        case 'absolute':
            if (defined('EBB_URL')) {
                $url = EBB_URL;
            } else {
                $uri_scheme = isHTTPS() ? 'https' : 'http';
                $uri_host = $_SERVER['HTTP_HOST'];
                $url_path = dirname($_SERVER['SCRIPT_NAME']);
                $url_path = str_replace('\\', '/', $url_path);
                $url = $uri_scheme . '://' . $uri_host . $url_path;
            }
            break;

        case 'relative':
            $url = (string) parse_url(getHomeURL(), PHP_URL_PATH);
            break;

    }

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getSiteURL($path = '', $format = 'absolute')
{
    $url = getHomeURL($format);

    if (! empty($path)) {
        $url = trimTrailingSlash($url) . '/' . ltrim( $path, '/' );
    }

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function addQueryArgs($uri, array $query_args)
{
    if (empty($query_args)) {
        return $uri;
    }

    $fragment = strstr($uri, '#');
    if (! empty($fragment)) {
        $uri = substr($uri, 0, -strlen($fragment));
    }

	if (strpos($uri, '?') !== false) {
		list($base, $query) = explode('?', $uri, 2);
	} else {
		$base = $uri;
        $query = '';
	}

    $query_array = array();
    parse_str($query, $query_array);

    foreach ($query_args as $key => $value) {
        $query_array[$key] = $value;
    }

	foreach ($query_array as $key => $value) {
        if ($value === false) {
            unset($query_args[$key]);
        }
	}

    $query = http_build_query($query_array);

    return $base . '?' . $query . $fragment;
}