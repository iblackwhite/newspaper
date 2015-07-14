<?php
/*  ----------------------------------------------------------------------------
    the default page template
 */


get_header();


//set the template id, used to get the template specific settings
$template_id = 'page';


$loop_sidebar_position = td_util::get_option('tds_' . $template_id . '_sidebar_pos'); //sidebar right is default (empty)

//get theme panel variable for page comments side wide
$td_enable_or_disable_page_comments = td_util::get_option('tds_disable_comments_pages');


//read the custom single post settings - this setting overids all of them
$td_page = get_post_meta($post->ID, 'td_page', true);
if (!empty($td_page['td_sidebar_position'])) {
    $loop_sidebar_position = $td_page['td_sidebar_position'];
}

// sidebar position used to align the breadcrumb on sidebar left + sidebar first on mobile issue
$td_sidebar_position = '';
if($loop_sidebar_position == 'sidebar_left') {
	$td_sidebar_position = 'td-sidebar-left';
}


/**
 * detect the page builder
 */
$td_use_page_builder = false;
if (method_exists('WPBMap', 'getShortCodes')) {
    $td_page_builder_short_codes = array_keys(WPBMap::getShortCodes());
    if (td_util::strpos_array($post->post_content, $td_page_builder_short_codes) === true) {
        $td_use_page_builder = true;
    }
}





if ($td_use_page_builder) {

    // the page is rendered using the page builder template (no sidebars)
    if (have_posts()) { ?>
        <?php while ( have_posts() ) : the_post(); ?>

            <div class="td-main-content-wrap td-main-page-wrap">
                <div class="td-container">
                    <?php the_content(); ?>
                </div>
            </div> <!-- /.td-main-content-wrap -->


        <?php endwhile; ?>
    <?php }
} else {

    //no page builder detected, we load a default page template with sidebar / no sidebar
    ?>

<div class="td-main-content-wrap">
    <div class="td-container <?php echo $td_sidebar_position; ?>">
        <div class="td-crumb-container">
            <?php echo td_page_generator::get_page_breadcrumbs(get_the_title()); ?>
        </div>
        <div class="td-pb-row">
            <?php
            switch ($loop_sidebar_position) {
                default:
                    ?>
                        <div class="td-pb-span8 td-main-content" role="main" itemscope="itemscope" itemprop="mainContentOfPage" itemtype="<?php echo td_global::$http_or_https?>://schema.org/CreativeWork">
                            <div class="td-ss-main-content">
                                <?php
                                if (have_posts()) {
                                    while ( have_posts() ) : the_post();
                                        ?>
                                        <div class="td-page-header">
                                            <h1 itemprop="name" class="entry-title td-page-title">
                                                <span><?php the_title() ?></span>
                                            </h1>
                                        </div>
                                        <div class="td-page-content">
                                        <?php
                                            the_content();
                                    endwhile;//end loop

                                }
                                ?>
                                </div>
                                <?php
                                if($td_enable_or_disable_page_comments == 'show_comments') {
                                    comments_template('', true);
                                }?>
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
                    <div class="td-pb-span8 td-main-content <?php echo $td_sidebar_position; ?>-content" role="main" itemscope="itemscope" itemprop="mainContentOfPage" itemtype="<?php echo td_global::$http_or_https?>://schema.org/CreativeWork">
                        <div class="td-ss-main-content">
                            <?php

                            if (have_posts()) {
                                while ( have_posts() ) : the_post();
                                    ?>
                                    <div class="td-page-header">
                                        <h1 itemprop="name" class="entry-title td-page-title">
                                            <span><?php the_title() ?></span>
                                        </h1>
                                    </div>
                                    <div class="td-page-content">
                                    <?php
                                    the_content();
                                endwhile; //end loop
                            }
                            ?>
                            </div>
                            <?php
                            if($td_enable_or_disable_page_comments == 'show_comments') {
                                comments_template('', true);
                            }?>
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
                    ?>
                    <div class="td-pb-span12 td-main-content" role="main" itemscope="itemscope" itemprop="mainContentOfPage" itemtype="<?php echo td_global::$http_or_https?>://schema.org/CreativeWork">

                        <?php
                        if (have_posts()) {
                            while ( have_posts() ) : the_post();
                                ?>
                                <div class="td-page-header">
                                    <h1 itemprop="name" class="entry-title td-page-title">
                                        <span><?php the_title() ?></span>
                                    </h1>
                                </div>
                                <div class="td-page-content">
                                <?php
                                the_content();
                            endwhile; //end loop
                        }
                        ?>
                        </div>
                        <?php
                        if($td_enable_or_disable_page_comments == 'show_comments') {
                            comments_template('', true);
                        }?>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div> <!-- /.td-pb-row -->
    </div> <!-- /.td-container -->
</div> <!-- /.td-main-content-wrap -->

    <?php
}




get_footer();