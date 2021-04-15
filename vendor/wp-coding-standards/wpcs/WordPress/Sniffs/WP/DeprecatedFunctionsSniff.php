<?php
/**
 * WordPress Coding Standard.
 *
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Restricts the use of various deprecated WordPress functions and suggests alternatives.
 *
 * This sniff will throw an error when usage of deprecated functions is detected
 * if the function was deprecated before the minimum supported WP version;
 * a warning otherwise.
 * By default, it is set to presume that a project will support the current
 * WP version and up to three releases before.
 *
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   0.14.0 Now has the ability to handle minimum supported WP version
 *                 being provided via the command-line or as as <config> value
 *                 in a custom ruleset.
 *
 * @uses    \WordPressCS\WordPress\Sniff::$minimum_supported_version
 */
class DeprecatedFunctionsSniff extends AbstractFunctionRestrictionsSniff
{
    /**
     * List of deprecated functions with alternative when available.
     *
     * To be updated after every major release.
     * Last updated for WordPress 4.8.
     *
     * Version numbers should be fully qualified.
     * Replacement functions should have parentheses.
     *
     * To retrieve a function list for comparison, the following tool is available:
     * https://github.com/JDGrimes/wp-deprecated-code-scanner
     *
     * @var array
     */
    private $deprecated_functions = [

        // WP 0.71.
        'the_category_head' => [
            'alt'     => 'get_the_category_by_ID()',
            'version' => '0.71',
        ],
        'the_category_ID' => [
            'alt'     => 'get_the_category()',
            'version' => '0.71',
        ],

        // WP 1.2.0.
        'permalink_link' => [
            'alt'     => 'the_permalink()',
            'version' => '1.2.0',
        ],

        // WP 1.5.0.
        'start_wp' => [
            // Verified correct alternative.
            'alt'     => 'the Loop',
            'version' => '1.5.0',
        ],

        // WP 1.5.1.
        'get_postdata' => [
            'alt'     => 'get_post()',
            'version' => '1.5.1',
        ],

        // WP 2.0.0.
        'create_user' => [
            'alt'     => 'wp_create_user()',
            'version' => '2.0.0',
        ],
        'next_post' => [
            'alt'     => 'next_post_link()',
            'version' => '2.0.0',
        ],
        'previous_post' => [
            'alt'     => 'previous_post_link()',
            'version' => '2.0.0',
        ],
        'user_can_create_draft' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_create_post' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_delete_post' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_delete_post_comments' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_edit_post' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_edit_post_comments' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_edit_post_date' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_edit_user' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],
        'user_can_set_post_date' => [
            'alt'     => 'current_user_can()',
            'version' => '2.0.0',
        ],

        // WP 2.1.0.
        'dropdown_cats' => [
            'alt'     => 'wp_dropdown_categories()',
            'version' => '2.1.0',
        ],
        'get_archives' => [
            'alt'     => 'wp_get_archives()',
            'version' => '2.1.0',
        ],
        'get_author_link' => [
            'alt'     => 'get_author_posts_url()',
            'version' => '2.1.0',
        ],
        'get_autotoggle' => [
            'alt'     => '',
            'version' => '2.1.0',
        ],
        'get_link' => [
            'alt'     => 'get_bookmark()',
            'version' => '2.1.0',
        ],
        'get_linkcatname' => [
            'alt'     => 'get_category()',
            'version' => '2.1.0',
        ],
        'get_linkobjects' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_linkobjectsbyname' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_linkrating' => [
            'alt'     => 'sanitize_bookmark_field()',
            'version' => '2.1.0',
        ],
        'get_links' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_links_list' => [
            'alt'     => 'wp_list_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_links_withrating' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_linksbyname' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_linksbyname_withrating' => [
            'alt'     => 'get_bookmarks()',
            'version' => '2.1.0',
        ],
        'get_settings' => [
            'alt'     => 'get_option()',
            'version' => '2.1.0',
        ],
        'link_pages' => [
            'alt'     => 'wp_link_pages()',
            'version' => '2.1.0',
        ],
        'links_popup_script' => [
            'alt'     => '',
            'version' => '2.1.0',
        ],
        'list_authors' => [
            'alt'     => 'wp_list_authors()',
            'version' => '2.1.0',
        ],
        'list_cats' => [
            'alt'     => 'wp_list_categories()',
            'version' => '2.1.0',
        ],
        'tinymce_include' => [
            'alt'     => 'wp_editor()',
            'version' => '2.1.0',
        ],
        'wp_get_links' => [
            'alt'     => 'wp_list_bookmarks()',
            'version' => '2.1.0',
        ],
        'wp_get_linksbyname' => [
            'alt'     => 'wp_list_bookmarks()',
            'version' => '2.1.0',
        ],
        'wp_get_post_cats' => [
            'alt'     => 'wp_get_post_categories()',
            'version' => '2.1.0',
        ],
        'wp_list_cats' => [
            'alt'     => 'wp_list_categories()',
            'version' => '2.1.0',
        ],
        'wp_set_post_cats' => [
            'alt'     => 'wp_set_post_categories()',
            'version' => '2.1.0',
        ],

        // WP 2.2.0.
        'comments_rss' => [
            'alt'     => 'get_post_comments_feed_link()',
            'version' => '2.2.0',
        ],

        // WP 2.3.0.
        'permalink_single_rss' => [
            'alt'     => 'the_permalink_rss()',
            'version' => '2.3.0',
        ],

        // WP 2.5.0.
        'comments_rss_link' => [
            'alt'     => 'post_comments_feed_link()',
            'version' => '2.5.0',
        ],
        'documentation_link' => [
            'alt'     => '',
            'version' => '2.5.0',
        ],
        'get_attachment_icon' => [
            'alt'     => 'wp_get_attachment_image()',
            'version' => '2.5.0',
        ],
        'get_attachment_icon_src' => [
            'alt'     => 'wp_get_attachment_image_src()',
            'version' => '2.5.0',
        ],
        'get_attachment_innerHTML' => [
            'alt'     => 'wp_get_attachment_image()',
            'version' => '2.5.0',
        ],
        'get_author_rss_link' => [
            'alt'     => 'get_author_feed_link()',
            'version' => '2.5.0',
        ],
        'get_category_rss_link' => [
            'alt'     => 'get_category_feed_link()',
            'version' => '2.5.0',
        ],
        'get_the_attachment_link' => [
            'alt'     => 'wp_get_attachment_link()',
            'version' => '2.5.0',
        ],
        'gzip_compression' => [
            'alt'     => '',
            'version' => '2.5.0',
        ],
        'wp_clearcookie' => [
            'alt'     => 'wp_clear_auth_cookie()',
            'version' => '2.5.0',
        ],
        'wp_get_cookie_login' => [
            'alt'     => '',
            'version' => '2.5.0',
        ],
        'wp_login' => [
            'alt'     => 'wp_signon()',
            'version' => '2.5.0',
        ],
        'wp_setcookie' => [
            'alt'     => 'wp_set_auth_cookie()',
            'version' => '2.5.0',
        ],

        // WP 2.6.0.
        'dropdown_categories' => [
            'alt'     => 'wp_category_checklist()',
            'version' => '2.6.0',
        ],
        'dropdown_link_categories' => [
            'alt'     => 'wp_link_category_checklist()',
            'version' => '2.6.0',
        ],

        // WP 2.7.0.
        'get_commentdata' => [
            'alt'     => 'get_comment()',
            'version' => '2.7.0',
        ],
        // This is a method i.e. WP_Filesystem_Base::find_base_dir() See #731.
        'find_base_dir' => [
            'alt'     => 'WP_Filesystem::abspath()',
            'version' => '2.7.0',
        ],
        // This is a method i.e. WP_Filesystem_Base::get_base_dir() See #731.
        'get_base_dir' => [
            'alt'     => 'WP_Filesystem::abspath()',
            'version' => '2.7.0',
        ],

        // WP 2.8.0.
        '__ngettext' => [
            'alt'     => '_n()',
            'version' => '2.8.0',
        ],
        '__ngettext_noop' => [
            'alt'     => '_n_noop()',
            'version' => '2.8.0',
        ],
        'attribute_escape' => [
            'alt'     => 'esc_attr()',
            'version' => '2.8.0',
        ],
        'get_author_name' => [
            'alt'     => 'get_the_author_meta(\'display_name\')',
            'version' => '2.8.0',
        ],
        'get_category_children' => [
            'alt'     => 'get_term_children()',
            'version' => '2.8.0',
        ],
        'get_catname' => [
            'alt'     => 'get_cat_name()',
            'version' => '2.8.0',
        ],
        'get_the_author_aim' => [
            'alt'     => 'get_the_author_meta(\'aim\')',
            'version' => '2.8.0',
        ],
        'get_the_author_description' => [
            'alt'     => 'get_the_author_meta(\'description\')',
            'version' => '2.8.0',
        ],
        'get_the_author_email' => [
            'alt'     => 'get_the_author_meta(\'email\')',
            'version' => '2.8.0',
        ],
        'get_the_author_firstname' => [
            'alt'     => 'get_the_author_meta(\'first_name\')',
            'version' => '2.8.0',
        ],
        'get_the_author_icq' => [
            'alt'     => 'get_the_author_meta(\'icq\')',
            'version' => '2.8.0',
        ],
        'get_the_author_ID' => [
            'alt'     => 'get_the_author_meta(\'ID\')',
            'version' => '2.8.0',
        ],
        'get_the_author_lastname' => [
            'alt'     => 'get_the_author_meta(\'last_name\')',
            'version' => '2.8.0',
        ],
        'get_the_author_login' => [
            'alt'     => 'get_the_author_meta(\'login\')',
            'version' => '2.8.0',
        ],
        'get_the_author_msn' => [
            'alt'     => 'get_the_author_meta(\'msn\')',
            'version' => '2.8.0',
        ],
        'get_the_author_nickname' => [
            'alt'     => 'get_the_author_meta(\'nickname\')',
            'version' => '2.8.0',
        ],
        'get_the_author_url' => [
            'alt'     => 'get_the_author_meta(\'url\')',
            'version' => '2.8.0',
        ],
        'get_the_author_yim' => [
            'alt'     => 'get_the_author_meta(\'yim\')',
            'version' => '2.8.0',
        ],
        'js_escape' => [
            'alt'     => 'esc_js()',
            'version' => '2.8.0',
        ],
        'register_sidebar_widget' => [
            'alt'     => 'wp_register_sidebar_widget()',
            'version' => '2.8.0',
        ],
        'register_widget_control' => [
            'alt'     => 'wp_register_widget_control()',
            'version' => '2.8.0',
        ],
        'sanitize_url' => [
            'alt'     => 'esc_url_raw()',
            'version' => '2.8.0',
        ],
        'the_author_aim' => [
            'alt'     => 'the_author_meta(\'aim\')',
            'version' => '2.8.0',
        ],
        'the_author_description' => [
            'alt'     => 'the_author_meta(\'description\')',
            'version' => '2.8.0',
        ],
        'the_author_email' => [
            'alt'     => 'the_author_meta(\'email\')',
            'version' => '2.8.0',
        ],
        'the_author_firstname' => [
            'alt'     => 'the_author_meta(\'first_name\')',
            'version' => '2.8.0',
        ],
        'the_author_icq' => [
            'alt'     => 'the_author_meta(\'icq\')',
            'version' => '2.8.0',
        ],
        'the_author_ID' => [
            'alt'     => 'the_author_meta(\'ID\')',
            'version' => '2.8.0',
        ],
        'the_author_lastname' => [
            'alt'     => 'the_author_meta(\'last_name\')',
            'version' => '2.8.0',
        ],
        'the_author_login' => [
            'alt'     => 'the_author_meta(\'login\')',
            'version' => '2.8.0',
        ],
        'the_author_msn' => [
            'alt'     => 'the_author_meta(\'msn\')',
            'version' => '2.8.0',
        ],
        'the_author_nickname' => [
            'alt'     => 'the_author_meta(\'nickname\')',
            'version' => '2.8.0',
        ],
        'the_author_url' => [
            'alt'     => 'the_author_meta(\'url\')',
            'version' => '2.8.0',
        ],
        'the_author_yim' => [
            'alt'     => 'the_author_meta(\'yim\')',
            'version' => '2.8.0',
        ],
        'unregister_sidebar_widget' => [
            'alt'     => 'wp_unregister_sidebar_widget()',
            'version' => '2.8.0',
        ],
        'unregister_widget_control' => [
            'alt'     => 'wp_unregister_widget_control()',
            'version' => '2.8.0',
        ],
        'wp_specialchars' => [
            'alt'     => 'esc_html()',
            'version' => '2.8.0',
        ],

        // WP 2.9.0.
        '_c' => [
            'alt'     => '_x()',
            'version' => '2.9.0',
        ],
        '_nc' => [
            'alt'     => '_nx()',
            'version' => '2.9.0',
        ],
        'get_real_file_to_edit' => [
            'alt'     => '',
            'version' => '2.9.0',
        ],
        'make_url_footnote' => [
            'alt'     => '',
            'version' => '2.9.0',
        ],
        'the_content_rss' => [
            'alt'     => 'the_content_feed()',
            'version' => '2.9.0',
        ],
        'translate_with_context' => [
            'alt'     => '_x()',
            'version' => '2.9.0',
        ],

        // WP 3.0.0.
        'activate_sitewide_plugin' => [
            'alt'     => 'activate_plugin()',
            'version' => '3.0.0',
        ],
        'add_option_update_handler' => [
            'alt'     => 'register_setting()',
            'version' => '3.0.0',
        ],
        'automatic_feed_links' => [
            'alt'     => 'add_theme_support( \'automatic-feed-links\' )',
            'version' => '3.0.0',
        ],
        'clean_url' => [
            'alt'     => 'esc_url()',
            'version' => '3.0.0',
        ],
        'clear_global_post_cache' => [
            'alt'     => 'clean_post_cache()',
            'version' => '3.0.0',
        ],
        'codepress_footer_js' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'codepress_get_lang' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'deactivate_sitewide_plugin' => [
            'alt'     => 'deactivate_plugin()',
            'version' => '3.0.0',
        ],
        'delete_usermeta' => [
            'alt'     => 'delete_user_meta()',
            'version' => '3.0.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'funky_javascript_callback' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'funky_javascript_fix' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'generate_random_password' => [
            'alt'     => 'wp_generate_password()',
            'version' => '3.0.0',
        ],
        'get_alloptions' => [
            'alt'     => 'wp_load_alloptions()',
            'version' => '3.0.0',
        ],
        'get_blog_list' => [
            'alt'     => 'wp_get_sites()',
            'version' => '3.0.0',
        ],
        'get_most_active_blogs' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'get_profile' => [
            'alt'     => 'get_the_author_meta()',
            'version' => '3.0.0',
        ],
        'get_user_details' => [
            'alt'     => 'get_user_by()',
            'version' => '3.0.0',
        ],
        'get_usermeta' => [
            'alt'     => 'get_user_meta()',
            'version' => '3.0.0',
        ],
        'get_usernumposts' => [
            'alt'     => 'count_user_posts()',
            'version' => '3.0.0',
        ],
        'graceful_fail' => [
            'alt'     => 'wp_die()',
            'version' => '3.0.0',
        ],
        // Verified version & alternative.
        'install_blog_defaults' => [
            'alt'     => 'wp_install_defaults',
            'version' => '3.0.0',
        ],
        'is_main_blog' => [
            'alt'     => 'is_main_site()',
            'version' => '3.0.0',
        ],
        'is_site_admin' => [
            'alt'     => 'is_super_admin()',
            'version' => '3.0.0',
        ],
        'is_taxonomy' => [
            'alt'     => 'taxonomy_exists()',
            'version' => '3.0.0',
        ],
        'is_term' => [
            'alt'     => 'term_exists()',
            'version' => '3.0.0',
        ],
        'is_wpmu_sitewide_plugin' => [
            'alt'     => 'is_network_only_plugin()',
            'version' => '3.0.0',
        ],
        'mu_options' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'remove_option_update_handler' => [
            'alt'     => 'unregister_setting()',
            'version' => '3.0.0',
        ],
        'set_current_user' => [
            'alt'     => 'wp_set_current_user()',
            'version' => '3.0.0',
        ],
        'update_usermeta' => [
            'alt'     => 'update_user_meta()',
            'version' => '3.0.0',
        ],
        'use_codepress' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],
        'validate_email' => [
            'alt'     => 'is_email()',
            'version' => '3.0.0',
        ],
        'wp_dropdown_cats' => [
            'alt'     => 'wp_dropdown_categories()',
            'version' => '3.0.0',
        ],
        'wp_shrink_dimensions' => [
            'alt'     => 'wp_constrain_dimensions()',
            'version' => '3.0.0',
        ],
        'wpmu_checkAvailableSpace' => [
            'alt'     => 'is_upload_space_available()',
            'version' => '3.0.0',
        ],
        'wpmu_menu' => [
            'alt'     => '',
            'version' => '3.0.0',
        ],

