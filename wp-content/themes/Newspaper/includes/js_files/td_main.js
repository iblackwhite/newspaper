"use strict";



/**
 * affix menu
 */
td_affix.init({
    menu_selector: '.td-header-menu-wrap',
    menu_wrap_selector: '.td-header-menu-wrap-full',
    tds_snap_menu: td_util.get_backend_var('tds_snap_menu'),
    tds_snap_menu_logo: td_util.get_backend_var('tds_logo_on_sticky'),
    menu_affix_height: 48,   // value must be set because it can't be computed at runtime because at the time of td_affix.init() we can have no affixed menu on page
    menu_affix_height_on_mobile: 54
});


/**
 * smooth scroll init
 */
/*
jQuery().ready(function () {
    if (td_detect.is_chrome === true && td_detect.is_osx === false) {
        td_smooth_scroll();
    }
});
*/


/**
 * sidebar init
 */
if (td_util.get_backend_var('tds_smart_sidebar') == 'enabled' && td_detect.is_ios === false) {
    jQuery(window).load(function() {
        // find the rows and the sidebars objects and add them to the magic sidebar object array
        jQuery('.td-ss-row').each(function () {
            //@todo check to see if the sidebar + content is pressent
            var td_smart_sidebar_item = new td_smart_sidebar.item();
            td_smart_sidebar_item.sidebar_jquery_obj = jQuery(this).children('.td-pb-span4').children('.wpb_wrapper');
            td_smart_sidebar_item.content_jquery_obj = jQuery(this).children('.td-pb-span8').children('.wpb_wrapper');
            td_smart_sidebar.add_item(td_smart_sidebar_item);
        });



        // check the page to see if we have smart sidebar classes (.td-ss-main-content and .td-ss-main-sidebar)
        if (jQuery('.td-ss-main-content').length > 0 && jQuery('.td-ss-main-sidebar').length > 0) {
            var td_smart_sidebar_item = new td_smart_sidebar.item();
            td_smart_sidebar_item.sidebar_jquery_obj = jQuery('.td-ss-main-sidebar');
            td_smart_sidebar_item.content_jquery_obj = jQuery('.td-ss-main-content');
            td_smart_sidebar.add_item(td_smart_sidebar_item);
        }

        td_smart_sidebar.td_events_resize();
    });
}


/**
 * pulldown lists
 *
 */

jQuery('.td-subcat-filter').each(function(index, element) {
    var jquery_object_container = jQuery(element);
    var horizontal_jquery_obj = jquery_object_container.find('.td-subcat-list:first');
    var vertical_jquery_obj = jquery_object_container.find('.td-subcat-dropdown:first');

    if (horizontal_jquery_obj.length == 1 && vertical_jquery_obj.length == 1) {

        var pulldown_item_obj = new td_pulldown.item();

        pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
        pulldown_item_obj.vertical_jquery_obj = vertical_jquery_obj;
        pulldown_item_obj.horizontal_element_css_class = 'td-subcat-item';
        pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.parents('.td_block_wrap:first');
        pulldown_item_obj.excluded_jquery_elements = [horizontal_jquery_obj.parent().siblings('.block-title:first')];

        td_pulldown.add_item(pulldown_item_obj);
    }
});

jQuery('.td-category-siblings').each(function(index, element) {
    var jquery_object_container = jQuery(element);
    var horizontal_jquery_obj = jquery_object_container.find('.td-category:first');
    var vertical_jquery_obj = jquery_object_container.find('.td-subcat-dropdown:first');

    if (horizontal_jquery_obj.length == 1 && vertical_jquery_obj.length == 1) {

        var pulldown_item_obj = new td_pulldown.item();

        pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
        pulldown_item_obj.vertical_jquery_obj = vertical_jquery_obj;
        pulldown_item_obj.horizontal_element_css_class = 'entry-category';
        pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.parents('.td-category-siblings:first');

        td_pulldown.add_item(pulldown_item_obj);
    }
});





/**
 * parallax effect
 */

// array keeping the td_animation_scroll.item items used for backstretch
var td_backstretch_items = [];


