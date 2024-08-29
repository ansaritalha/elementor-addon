<?php
/*
Plugin Name: Custom Elementor Controls
Description: Adds custom URL controls to Besa Elementor Product Categories Tabs widget and modifies the button HTML.
Version: 1.0
Author: Talha Ansari
Author URI: https://fastwebsites.pro/
            https://www.fiverr.com/ansari_talha?up_rollout=true
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom URL controls to the Besa Elementor Product Categories Tabs widget.
 *
 * @param \Elementor\Widget_Base $widget The widget instance.
 * @param string $section_id The section ID.
 * @param array $args Additional arguments.
 */
function add_custom_url_controls( $widget, $section_id, $args ) {
    if ( 'besa-product-categories-tabs' === $widget->get_name() && 'section_general' === $section_id ) {
        $widget->start_injection(
            [
                'at' => 'after',
                'of' => 'text_button',
            ]
        );

        $widget->add_control(
            'show_custom_url_box',
            [
                'label' => esc_html__( 'Custom URL?', 'besa' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $widget->add_control(
            'my_custom_btn_url',
            [
                'label' => esc_html__( 'URL for Button', 'besa' ),
                'type' => \Elementor\Controls_Manager::URL,
                'condition' => [
                    'show_custom_url_box' => 'yes',
                ],
            ]
        );

        $widget->end_injection();
    }
}
add_action( 'elementor/element/before_section_end', 'add_custom_url_controls', 10, 3 );

function modify_product_categories_tabs_button( $widget_content, $widget ) {
    
    if ( 'besa-product-categories-tabs' === $widget->get_name() ) {
        $settings = $widget->get_settings_for_display();
        $text_button = isset($settings['text_button']) ? $settings['text_button'] : '';
        $icon_button = isset($settings['icon_button']) ? $settings['icon_button'] : '';
        $show_custom_url_box = isset($settings['show_custom_url_box']) ? $settings['show_custom_url_box'] : 'no';
        $my_custom_btn_url = isset($settings['my_custom_btn_url']) ? $settings['my_custom_btn_url']['url'] : '';

        // Default URL
        $url_category = get_permalink(wc_get_page_id('shop'));

        // Check if custom URL control is enabled and not empty
        if ( 'yes' === $show_custom_url_box && !empty($my_custom_btn_url) ) {
            $url_category = esc_url($my_custom_btn_url);
        }

        // Use regex to update href attribute only
        $pattern = '/<a href=(.*?) class="btn">/';
        $replacement = '<a href="' . esc_url($url_category) . '" class="btn">';

        $widget_content = preg_replace($pattern, $replacement, $widget_content);
    }

    return $widget_content;
}
add_filter( 'elementor/widget/render_content', 'modify_product_categories_tabs_button', 10, 2 );