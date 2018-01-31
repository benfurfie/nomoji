<?php
/**
 * Plugin Name: Nomoji
 * Description: Removes all traces of the emoji script from WordPress.
 * Author: Ben Furfie
 * Author URI: https://www.benfurfie.co.uk
 * Version: 0.1.2
 * Copyright: Ben Furfie © 2018
 * License: GPL2.0+
 * 
 * @package WordPress
 * @subpackage Nomoji
 * @author Ben Furfie <hello@benfurfie.co.uk>
 * @copyright 2018 Ben Furfie
 * @version 0.1.2
 * @license GPL2.0+
 */
namespace nomoji;

defined('ABSPATH') or die('Where do you think you\'re going?');

class Nomoji
{
    function __construct()
    {
        add_action('init', array($this, 'remove_emojis'));
        add_filter('tiny_mce_plugins', array($this, 'disable_emojis_tinymce'));
        add_filter('wp_resource_hints', array($this, 'disable_emojis_remove_dns_prefetch', 10, 2));
    }

    /**
     * Disable the emoji script and styles.
     * 
     * @since 0.1.0
     */
    function remove_emojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles'); 
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji'); 
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    /**
     * Remove emojis from the tinymce plugin.
     * 
     * @since 0.1.0
     * @param array $plugins 
     * @return array Difference betwen the two arrays
     */
    function remove_emojis_from_tinymce($plugins)
    {
        if(is_array($plugins))
        {
            return array_diff($plugins, array('wpemoji'));
        }
        else {
            return array();
        }
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     * 
     * @param array $urls URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     * @return array Difference betwen the two arrays.
     */
    function prevent_emoji_cdn_prefetch($relation_type)
    {
        if('dns-prefetch' == $relation_type)
        {
            $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
            $urls = array_diff($urls, array($emoji_svg_url));
        }
        return $urls;
    }
}

$nomojis = new Nomoji();