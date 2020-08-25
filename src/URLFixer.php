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
     * Get WordPress Install Dir
     *
     * @param string $prepend
     * @param string $append
     *
     * @return string
     */
    public function wpInstallDir($prepend = '', $append = '')
    {
        return $prepend . defined(WP_INSTALL_DIR) ? WP_INSTALL_DIR : 'wp' . $append;
    }

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
        $wp_path = self::wpInstallDir('/');

        if (substr($value, -strlen($wp_path)) === $wp_path) {
            $value = substr($value, 0, -3);
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
        $wp_path = self::wpInstallDir('/');

        if (substr($url, -strlen($wp_path)) !== $wp_path && (is_main_site() || is_subdomain_install())) {
            $url .= $wp_path;
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
        $wp_path = self::wpInstallDir('', '/');


        if (substr($url, -strlen($wp_path)) !== $wp_path) {
            $url .= $wp_path;
        }

        return $url . $path;
    }
}