        // WP 3.1.0.
        'get_author_user_ids' => [
            'alt'     => 'get_users()',
            'version' => '3.1.0',
        ],
        'get_dashboard_blog' => [
            'alt'     => 'get_site()',
            'version' => '3.1.0',
        ],
        'get_editable_authors' => [
            'alt'     => 'get_users()',
            'version' => '3.1.0',
        ],
        'get_editable_user_ids' => [
            'alt'     => 'get_users()',
            'version' => '3.1.0',
        ],
        'get_nonauthor_user_ids' => [
            'alt'     => 'get_users()',
            'version' => '3.1.0',
        ],
        'get_others_drafts' => [
            'alt'     => '',
            'version' => '3.1.0',
        ],
        'get_others_pending' => [
            'alt'     => '',
            'version' => '3.1.0',
        ],
        'get_others_unpublished_posts' => [
            'alt'     => '',
            'version' => '3.1.0',
        ],
        'get_users_of_blog' => [
            'alt'     => 'get_users()',
            'version' => '3.1.0',
        ],
        'install_themes_feature_list' => [
            'alt'     => 'get_theme_feature_list()',
            'version' => '3.1.0',
        ],
        'is_plugin_page' => [
            // Verified correct alternative.
            'alt'     => 'global $plugin_page and/or get_plugin_page_hookname() hooks',
            'version' => '3.1.0',
        ],
        'update_category_cache' => [
            'alt'     => '',
            'version' => '3.1.0',
        ],

