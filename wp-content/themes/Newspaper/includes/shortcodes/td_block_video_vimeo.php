<?php
/**
 * Created by PhpStorm.
 * User: tagdiv
 * Date: 30.12.2014
 * Time: 13:27
 */

require_once(get_template_directory() . '/includes/wp_booster/td_video_playlist_render.php');

//class for vimeo playlist shortcode
class td_block_video_vimeo extends td_block {
    function render($atts, $content = null) {
        //load the froogaloop library for vimeo
        wp_enqueue_script('td-froogaloop', get_template_directory_uri() . '/includes/js_files/vimeo_froogaloop.js', array('jquery'), TD_THEME_VERSION, true); //load at beginning

        return td_video_playlist_render::render_generic($atts, 'vimeo');
    }
}