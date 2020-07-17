<?php

namespace Roots\Bedrock;

/**
 * Class URLFixer
 * @package Roots\Bedrock
 * @author Roots
 * @link https://roots.io/
 */
class URLFixer
{
    /**
     * Add filters to verify / fix URLs.
     */
    public function addFilters()
    {
        add_filter('option_home', [$this, 'fixHomeURL']);
        add_filter('option_siteurl', [$this, 'fixSiteURL']);
        add_filter('network_site_url', [$this, 'fixNetworkSiteURL'], 10, 3);
    }

    /**
     * Ensure that home URL does not contain the /wp subdirectory.
     *
     * @param string $value the unchecked home URL
     * @return string the verified home URL
     */
    public function fixHomeURL($value)
    {
        if (substr($value, -10) === '/backstage') {
            $value = substr($value, 0, -10);
        }
        return $value;
    }

    /**
     * Ensure that site URL contains the /wp subdirectory.
     *
     * @param string $url the unchecked site URL
     * @return string the verified site URL
     */
    public function fixSiteURL($url)
    {
        if (substr($url, -10) !== '/backstage' && (is_main_site() || is_subdomain_install())) {
            $url .= '/backstage';
        }
        return $url;
    }

    /**
     * Ensure that the network site URL contains the /wp subdirectory.
     *
     * @param string $url    the unchecked network site URL with path appended
     * @param string $path   the path for the URL
     * @param string $scheme the URL scheme
     * @return string the verified network site URL
     */
    public function fixNetworkSiteURL($url, $path, $scheme)
    {
        $path = ltrim($path, '/');
        $url = substr($url, 0, strlen($url) - strlen($path));

        if (substr($url, -10) !== 'backstage/') {
            $url .= 'backstage/';
        }

        return $url . $path;
    }
}