jQuery(window).ready(function() {

    jQuery('.td-backstretch').each(function (index, element) {

        if (!jQuery(element).hasClass('not-parallax')) {

            var item = new td_animation_scroll.item();
            item.jquery_obj = jQuery(element);
            item.wrapper_jquery_obj = item.jquery_obj.parent();

            // - ideal translation is when the top of wrapper_jquery_obj is at the top of the view port, the jquery_obj image
            // is also already translated at the top of the view port
            // - the start_value should be item.wrapper_jquery_obj.offset().top + how much the jquery_obj was translated

            td_animation_scroll.add_item(item);

            // we keep the td_animation_scroll.item to change operation settings when the viewport is changing
            td_backstretch_items.push(item);

            td_compute_backstretch_item(item);
        }
    });


    jQuery('.td-parallax-header').each(function (index, element) {

        var item = new td_animation_scroll.item();
        item.jquery_obj = jQuery(element);

        item.add_item_property('move_y', 50, 100, 0, 100, '');
        item.add_item_property('opacity', 50, 100, 1, 0, '');

        item.animation_callback = function () {

            var move_y_property = parseFloat(item.computed_item_properties['move_y']).toFixed();
            var opacity_property = parseFloat(item.computed_item_properties['opacity']);

            item.jquery_obj.css({
                '-webkit-transform': 'translate3d(0px,' + move_y_property + 'px, 0px)',
                'transform': 'translate3d(0px,' + move_y_property + 'px, 0px)'
            });

            item.jquery_obj.css('transform', 'translate3d(0px,' + move_y_property + 'px, 0px)');

            item.jquery_obj.css('opacity', opacity_property);

            item.redraw = false;
        }

        td_animation_scroll.add_item(item);
    });


    td_animation_scroll.compute_all_items();




    // load animation stack
    td_animation_stack.ready_init();
});




/**
 * Function used to register the 'move_y' property for every td_animations_scroll.item item of the td_backstretch_items array.
 * It scales the object image and translate it. At first it is translated so its bottom is at the bottom of the viewport,
 * but considering the backstretch css classes applied
 *
 * @param item td_animation_scroll.item
 */
function td_compute_backstretch_item(item) {

        // Important! The following variables must be computed after add_item calling function, because they need item.full_height, item.offset_top, etc

        // percent when the object is in initial position
        // Important! It doesn't matter if the document is scrolled
        var initial_percent = (td_events.window_innerHeight - item.offset_top) * 100 / (td_events.window_innerHeight + item.full_height);

        // percent when the object has its top at the top of the window
        var intermediary_top_percent =  (td_events.window_innerHeight) * 100 / (td_events.window_innerHeight + item.full_height);


        // IMPORTANT! We suppose the item.offset_top is positive


        // the value used to compute the scale_factor
        // Important! It can be any value
        var scale_seed = item.offset_top / 2;

        // if item.offset_top is zero, we set the scale_seed at a custom value
        if (scale_seed == 0) {
            scale_seed = 100;
        }

        // the start_value is half of the scale_seed, so the object [image] is translated as its bottom is at the bottom of its view
        var start_value = - scale_seed / 2;


        // DO NOT DELETE THE NEXT CODE LINES. The right value would be the next, but the divide operation does not have 100% accuracy, so we increase the interval
        // and so we are sure the object is not translated more than needed when is at the top of the window
        //
        // When the top of the view is at the top of the window, the object [image] must be already translated at the top of the window.
        //
        //var end_value = ((100 - initial_percent) * scale_seed) / (intermediary_top_percent - initial_percent);;
        //
        //or actually
        //
        //var end_value = ((100 - initial_percent) * (item.offset_top / 2)) / (intermediary_top_percent - initial_percent);;

        var end_value = ((100 - initial_percent) * (scale_seed / 1.2)) / (intermediary_top_percent - initial_percent);

        // fix for firefox. It rounds up and loose 1 pixel
        start_value += 0.5;


        // if there already exists a 'move_y' property, it is removed
        item.remove_item_property('move_y');

        item.add_item_property('move_y', initial_percent, 100, start_value, end_value, '');


        var scale_factor = parseFloat(1 + Math.abs(scale_seed) / item.full_height).toFixed(2);


        // if there's already registered an 'animation_callback' function, it is removed
        delete item.animation_callback;

        item.animation_callback = function () {

            var property_value = parseFloat(item.computed_item_properties['move_y']).toFixed();

            item.jquery_obj.css({
                'left': '50%',
                '-webkit-transform': 'translate3d(-50%,' + property_value + 'px, 0px) scale(' + scale_factor + ',' + scale_factor + ')',
                'transform': 'translate3d(-50%,' + property_value + 'px, 0px) scale(' + scale_factor + ',' + scale_factor + ')'
            });

            item.redraw = false;
        }
}
