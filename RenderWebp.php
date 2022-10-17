<?php

/*
    Plugin Name:        RenderWebp
    Description:        Webp functions for dev
    Version:            0.1.0
    Author:             Jérémy Essig
    License:            GPLv2 or later
    License URI:        http://www.gnu.org/licenses/gpl-2.0.html
    Requires PHP:       5.6 or higher
    Requires at least:  4.9 or higher

 */

use RenderWebp as GlobalRenderWebp;

defined('ABSPATH') || exit;

if (!class_exists('Webp')) {

    class RenderWebp
    {

        public $version = '0.1.0';

        private $plugin_url;

        /**
         * Disabling the constructor to create a singleton ! 
         */
        public function __construct()
        {
            // Silence is gold and we agree
        }

        /**
         * Initialize the plugin
         * Because we are in a singleton, we create this method to replace the constructor
         * We returning a Methodik instance to use the fluent pattern.
         * 
         * @return RenderWebp
         */
        public function init(): RenderWebp
        {
            $this->plugin_url = plugin_dir_url(__FILE__);
            add_shortcode('webp', array($this, 'shortcode_img'));

            return $this;
        }

        /**
         * Shortcode to return an img
         *
         * @param [type] $atts
         * @return void
         */
        public function shortcode_img($atts): void
        {
            $atts = shortcode_atts(
                array(
                    'path' => '',
                    'loading' => '',
                    'alt' => '',
                    'title' => ''
                ),
                $atts,
                'webp'
            );

            RenderWebp::img(
                $atts['path'],
                array(
                    'alt' => $atts['alt'],
                    'title' => $atts['title'],
                    'loading' => $atts['loading']
                )
            );
        }

        /**
         * Render a srcest snippet code for webp and other images files.
         * args can be: loading, alt and title
         * Important: you must provide two images: a webp and an other format in the same directory
         *
         * @param string $path
         * @param array $args
         * @return void
         */
        public static function img(string $path, array $args = []): void
        {
            $loading = isset($args['loading']) ? $args['loading'] : '';

            $alt = isset($args['alt']) ? $args['alt'] : '';

            $title = isset($args['title']) ? $args['alt'] : '';

            if (mb_substr($path, 0, 1) != '/') {
                $path = '/' . $path;
            }

            // Liste des extensions autrisées
            $type_authorized = array("image/jpg", "image/png", "image/jpeg", "image/gif");


            $img_path = esc_url(get_template_directory_uri() . $path);

            // Vérification du MIME du fichier
            $img_type = mime_content_type(get_template_directory() . $path);

            if (!in_array($img_type, $type_authorized)) {
                return;
            }

            $alt_html = !empty($alt) ? 'alt="' . esc_attr($alt) . '"' : '';
            $loading_html = !empty($loading) ? 'loading="' . esc_attr($loading) . '"' : '';
            $title_html = !empty($title) ? 'title="' . esc_attr($title) . '"' : '';

            $img_webp = explode(".", $path)[0];
            $img_webp = get_template_directory_uri() . $img_webp . ".webp";

            $html = '<picture>';
            $html .= '<source srcset="' . $img_webp . '" type="image/webp">';
            $html .= '<source srcset="' . $img_path . '" type="image/' . $img_type . '">';
            $html .= '<img src="' . esc_attr($img_path) . '" ' . $alt_html . ' ' . $title_html . ' ' . $loading_html . '>';
            $html .= '</picture>';
            echo $html;
        }
    }
}


$render_webp = (new RenderWebp())->init();
