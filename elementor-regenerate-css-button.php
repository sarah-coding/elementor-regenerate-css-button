<?php
/**
 * Plugin Name: Elementor Regenerate CSS Button
 * Plugin URI: https://github.com/sarah-coding/elementor-regenerate-css-button
 * Description: Link the "Regenerate CSS" button into the admin menubar.
 * Author: SarahCoding
 * Author URI: https://sarahcoding.com
 * Version: 1.0.0
 * Text Domain: ercb
 * Tested up to: 5.5
 */

/**
 * Copyright (c) SarahCoding <contact.sarahcoding@gmail.com>
 *
 * This source code is licensed under the license
 * included in the root directory of this application.
 */

/**
 * Make sure translation is available.
 *
 * @internal Used as a hook.
 *
 * @see https://developer.wordpress.org/reference/hooks/plugins_loaded/
 */
function sc_ercb_i18n()
{
    load_plugin_textdomain('elementor-regenerate-css-button', false, 'elementor-regenerate-css-button/languages');
}
add_action('plugins_loaded', 'sc_ercb_i18n', 10, 0);

/**
 * Add button to the admin bar
 *
 * @internal Used as a hook.
 *
 * @see https://developer.wordpress.org/reference/hooks/admin_bar_menu/
 */
function sc_link_elementor_clear_cache_button(WP_Admin_Bar $menubar)
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $menubar->add_node([
        'id'    => 'sc-regenerate-elementor-css',
        'title' => esc_html__('Regenerate Elementor CSS', 'elementor-regenerate-css-button'),
        'href'  => "#",
        'meta'  => ['class' => wp_create_nonce('elementor_clear_cache')]
    ]);
}
add_action('admin_bar_menu', 'sc_link_elementor_clear_cache_button', 202);

/**
 * Add inline scripts and styles for admin bar.
 *
 * @internal Used as a hook.
 */
function sc_ercb_add_inline_css()
{
    wp_localize_script('admin-bar', 'ercbI18n', [
        'confirm' => esc_html__('Are you sure? This action cannot be undone!', 'elementor-regenerate-css-button')
    ]);

    wp_add_inline_script('admin-bar', 'jQuery(()=>{jQuery("#wp-admin-bar-sc-regenerate-elementor-css > .ab-item").on("click",c=>{c.preventDefault();const go=confirm(ercbI18n.confirm);if(!go) return;const d=jQuery(c.currentTarget),a=d.parent("li");a.length&&(d.removeClass("success").addClass("loading"),jQuery.post(elementorCommonConfig.ajax.url,{action:"elementor_clear_cache",_nonce:a.attr("class")}).done(()=>{d.removeClass("loading").addClass("success"),setTimeout(()=>d.removeClass("success"),3e3)}))})});');

    wp_add_inline_style('admin-bar', '@keyframes ercbRotation{0%{transform:rotate(0deg)}100%{transform:rotate(359deg)}}#wp-admin-bar-sc-regenerate-elementor-css > .ab-item::before{font-family:"eicons";content:"\e8a6";margin-right:2px;padding:0;line-height:32px}#wp-admin-bar-sc-regenerate-elementor-css > .loading::before{animation:ercbRotation 1s infinite linear}#wp-admin-bar-sc-regenerate-elementor-css > .success::before{content:"\e90e"}');
}
add_action('admin_bar_init', 'sc_ercb_add_inline_css');