        // WP 3.2.0.
        'favorite_actions' => [
            'alt'     => 'WP_Admin_Bar',
            'version' => '3.2.0',
        ],
        'wp_dashboard_quick_press_output' => [
            'alt'     => 'wp_dashboard_quick_press()',
            'version' => '3.2.0',
        ],
        'wp_timezone_supported' => [
            'alt'     => '',
            'version' => '3.2.0',
        ],

        // WP 3.3.0.
        'add_contextual_help' => [
            'alt'     => 'get_current_screen()->add_help_tab()',
            'version' => '3.3.0',
        ],
        'get_boundary_post_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'get_index_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'get_parent_post_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'get_user_by_email' => [
            'alt'     => 'get_user_by(\'email\')',
            'version' => '3.3.0',
        ],
        'get_user_metavalues' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'get_userdatabylogin' => [
            'alt'     => 'get_user_by(\'login\')',
            'version' => '3.3.0',
        ],
        'index_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'is_blog_user' => [
            'alt'     => 'is_user_member_of_blog()',
            'version' => '3.3.0',
        ],
        'media_upload_audio' => [
            'alt'     => 'wp_media_upload_handler()',
            'version' => '3.3.0',
        ],
        'media_upload_file' => [
            'alt'     => 'wp_media_upload_handler()',
            'version' => '3.3.0',
        ],
        'media_upload_image' => [
            'alt'     => 'wp_media_upload_handler()',
            'version' => '3.3.0',
        ],
        'media_upload_video' => [
            'alt'     => 'wp_media_upload_handler()',
            'version' => '3.3.0',
        ],
        'parent_post_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'sanitize_user_object' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'screen_layout' => [
            'alt'     => '$current_screen->render_screen_layout()',
            'version' => '3.3.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'screen_meta' => [
            'alt'     => '$current_screen->render_screen_meta()',
            'version' => '3.3.0',
        ],
        'screen_options' => [
            'alt'     => '$current_screen->render_per_page_options()',
            'version' => '3.3.0',
        ],
        'start_post_rel_link' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'the_editor' => [
            'alt'     => 'wp_editor()',
            'version' => '3.3.0',
        ],
        'type_url_form_audio' => [
            'alt'     => 'wp_media_insert_url_form(\'audio\')',
            'version' => '3.3.0',
        ],
        'type_url_form_file' => [
            'alt'     => 'wp_media_insert_url_form(\'file\')',
            'version' => '3.3.0',
        ],
        'type_url_form_image' => [
            'alt'     => 'wp_media_insert_url_form(\'image\')',
            'version' => '3.3.0',
        ],
        'type_url_form_video' => [
            'alt'     => 'wp_media_insert_url_form(\'video\')',
            'version' => '3.3.0',
        ],
        'wp_admin_bar_dashboard_view_site_menu' => [
            'alt'     => '',
            'version' => '3.3.0',
        ],
        'wp_preload_dialogs' => [
            'alt'     => 'wp_editor()',
            'version' => '3.3.0',
        ],
        'wp_print_editor_js' => [
            'alt'     => 'wp_editor()',
            'version' => '3.3.0',
        ],
        'wp_quicktags' => [
            'alt'     => 'wp_editor()',
            'version' => '3.3.0',
        ],
        'wp_tiny_mce' => [
            'alt'     => 'wp_editor()',
            'version' => '3.3.0',
        ],
        'wpmu_admin_do_redirect' => [
            'alt'     => 'wp_redirect()',
            'version' => '3.3.0',
        ],
        'wpmu_admin_redirect_add_updated_param' => [
            'alt'     => 'add_query_arg()',
            'version' => '3.3.0',
        ],

        // WP 3.4.0.
        'add_custom_background' => [
            'alt'     => 'add_theme_support( \'custom-background\', $args )',
            'version' => '3.4.0',
        ],
        'add_custom_image_header' => [
            'alt'     => 'add_theme_support( \'custom-header\', $args )',
            'version' => '3.4.0',
        ],
        'clean_page_cache' => [
            'alt'     => 'clean_post_cache()',
            'version' => '3.4.0',
        ],
        'clean_pre' => [
            'alt'     => '',
            'version' => '3.4.0',
        ],
        'current_theme_info' => [
            'alt'     => 'wp_get_theme()',
            'version' => '3.4.0',
        ],
        'debug_fclose' => [
            'alt'     => 'error_log()',
            'version' => '3.4.0',
        ],
        'debug_fopen' => [
            'alt'     => 'error_log()',
            'version' => '3.4.0',
        ],
        'debug_fwrite' => [
            'alt'     => 'error_log()',
            'version' => '3.4.0',
        ],
        'display_theme' => [
            'alt'     => '',
            'version' => '3.4.0',
        ],
        'get_allowed_themes' => [
            'alt'     => 'wp_get_themes( array( \'allowed\' => true ) )',
            'version' => '3.4.0',
        ],
        'get_broken_themes' => [
            'alt'     => 'wp_get_themes( array( \'errors\' => true )',
            'version' => '3.4.0',
        ],
        'get_current_theme' => [
            'alt'     => 'wp_get_theme()',
            'version' => '3.4.0',
        ],
        'get_site_allowed_themes' => [
            'alt'     => 'WP_Theme::get_allowed_on_network()',
            'version' => '3.4.0',
        ],
        'get_theme' => [
            'alt'     => 'wp_get_theme( $stylesheet )',
            'version' => '3.4.0',
        ],
        'get_theme_data' => [
            'alt'     => 'wp_get_theme()',
            'version' => '3.4.0',
        ],
        'get_themes' => [
            'alt'     => 'wp_get_themes()',
            'version' => '3.4.0',
        ],
        'logIO' => [
            'alt'     => 'error_log()',
            'version' => '3.4.0',
        ],
        'remove_custom_background' => [
            'alt'     => 'remove_theme_support( \'custom-background\' )',
            'version' => '3.4.0',
        ],
        'remove_custom_image_header' => [
            'alt'     => 'remove_theme_support( \'custom-header\' )',
            'version' => '3.4.0',
        ],
        'update_page_cache' => [
            'alt'     => 'update_post_cache()',
            'version' => '3.4.0',
        ],
        'wpmu_get_blog_allowedthemes' => [
            'alt'     => 'WP_Theme::get_allowed_on_site()',
            'version' => '3.4.0',
        ],

        // WP 3.4.1.
        'wp_explain_nonce' => [
            'alt'     => 'wp_nonce_ays()',
            'version' => '3.4.1',
        ],

        // WP 3.5.0.
        '_flip_image_resource' => [
            'alt'     => 'WP_Image_Editor::flip()',
            'version' => '3.5.0',
        ],
        '_get_post_ancestors' => [
            'alt'     => '',
            'version' => '3.5.0',
        ],
        '_insert_into_post_button' => [
            'alt'     => '',
            'version' => '3.5.0',
        ],
        '_media_button' => [
            'alt'     => '',
            'version' => '3.5.0',
        ],
        '_rotate_image_resource' => [
            'alt'     => 'WP_Image_Editor::rotate()',
            'version' => '3.5.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        '_save_post_hook' => [
            'alt'     => '',
            'version' => '3.5.0',
        ],
        'gd_edit_image_support' => [
            'alt'     => 'wp_image_editor_supports()',
            'version' => '3.5.0',
        ],
        'get_default_page_to_edit' => [
            'alt'     => 'get_default_post_to_edit( \'page\' )',
            'version' => '3.5.0',
        ],
        'get_post_to_edit' => [
            'alt'     => 'get_post()',
            'version' => '3.5.0',
        ],
        'get_udims' => [
            'alt'     => 'wp_constrain_dimensions()',
            'version' => '3.5.0',
        ],
        'image_resize' => [
            'alt'     => 'wp_get_image_editor()',
            'version' => '3.5.0',
        ],
        'sticky_class' => [
            'alt'     => 'post_class()',
            'version' => '3.5.0',
        ],
        'user_pass_ok' => [
            'alt'     => 'wp_authenticate()',
            'version' => '3.5.0',
        ],
        'wp_cache_reset' => [
            'alt'     => 'WP_Object_Cache::reset()',
            'version' => '3.5.0',
        ],
        'wp_create_thumbnail' => [
            'alt'     => 'image_resize()',
            'version' => '3.5.0',
        ],
        'wp_get_single_post' => [
            'alt'     => 'get_post()',
            'version' => '3.5.0',
        ],
        'wp_load_image' => [
            'alt'     => 'wp_get_image_editor()',
            'version' => '3.5.0',
        ],

        // WP 3.6.0.
        'get_user_id_from_string' => [
            'alt'     => 'get_user_by()',
            'version' => '3.6.0',
        ],
        'wp_convert_bytes_to_hr' => [
            'alt'     => 'size_format()',
            'version' => '3.6.0',
        ],
        'wp_nav_menu_locations_meta_box' => [
            'alt'     => '',
            'version' => '3.6.0',
        ],

        // WP 3.7.0.
        '_search_terms_tidy' => [
            'alt'     => '',
            'version' => '3.7.0',
        ],
        'get_blogaddress_by_domain' => [
            'alt'     => '',
            'version' => '3.7.0',
        ],
        'the_attachment_links' => [
            'alt'     => '',
            'version' => '3.7.0',
        ],
        'wp_update_core' => [
            'alt'     => 'new Core_Upgrader();',
            'version' => '3.7.0',
        ],
        'wp_update_plugin' => [
            'alt'     => 'new Plugin_Upgrader();',
            'version' => '3.7.0',
        ],
        'wp_update_theme' => [
            'alt'     => 'new Theme_Upgrader();',
            'version' => '3.7.0',
        ],

        // WP 3.8.0.
        'get_screen_icon' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        'screen_icon' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_incoming_links' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_incoming_links_control' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_incoming_links_output' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_plugins' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_primary_control' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_recent_comments_control' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_secondary' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_secondary_control' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_dashboard_secondary_output' => [
            'alt'     => '',
            'version' => '3.8.0',
        ],

        // WP 3.9.0.
        '_relocate_children' => [
            'alt'     => '',
            'version' => '3.9.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'default_topic_count_text' => [
            'alt'     => '',
            'version' => '3.9.0',
        ],
        'format_to_post' => [
            'alt'     => '',
            'version' => '3.9.0',
        ],
        'get_current_site_name' => [
            'alt'     => 'get_current_site()',
            'version' => '3.9.0',
        ],
        'rich_edit_exists' => [
            'alt'     => '',
            'version' => '3.9.0',
        ],
        'wpmu_current_site' => [
            'alt'     => '',
            'version' => '3.9.0',
        ],

        // WP 4.0.0.
        'get_all_category_ids' => [
            'alt'     => 'get_terms()',
            'version' => '4.0.0',
        ],
        'like_escape' => [
            'alt'     => 'wpdb::esc_like()',
            'version' => '4.0.0',
        ],
        'url_is_accessable_via_ssl' => [
            'alt'     => '',
            'version' => '4.0.0',
        ],

        // WP 4.1.0.
        // This is a method from the WP_Customize_Image_Control class. See #731.
        'add_tab' => [
            'alt'     => '',
            'version' => '4.1.0',
        ],
        // This is a method from the WP_Customize_Image_Control class. See #731.
        'prepare_control' => [
            'alt'     => '',
            'version' => '4.1.0',
        ],
        // This is a method from the WP_Customize_Image_Control class. See #731.
        'print_tab_image' => [
            'alt'     => '',
            'version' => '4.1.0',
        ],
        // This is a method from the WP_Customize_Image_Control class. See #731.
        'remove_tab' => [
            'alt'     => '',
            'version' => '4.1.0',
        ],

        // WP 4.2.0.
        // This is a method from the WP_Customize_Widgets class. See #731.
        'prepreview_added_sidebars_widgets' => [
            'alt'     => 'the \'customize_dynamic_setting_args\' filter',
            'version' => '4.2.0',
        ],
        // This is a method from the WP_Customize_Widgets class. See #731.
        'prepreview_added_widget_instance' => [
            'alt'     => 'the \'customize_dynamic_setting_args\' filter',
            'version' => '4.2.0',
        ],
        // This is a method from the WP_Customize_Widgets class. See #731.
        'remove_prepreview_filters' => [
            'alt'     => 'the \'customize_dynamic_setting_args\' filter',
            'version' => '4.2.0',
        ],
        // This is a method from the WP_Customize_Widgets class. See #731.
        'setup_widget_addition_previews' => [
            'alt'     => 'the \'customize_dynamic_setting_args\' filter',
            'version' => '4.2.0',
        ],

        // WP 4.3.0.
        '_preview_theme_stylesheet_filter' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        '_preview_theme_template_filter' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        'preview_theme' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        'preview_theme_ob_filter' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        'preview_theme_ob_filter_callback' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        // Verified; see https://core.trac.wordpress.org/ticket/41121, patch 3.
        'wp_ajax_wp_fullscreen_save_post' => [
            'alt'     => '',
            'version' => '4.3.0',
        ],
        'wp_htmledit_pre' => [
            'alt'     => 'format_for_editor()',
            'version' => '4.3.0',
        ],
        'wp_richedit_pre' => [
            'alt'     => 'format_for_editor()',
            'version' => '4.3.0',
        ],

        // WP 4.4.0.
        'create_empty_blog' => [
            'alt'     => '',
            'version' => '4.4.0',
        ],
        'force_ssl_login' => [
            'alt'     => 'force_ssl_admin()',
            'version' => '4.4.0',
        ],
        'get_admin_users_for_domain' => [
            'alt'     => '',
            'version' => '4.4.0',
        ],
        'post_permalink' => [
            'alt'     => 'get_permalink()',
            'version' => '4.4.0',
        ],
        'wp_get_http' => [
            'alt'     => 'the WP_Http class',
            'version' => '4.4.0',
        ],
        // This is a method i.e. WP_Widget_Recent_Comments::flush_widget_cache() See #731.
        'flush_widget_cache' => [
            'alt'     => '',
            'version' => '4.4.0',
        ],

        // WP 4.5.0.
        'add_object_page' => [
            'alt'     => 'add_menu_page()',
            'version' => '4.5.0',
        ],
        'add_utility_page' => [
            'alt'     => 'add_menu_page()',
            'version' => '4.5.0',
        ],
        'comments_popup_script' => [
            'alt'     => '',
            'version' => '4.5.0',
        ],
        'get_comments_popup_template' => [
            'alt'     => '',
            'version' => '4.5.0',
        ],
        'get_currentuserinfo' => [
            'alt'     => 'wp_get_current_user()',
            'version' => '4.5.0',
        ],
        'is_comments_popup' => [
            'alt'     => '',
            'version' => '4.5.0',
        ],
        'popuplinks' => [
            'alt'     => '',
            'version' => '4.5.0',
        ],

        // WP 4.6.0.
        'post_form_autocomplete_off' => [
            'alt'     => '',
            'version' => '4.6.0',
        ],
        'wp_embed_handler_googlevideo' => [
            'alt'     => '',
            'version' => '4.6.0',
        ],
        'wp_get_sites' => [
            'alt'     => 'get_sites()',
            'version' => '4.6.0',
        ],

        // WP 4.7.0.
        '_sort_nav_menu_items' => [
            'alt'     => 'wp_list_sort()',
            'version' => '4.7.0',
        ],
        '_usort_terms_by_ID' => [
            'alt'     => 'wp_list_sort()',
            'version' => '4.7.0',
        ],
        '_usort_terms_by_name' => [
            'alt'     => 'wp_list_sort()',
            'version' => '4.7.0',
        ],
        'get_paged_template' => [
            'alt'     => '',
            'version' => '4.7.0',
        ],
        'wp_get_network' => [
            'alt'     => 'get_network()',
            'version' => '4.7.0',
        ],
        'wp_kses_js_entities' => [
            'alt'     => '',
            'version' => '4.7.0',
        ],

        // WP 4.8.0.
        'wp_dashboard_plugins_output' => [
            'alt'     => '',
            'version' => '4.8.0',
        ],

        // WP 4.9.0.
        'get_shortcut_link' => [
            'alt'     => '',
            'version' => '4.9.0',
        ],
        'is_user_option_local' => [
            'alt'     => '',
            'version' => '4.9.0',
        ],
        'wp_ajax_press_this_add_category' => [
            'alt'     => '',
            'version' => '4.9.0',
        ],
        'wp_ajax_press_this_save_post' => [
            'alt'     => '',
            'version' => '4.9.0',
        ],

        // WP 5.1.0.
        'insert_blog' => [
            'alt'     => 'wp_insert_site()',
            'version' => '5.1.0',
        ],
        'install_blog' => [
            'alt'     => '',
            'version' => '5.1.0',
        ],

        // WP 5.3.0.
        '_wp_json_prepare_data' => [
            'alt'     => '',
            'version' => '5.3.0',
        ],
        '_wp_privacy_requests_screen_options' => [
            'alt'     => '',
            'version' => '5.3.0',
        ],
        'update_user_status' => [
            'alt'     => 'wp_update_user()',
            'version' => '5.3.0',
        ],

        // WP 5.4.0.
        'wp_get_user_request_data' => [
            'alt'     => 'wp_get_user_request()',
            'version' => '5.4.0',
        ],
    ];

    /**
     * Groups of functions to restrict.
     *
     * @return array
     */
    public function getGroups()
    {
        // Make sure all array keys are lowercase.
        $this->deprecated_functions = array_change_key_case($this->deprecated_functions, CASE_LOWER);

        return [
            'deprecated_functions' => [
                'functions' => array_keys($this->deprecated_functions),
            ],
        ];
    }

    /**
     * Process a matched token.
     *
     * @param int    $stackPtr        The position of the current token in the stack.
     * @param string $group_name      The name of the group which was matched. Will
     *                                always be 'deprecated_functions'.
     * @param string $matched_content The token content (function name) which was matched.
     *
     * @return void
     */
    public function process_matched_token($stackPtr, $group_name, $matched_content)
    {
        $this->get_wp_version_from_cl();

        $function_name = strtolower($matched_content);

        $message = '%s() has been deprecated since WordPress version %s.';
        $data = [
            $matched_content,
            $this->deprecated_functions[$function_name]['version'],
        ];

        if (! empty($this->deprecated_functions[$function_name]['alt'])) {
            $message .= ' Use %s instead.';
            $data[] = $this->deprecated_functions[$function_name]['alt'];
        }

        $this->addMessage(
            $message,
            $stackPtr,
            (version_compare($this->deprecated_functions[$function_name]['version'], $this->minimum_supported_version, '<')),
            $this->string_to_errorcode($matched_content.'Found'),
            $data
        );
    }
}
