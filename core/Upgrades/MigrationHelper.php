<?php

namespace Layotter\Upgrades;

use Layotter\Core;
use Layotter\Views\UpgradeNotice;
use Layotter\Views\Upgrader;

class MigrationHelper {

    const UPGRADE_OPTION = 'layotter_needs_upgrade';
    const META_FIELD_MODEL_VERSION = 'layotter_model_version';
    const CURRENT_MODEL_VERSION = '2.0.0';

    /**
     * MigrationHelper constructor.
     */
    public function __construct() {
        if ($this->needs_upgrade()) {
            # @TODO: the magic!
        }
    }

    /**
     * @return bool
     */
    public static function needs_upgrade() {
        // TODO: implement
        return false;


        $needs_upgrade = \get_option(self::UPGRADE_OPTION);

        if (ctype_digit($needs_upgrade)) {
            return (bool)$needs_upgrade;
        }

        $has_upgradable_posts = (bool)self::count_upgradable_posts();

        if ($has_upgradable_posts) {
            \update_option(self::UPGRADE_OPTION, 1);
        }

        return true;
    }

    /**
     * @return int
     */
    private static function count_upgradable_posts() {
        $upgradable_posts = self::get_upgradable_post_ids();

        return count($upgradable_posts);
    }

    /**
     * @return array|null|object
     */
    public static function get_upgradable_post_ids() {
        global $wpdb;
        $q = "SELECT ID
              FROM {$wpdb->prefix}posts
              WHERE post_content LIKE '[layotter%[/layotter]'
              AND ID NOT IN ( SELECT post_id
                              FROM {$wpdb->prefix}postmeta
                              WHERE meta_key = '" . self::META_FIELD_MODEL_VERSION . "'
                              AND meta_value = '" . self::CURRENT_MODEL_VERSION . "' )";

        $flatten = function($value) {
            return reset($value);
        };

        return array_map($flatten, $wpdb->get_results($q, ARRAY_N));
    }


    public function migrate_all_layouts() {
        $layouts = get_option('layotter_post_layouts');

        foreach ($layouts as $id => $layout) {
            $lm = new LayoutMigrator($id);
            $lm->migrate();
        }
    }

    public function migrate_all_templates() {
        $templates = get_option('layotter_element_templates');

        foreach ($templates as $id => $template) {
            $tm = new TemplateMigrator($id);
            $tm->migrate();
        }
    }

    /**
     * Create an admin menu entry for Layotter
     */
    public static function admin_menu() {
        add_submenu_page(
            'layotter-settings',
            __('Layotter Database Upgrade', 'layotter'), // title
            __('Database Upgrade', 'layotter'), // menu name
            'activate_plugins', // capability
            'layotter-upgrade', // page name
            array(__CLASS__, 'upgrade_page') // callback
        );
    }

    public static function upgrade_page() {
        Upgrader::view();
    }

    public static function show_upgrade_prompt() {
        add_action('admin_notices', array(__CLASS__, 'print_error'));
        add_action('admin_head', array(__CLASS__, 'hook_editor'));
        add_action('admin_head', array(__CLASS__, 'assets'));
        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
    }

    public static function assets() {
        wp_enqueue_style('layotter', plugins_url('assets/css/editor.css', __DIR__ . '/../../..'));
        wp_enqueue_style('layotter-font-awesome', plugins_url('assets/css/font-awesome.min.css', __DIR__ . '/../../..'));
        wp_enqueue_script('layotter-upgrades', plugins_url('assets/js/upgrades.js', __DIR__ . '/../../..'), array('jquery'));
    }

    /**
     * Output error message
     */
    public static function print_error() {
        $current_screen = get_current_screen();
        $page = $current_screen->base;
        if ($page == 'layotter_page_layotter-upgrade') {
            return;
        }

        ?>
        <div class="error">
            <p>
                <?php _e('A database upgrade is required to continue using Layotter.', 'layotter'); ?>
                <?php
                if (current_user_can('activate_plugins')) {
                    ?>
                    <a href="<?php echo admin_url('admin.php?page=layotter-upgrade'); ?>"><?php _e('Go to the upgrade page', 'layotter');  ?></a>
                    <?php
                } else {
                    _e('Please ask the site admin to run the upgrade.', 'layotter');
                }
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Replace TinyMCE with Layotter on Layotter-enabled screens
     */
    public static function hook_editor() {
        if (!Core::is_enabled()) {
            return;
        }

        $post_type = get_post_type();

        // remove TinyMCE
        remove_post_type_support($post_type, 'editor');

        // insert layotter
        add_meta_box('layotter_wrapper', // ID
            'Layotter', // title
            array(__CLASS__, 'output_editor'), // callback
            $post_type, // post type for which to enable
            'normal', // position
            'high' // priority
        );
    }

    /**
     * Output backend HTML for Layotter
     *
     * @param $post object Post object as provided by Wordpress
     */
    public static function output_editor($post) {
        UpgradeNotice::view();
    }

}