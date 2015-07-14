<?php
// Default template - post-final-default.psd

global $loop_module_id, $loop_sidebar_position, $post;



//global $wp_query;
//var_dump($wp_query->query_vars);

td_global::load_single_post($post);


/*  ----------------------------------------------------------------------------
    the post template (single article template)
 */

get_header();


//set the template id, used to get the template specific settings
$template_id = 'home';  //home = blog index = blog - use the same settings from the blog index

//prepare the loop variables

//read the global settings
$loop_sidebar_position = td_util::get_option('tds_' . $template_id . '_sidebar_pos'); //sidebar right is default (empty)
$loop_module_id = 1; //use the default 1 module (full post)

//read the primary category sidebar position! - we have to make the page after the primary category or after the global setting
$primary_category_id = td_global::get_primary_category_id();
if (!empty($primary_category_id)) {
    $tax_meta_sidebar = td_util::get_category_option($primary_category_id, 'tdc_sidebar_pos');//swich by RADU A, get_tax_meta($primary_category_id, 'tdc_sidebar_pos');
    if (!empty($tax_meta_sidebar)) {
        //update the sidebar position from the category setting
        $loop_sidebar_position = $tax_meta_sidebar;
    }
}


//read the custom single post settings - this setting overids all of them
$td_post_theme_settings = get_post_meta($post->ID, 'td_post_theme_settings', true);
if (!empty($td_post_theme_settings['td_sidebar_position'])) {
    $loop_sidebar_position = $td_post_theme_settings['td_sidebar_position'];
}

//set the content width if needed (we already have the default in functions)
if ($loop_sidebar_position == 'no_sidebar') {
    $content_width = 980;
}

//send the sidebar position to gallery
td_global::$cur_single_template_sidebar_pos = $loop_sidebar_position;

//increment the views counter
td_page_views::update_page_views($post->ID);


//added by Radu A. check if this post have a post template to be display with.
//if not use the default site post template from Theme Panel -> Post Settings -> Default site post template
$td_default_site_post_template = td_util::get_option('td_default_site_post_template');

if(empty($td_post_theme_settings['td_post_template']) and !empty($td_default_site_post_template)) {
    $td_post_theme_settings['td_post_template'] = $td_default_site_post_template;
}

// sidebar position used to align the breadcrumb on sidebar left
$td_sidebar_position = '';
if($loop_sidebar_position == 'sidebar_left') {
	$td_sidebar_position = 'td-sidebar-left';
}

if (empty($td_post_theme_settings['td_post_template'])) {
    $td_mod_single = new td_module_single($post);

?>
<div class="td-main-content-wrap">

    <div class="td-container td-post-template-default <?php echo $td_sidebar_position; ?>">
        <div class="td-crumb-container"><?php echo td_page_generator::get_single_breadcrumbs($td_mod_single->title); ?></div>

        <div class="td-pb-row">
            <?php

            //the default template
            switch ($loop_sidebar_position) {
                default: //sidebar right
                    ?>
                        <div class="td-pb-span8 td-main-content" role="main">
                            <div class="td-ss-main-content">
                                <?php
                                locate_template('loop-single.php', true);
                                comments_template('', true);
                                ?>
                            </div>
                        </div>
                        <div class="td-pb-span4 td-main-sidebar" role="complementary">
                            <div class="td-ss-main-sidebar">
                                <?php get_sidebar(); ?>
                            </div>
                        </div>
                    <?php
                    break;

                case 'sidebar_left':
                    ?>
                        <div class="td-pb-span8 td-main-content <?php echo $td_sidebar_position; ?>-content" role="main">
                            <div class="td-ss-main-content">
                                <?php
                                locate_template('loop-single.php', true);
                                comments_template('', true);
                                ?>
                            </div>
                        </div>
		                <div class="td-pb-span4 td-main-sidebar" role="complementary">
			                <div class="td-ss-main-sidebar">
				                <?php get_sidebar(); ?>
			                </div>
		                </div>
                    <?php
                    break;

                case 'no_sidebar':
                    td_global::$load_featured_img_from_template = 'td_1068x0';
                    ?>
                        <div class="td-pb-span12 td-main-content" role="main">
                            <div class="td-ss-main-content">
                                <?php
                                locate_template('loop-single.php', true);
                                comments_template('', true);
                                ?>
                            </div>
                        </div>
                    <?php
                    break;

            }
            ?>
        </div> <!-- /.td-pb-row -->
    </div> <!-- /.td-container -->
</div> <!-- /.td-main-content-wrap -->

<?php

} else {
    //the user has selected a different template & we make sure we only load our templates - the list of allowed templates is in includes/wp_booster/td_global.php
    td_api_single_template::_helper_show_single_template($td_post_theme_settings['td_post_template']);
}
get_footer();