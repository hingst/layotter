<?php

/**
 * Class Layotter_Editable_Model
 */
class Layotter_Editable_Model {

    /**
     * @return array
     */
    private static function get_labels() {
        return array(
            'name'               => _x('Layotter Editables', 'post type general name', 'layotter'),
            'singular_name'      => _x('Layotter Editable', 'post type singular name', 'layotter'),
            'menu_name'          => _x('Layotter Editables', 'admin menu', 'layotter'),
            'name_admin_bar'     => _x('Layotter Editable', 'add new on admin bar', 'layotter'),
            'add_new'            => _x('Add New', 'Layotter Editable', 'layotter'),
            'add_new_item'       => __('Add New Layotter Editable', 'layotter'),
            'new_item'           => __('New Layotter Editable', 'layotter'),
            'edit_item'          => __('Edit Layotter Editable', 'layotter'),
            'view_item'          => __('View Layotter Editable', 'layotter'),
            'all_items'          => __('All Layotter Editables', 'layotter'),
            'search_items'       => __('Search Layotter Editables', 'layotter'),
            'parent_item_colon'  => __('Belongs to Post:', 'layotter'),
            'not_found'          => __('No Layotter Editables found.', 'layotter'),
            'not_found_in_trash' => __('No Layotter Editables found in Trash.', 'layotter'),
        );
    }

    /**
     * Register underlying post_type for Layotter_Editable
     */
    public static function register_post_types() {
        $labels = self::get_labels();

        $args = array(
            'labels'             => $labels,
            'description'        => __('CPT for Layotter Editables.', 'layotter'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => 99999,
            'supports'           => array('title'),
            'capabilities' => array(
                'create_posts'   => 'do_not_allow',
                'delete_post'    => 'do_not_allow',
                'publish_posts'  => 'do_not_allow',
            ),
            'map_meta_cap' => true,
        );

        register_post_type('layotter_editable', $args);
    }

    /**
     *
     */
    public static function add_to_menu() {
        if(Layotter_Settings::is_debug_mode_enabled()) {
            $labels = self::get_labels();
            add_submenu_page(
                'layotter-settings',
                $labels['name'],
                $labels['menu_name'],
                'manage_options',
                'edit.php?post_type=layotter_editable'
            );
        }
    }
}