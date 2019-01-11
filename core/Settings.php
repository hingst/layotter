<?php

namespace Layotter;

use Layotter\Acf\Adapter;

/**
 * This shameful piece of spaghetti madness creates a settings page and sets default
 * settings on plugin activation. Completely self-contained.
 */
class Settings {

    private static $last_edited_tab;
    private static $current_settings;
    private static $default_settings;
    private static $col_class_translations;
    private static $docs_view_filters_link = 'http://docs.layotter.com/filters/view-filters/';
    private static $docs_settings_filters_link = 'http://docs.layotter.com/filters/settings-filters/';

    /**
     * Register hooks and declare default settings
     */
    public static function init() {
        // do stuff on plugin activation
        register_activation_hook(dirname(__DIR__) . '/index.php', [__CLASS__, 'set_defaults_on_activation']);
        add_action('wpmu_new_blog', [__CLASS__, 'set_defaults_on_new_site']);

        // use the following line FOR DEBUGGING ONLY
        // REMOVES ALL SETTINGS on plugin deactivation!
        // register_deactivation_hook(dirname(__DIR__) . '/index.php', [__CLASS__, 'remove_all_settings']);

        // translate labels on admin_init - otherwise translations wouldn't be available yet
        add_action('admin_init', [__CLASS__, 'translate_labels']);

        // enable settings page only if we're in the backend
        if (is_admin()) {
            // register settings, create menu entry, load assets
            add_action('admin_init', [__CLASS__, 'admin_init']);
            add_action('admin_menu', [__CLASS__, 'admin_menu']);
            add_action('admin_head', [__CLASS__, 'admin_head']);
        }

        self::$default_settings = [
            'general' => [
                'enable_for' => [
                    'page' => '1'
                ],
                'enable_default_element_type' => '1',
                'enable_post_layouts' => '1',
                'enable_element_templates' => '1',
                'enable_default_css' => '1',
                'enable_example_element' => '1',
                'debug_mode' => [
                    'administrator' => '0'
                ]
            ],
            'wrapper' => [
                'html_before' => '<div class="lo-wrapper">',
                'html_after' => '</div>'
            ],
            'rows' => [
                'html_before' => '<div class="lo-row">',
                'html_after' => '</div>',
                'allow' => [
                    '1/1' => '1',
                    '1/2 1/2' => '1',
                    '1/3 1/3 1/3' => '1',
                    '1/3 2/3' => '1',
                    '2/3 1/3' => '1',
                    '1/4 1/4 1/4 1/4' => '0',
                    '3/4 1/4' => '0',
                    '1/4 3/4' => '0',
                ],
                'default_layout' => '1/3 1/3 1/3'
            ],
            'cols' => [
                'html_before' => '<div class="%%CLASS%%">',
                'html_after' => '</div>',
                'classes' => [
                    '1/12' => 'lo-col-size1of12',
                    '1/6' => 'lo-col-size2of12',
                    '1/4' => 'lo-col-size3of12',
                    '1/3' => 'lo-col-size4of12',
                    '5/12' => 'lo-col-size5of12',
                    '1/2' => 'lo-col-size6of12',
                    '7/12' => 'lo-col-size7of12',
                    '2/3' => 'lo-col-size8of12',
                    '3/4' => 'lo-col-size9of12',
                    '5/6' => 'lo-col-size10of12',
                    '11/12' => 'lo-col-size11of12',
                    '1/1' => 'lo-col-size12of12'
                ],
            ],
            'elements' => [
                'html_before' => '<div class="lo-element">',
                'html_after' => '</div>'
            ]
        ];
    }

    /**
     * Translations get their own method because Wordpress' translation service isn't ready at init()
     */
    public static function translate_labels() {
        self::$col_class_translations = [
            '1/1' => __('Full width', 'layotter'),
            '1/2' => __('A half', 'layotter'),
            '1/3' => __('A third', 'layotter'),
            '2/3' => __('Two thirds', 'layotter'),
            '1/4' => __('A fourth', 'layotter'),
            '3/4' => __('Three fourths', 'layotter'),
            '1/6' => __('A sixth', 'layotter'),
            '5/6' => __('Five sixths', 'layotter'),
            '1/12' => __('A twelfth', 'layotter'),
            '5/12' => __('Five twelfths', 'layotter'),
            '7/12' => __('Seven twelfths', 'layotter'),
            '11/12' => __('Eleven twelfths', 'layotter')
        ];
    }

    /**
     * Get settings for a specific category, just a simple shortcut
     *
     * @param string $category Must be general, wrapper, rows, cols, elements, or empty for alle settings
     * @return array All settings for the requested category
     */
    private static function get_settings($category = '') {
        $settings = get_option('layotter_settings');
        if (isset($settings[ $category ])) {
            return $settings[ $category ];
        } else {
            return $settings;
        }
    }

    /**
     * Get a list of all post types that Layotter is enabled for
     *
     * @return array Enabled post types
     */
    public static function get_enabled_post_types() {
        $enabled_post_types = [];

        $settings = self::get_settings('general');
        if (isset($settings['enable_for']) && is_array($settings['enable_for'])) {
            foreach ($settings['enable_for'] as $post_type => $enabled) {
                if ($enabled == '1') {
                    $enabled_post_types[] = $post_type;
                }
            }
        }

        return apply_filters('layotter/enabled_post_types', $enabled_post_types);
    }

    /**
     * Check if the post layouts feature is enabled
     *
     * @return bool
     */
    public static function post_layouts_enabled() {
        $settings = self::get_settings('general');
        $is_enabled = (isset($settings['enable_post_layouts']) && $settings['enable_post_layouts'] == '1');
        return apply_filters('layotter/enable_post_layouts', $is_enabled);
    }

    /**
     * Check if the element templates feature is enabled
     *
     * @return bool
     */
    public static function element_templates_enabled() {
        $settings = self::get_settings('general');
        $is_enabled = (isset($settings['enable_element_templates']) && $settings['enable_element_templates'] == '1');
        return apply_filters('layotter/enable_element_templates', $is_enabled);
    }

    /**
     * Check if basic frontend styles are enabled
     *
     * @return bool
     */
    public static function default_css_enabled() {
        $settings = self::get_settings('general');
        $is_enabled = (isset($settings['enable_default_css']) && $settings['enable_default_css'] == '1');
        return apply_filters('layotter/enable_default_css', $is_enabled);
    }

    /**
     * Check if the example element is enabled
     *
     * @return bool
     */
    public static function example_element_enabled() {
        $settings = self::get_settings('general');
        $is_enabled = (isset($settings['enable_example_element']) && $settings['enable_example_element'] == '1');
        return apply_filters('layotter/enable_example_element', $is_enabled);
    }

    /**
     * Check if debug mode is enabled for the current user
     *
     * @return bool
     */
    public static function is_debug_mode_enabled() {
        // fetch roles that debug mode is enabled for
        $enabled_for_roles = [];
        $settings = self::get_settings('general');
        if (isset($settings['debug_mode']) && is_array($settings['debug_mode'])) {
            foreach ($settings['debug_mode'] as $role => $enabled) {
                if ($enabled == '1') {
                    $enabled_for_roles[] = $role;
                }
            }
        }

        // allow filter customization
        $enabled_for_roles = apply_filters('layotter/debug_mode_roles', $enabled_for_roles);

        // check if current user's roles contain a role that debug mode is enabled for
        $current_user = wp_get_current_user();
        $current_user_roles = $current_user->roles;
        foreach ($current_user_roles as $role) {
            if (in_array($role, $enabled_for_roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a list of all allowed row layouts
     *
     * @return array Row layouts, e.g. ['1/1', '1/3 1/3 1/3']
     */
    public static function get_allowed_row_layouts() {
        $allowed_layouts = [];

        $settings = self::get_settings('rows');
        if (isset($settings['allow']) && is_array($settings['allow'])) {
            foreach ($settings['allow'] as $layout => $allowed) {
                if ($allowed == '1') {
                    $allowed_layouts[] = $layout;
                }
            }
        }

        return array_values(apply_filters('layotter/rows/allowed_layouts', $allowed_layouts));
    }

    /**
     * Get the default row layout
     *
     * @return string Row layout, e.g. '2/3 1/3'
     */
    public static function get_default_row_layout() {
        $settings = self::get_settings('rows');
        $default_layout = isset($settings['default_layout']) ? $settings['default_layout'] : '';
        return apply_filters('layotter/rows/default_layout', $default_layout);
    }

    /**
     * Take a col layout and return user-provided class
     *
     * @param string $layout Col layout string (e.g. '1/2', '1/3')
     * @return string CSS class for the col layout, as provided by the user
     */
    public static function get_col_layout_class($layout) {
        $settings = self::get_settings('cols');
        $classes = (isset($settings['classes']) && is_array($settings['classes'])) ? $settings['classes'] : [];
        $classes = apply_filters('layotter/columns/classes', $classes);
        return $classes[ $layout ];
    }

    /**
     * Get the HTML wrapper for posts, rows, columns or elements
     *
     * @param string $type 'wrapper', 'rows', 'cols', 'elements'
     * @return array Contains two keys: 'before' and 'after', each containing an HTML string
     */
    public static function get_html_wrapper($type) {
        $settings = self::get_settings($type);
        return [
            'before' => $settings['html_before'],
            'after' => $settings['html_after']
        ];
    }

    /**
     * Set default settings on plugin activation
     */
    public static function set_defaults_on_activation() {
        // add_option makes sure existing settings will not be overwritten (as opposed to update_option)
        add_option('layotter_settings', self::$default_settings);
    }

    /**
     * Set default settings when creating a new site in a multi-site environment
     *
     * @param int $new_site_id ID of the newly created site
     */
    public static function set_defaults_on_new_site($new_site_id) {
        switch_to_blog($new_site_id);
        self::set_defaults_on_activation();
        restore_current_blog();
    }

    /**
     * Remove all settings on plugin deactivation
     * For debugging only!
     */
    public static function remove_all_settings() {
        delete_option('layotter_settings');
    }

    /**
     * Register settings group
     */
    public static function admin_init() {
        register_setting('layotter_settings', 'layotter_settings');
    }

    /**
     * Load scripts and styles and show message if settings have just been saved
     */
    public static function admin_head() {
        // load assets only if necessary
        $current_screen = get_current_screen();
        if ($current_screen->id !== 'toplevel_page_layotter-settings') {
            return;
        }

        wp_enqueue_script('layotter-settings', plugins_url('assets/js/settings.js', __DIR__), ['jquery']);
        wp_enqueue_style('layotter', plugins_url('assets/css/editor.css', __DIR__));
        wp_enqueue_style('layotter-font-awesome', plugins_url('assets/css/font-awesome.min.css', __DIR__));

        // display notice if settings have been saved
        if (isset($_GET['settings-updated'])) {
            add_action('admin_notices', [__CLASS__, 'settings_saved_notice']);
        }
    }

    /**
     * Callback to display "settings saved" notice
     */
    public static function settings_saved_notice() {
        ?>
        <div class="updated" id="layotter-settings-saved-notice">
            <p><?php _e('Your settings have been saved.', 'layotter'); ?></p>
        </div>
        <?php
    }

    /**
     * Create an admin menu entry for Layotter
     */
    public static function admin_menu() {
        add_menu_page(__('Layotter Settings', 'layotter'), // title
            'Layotter', // menu name
            'activate_plugins', // capability
            'layotter-settings', // page name
            [__CLASS__, 'settings_page'], // callback
            'dashicons-tagcloud', // icon
            null // position
        );
    }

    /**
     * Output HTML for the settings page
     */
    public static function settings_page() {
        self::$current_settings = self::get_settings();

        self::$last_edited_tab = '#layotter-settings-general';
        if (isset($_GET['settings-updated']) && isset(self::$current_settings['internal']['last_edited_tab']) && !empty(self::$current_settings['internal']['last_edited_tab'])) {
            self::$last_edited_tab = self::$current_settings['internal']['last_edited_tab'];
        }

        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Layotter Settings', 'layotter'); ?></h2>
            <h2 class="nav-tab-wrapper">
                <a href="#layotter-settings-general"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-general' ? ' nav-tab-active' : ''; ?>"><?php _e('General', 'layotter'); ?></a>
                <a href="#layotter-settings-wrapper"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-wrapper' ? ' nav-tab-active' : ''; ?>"><?php _e('Wrapper', 'layotter'); ?></a>
                <a href="#layotter-settings-rows"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-rows' ? ' nav-tab-active' : ''; ?>"><?php _e('Rows', 'layotter'); ?></a>
                <a href="#layotter-settings-cols"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-cols' ? ' nav-tab-active' : ''; ?>"><?php _e('Columns', 'layotter'); ?></a>
                <a href="#layotter-settings-elements"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-elements' ? ' nav-tab-active' : ''; ?>"><?php _e('Elements', 'layotter'); ?></a>
                <a href="#layotter-settings-debug"
                   class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-debug' ? ' nav-tab-active' : ''; ?>"><?php _e('Debug', 'layotter'); ?></a>
            </h2>
            <form action="options.php" method="post">
                <?php

                // to keep things clean, each form has its own method.
                // settings_fields() creates a nonce and other necessary form elements
                // Wordpress takes care of saving form data, which is nice

                settings_fields('layotter_settings');

                self::settings_general();
                self::settings_wrapper();
                self::settings_rows();
                self::settings_cols();
                self::settings_elements();
                self::settings_debug();

                ?>
                <input type="hidden" id="layotter-last-edited-tab" name="layotter_settings[internal][last_edited_tab]"
                       value="<?php echo self::$last_edited_tab; ?>">
            </form>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for general settings
     */
    public static function settings_general() {
        // first, get current settings
        if (isset(self::$current_settings['general'])) {
            $settings = self::$current_settings['general'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-general"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-general' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('Post types', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Select the post types for which Layotter should be enabled.', 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/enabled_post_types')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/enabled_post_types</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <div class="layotter-settings-checkboxes">
                <p>
                    <label>
                        <input type="checkbox" name="layotter_settings[general][enable_for][post]"
                               value="1" <?php if (isset($settings['enable_for']['post'])) {
                            checked($settings['enable_for']['post']);
                        } ?>>
                        <?php _e('Posts', 'layotter'); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="layotter_settings[general][enable_for][page]"
                               value="1" <?php if (isset($settings['enable_for']['page'])) {
                            checked($settings['enable_for']['page']);
                        } ?>>
                        <?php _e('Pages', 'layotter'); ?>
                    </label>
                </p>
                <?php

                $field_group_post_type = Adapter::get_field_group_post_type();

                $post_types = get_post_types([
                    '_builtin' => false,
                    'show_ui' => true
                ], 'objects');

                foreach ($post_types as $post_type) {

                    // exclude ACF field groups
                    if ($post_type->name == $field_group_post_type) {
                        continue;
                    }

                    ?>
                    <p>
                        <label>
                            <input type="checkbox"
                                   name="layotter_settings[general][enable_for][<?php echo $post_type->name; ?>]"
                                   value="1" <?php if (isset($settings['enable_for'][ $post_type->name ])) {
                                checked($settings['enable_for'][ $post_type->name ]);
                            } ?>>
                            <?php echo $post_type->label; ?>
                        </label>
                    </p>
                    <?php

                }
                ?>
            </div>
            <h3>
                <?php _e('Post layouts', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('This feature lets you save entire post layouts as templates to be used as a starting point for new posts.', 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/enable_post_layouts')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/enable_post_layouts</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_post_layouts]"
                           value="1" <?php if (isset($settings['enable_post_layouts'])) {
                        checked($settings['enable_post_layouts']);
                    } ?>>
                    <?php _e('Enable post layouts', 'layotter'); ?>
                </label>
            </p>
            <h3>
                <?php _e('Element templates', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e("This feature lets you save elements as templates. Element templates look just like regular elements, but if you edit them on one page, they'll be updated on every other page as well.", 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/enable_element_templates')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/enable_element_templates</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_element_templates]"
                           value="1" <?php if (isset($settings['enable_element_templates'])) {
                        checked($settings['enable_element_templates']);
                    } ?>>
                    <?php _e('Enable element templates', 'layotter'); ?>
                </label>
            </p>
            <h3>
                <?php _e('Default frontend CSS', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e("Layotter comes with a set of very basic styles to make rows and columns work in the frontend (as long as you don't change the default class names). Disable this option if you want to write your own styles.", 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/enable_default_css')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/enable_default_css</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_default_css]"
                           value="1" <?php if (isset($settings['enable_default_css'])) {
                        checked($settings['enable_default_css']);
                    } ?>>
                    <?php _e('Use default frontend CSS', 'layotter'); ?>
                </label>
            </p>
            <h3>
                <?php _e('Example element type', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e("Layotter comes with an example element type to play around and get started quickly. You'll probably want to disable it once you start creating your own element types.", 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/enable_example_element')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/enable_example_element</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_example_element]"
                           value="1" <?php if (isset($settings['enable_example_element'])) {
                        checked($settings['enable_example_element']);
                    } ?>>
                    <?php _e('Use example element type', 'layotter'); ?>
                </label>
            </p>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for wrapper settings
     */
    public static function settings_wrapper() {
        // first, get current settings
        if (isset(self::$current_settings['wrapper'])) {
            $settings = self::$current_settings['wrapper'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-wrapper"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-wrapper' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('HTML wrapper', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Enter HTML code to wrap around the whole content.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <?php
                $has_filter = false;
                if (has_filter('layotter/view/post')) {
                    $has_filter = true;
                    ?>
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("These settings currently have no effect because they're overwritten by a %s filter used in your code.", 'layotter'), '<code>layotter/view/post</code>');
                } else {
                    ?>
                    <i class="fa fa-info-circle"></i>
                    <?php
                    printf(__('You can use <a href="%s" target="_blank">view filters</a> for way more flexibility.', 'layotter'), self::$docs_view_filters_link);
                }
                ?>
            </p>
            <?php
            if ($has_filter) {
            ?>
            <div class="layotter-shaded">
                <?php
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before content:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div class="lo-wrapper"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[wrapper][html_before]" rows="4"
                                      cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after content:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[wrapper][html_after]" rows="4"
                                      cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                if ($has_filter) {
                ?>
            </div>
        <?php
        }
        submit_button(__('Save settings', 'layotter'));
        ?>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for row settings
     */
    public static function settings_rows() {
        // first, get current settings
        if (isset(self::$current_settings['rows'])) {
            $settings = self::$current_settings['rows'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-rows"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-rows' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('HTML wrapper', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Enter HTML code to wrap around each row.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <?php
                $has_filter = false;
                if (has_filter('layotter/view/row')) {
                    $has_filter = true;
                    ?>
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("These settings currently have no effect because they're overwritten by a %s filter used in your code.", 'layotter'), '<code>layotter/view/row</code>');
                } else {
                    ?>
                    <i class="fa fa-info-circle"></i>
                    <?php
                    printf(__('You can use <a href="%s" target="_blank">view filters</a> for way more flexibility.', 'layotter'), self::$docs_view_filters_link);
                }
                ?>
            </p>
            <?php
            if ($has_filter) {
            ?>
            <div class="layotter-shaded">
                <?php
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each row:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div
                                    class="lo-row"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[rows][html_before]" rows="4"
                                      cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after each row:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[rows][html_after]" rows="4"
                                      cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                if ($has_filter) {
                ?>
            </div>
        <?php
        }
        ?>
            <h3>
                <?php _e('Allowed layouts', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Choose the row layouts you want to use. Disabling a layout means it will not be available for newly created rows from now on &ndash; existing rows with that layout will stay the way they are until you change them by hand.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <?php
                if (has_filter('layotter/rows/allowed_layouts')) {
                    ?>
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/allowed_layouts</code>');
                } else {
                    ?>
                    <i class="fa fa-info-circle"></i>
                    <?php
                    printf(__('You can create more complex layouts using <a href="%s" target="_blank">settings filters</a>.', 'layotter'), self::$docs_settings_filters_link);
                }
                ?>
            </p>
            <fieldset id="layotter-row-layouts">
                <?php
                foreach (self::$default_settings['rows']['allow'] as $layout => $default_value) {
                    $layout_array = explode(' ', $layout);

                    ?>
                    <p>
                        <label>
                            <input type="checkbox" data-layout="<?php echo $layout; ?>"
                                   name="layotter_settings[rows][allow][<?php echo $layout; ?>]"
                                   value="1" <?php if (isset($settings['allow'][ $layout ])) {
                                checked($settings['allow'][ $layout ]);
                            } ?>>
                            <span class="layotter-row-layout-option">
                                    <?php

                                    foreach ($layout_array as $col_width) {
                                        echo '<span data-width="' . $col_width . '"></span>';
                                    }

                                    ?>
                                </span>
                            <span class="layotter-default-row-layout-message description"><?php _e('default for new rows', 'layotter'); ?></span>
                        </label>
                    </p>
                    <?php

                }
                ?>
            </fieldset>
            <h3>
                <?php _e('Default layout', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Select a default layout that will be used in all newly created rows.', 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/rows/default_layout')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/default_layout</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <select id="layotter-default-row-layout" name="layotter_settings[rows][default_layout]">
                <?php
                foreach (self::$default_settings['rows']['allow'] as $layout => $default_value) {

                    ?>
                    <option value="<?php echo $layout; ?>" <?php selected($settings['default_layout'] == $layout); ?>><?php echo $layout; ?></option>
                    <?php

                }
                ?>
            </select>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for column settings
     */
    public static function settings_cols() {
        // first, get current settings
        if (isset(self::$current_settings['cols'])) {
            $settings = self::$current_settings['cols'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-cols"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-cols' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('HTML wrapper', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php printf(__('Enter HTML code to wrap around each column. You can use the variable %s which will be replaced with the corresponding class name entered below.', 'layotter'), '<code>%%CLASS%%</code>'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <?php
                $has_filter = false;
                if (has_filter('layotter/view/column')) {
                    $has_filter = true;
                    ?>
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("These settings currently have no effect because they're overwritten by a %s filter used in your code.", 'layotter'), '<code>layotter/view/column</code>');
                } else {
                    ?>
                    <i class="fa fa-info-circle"></i>
                    <?php
                    printf(__('You can use <a href="%s" target="_blank">view filters</a> for way more flexibility.', 'layotter'), self::$docs_view_filters_link);
                }
                ?>
            </p>
            <?php
            if ($has_filter) {
            ?>
            <div class="layotter-shaded">
                <?php
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each column:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div
                                    class="%%CLASS%%"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[cols][html_before]" rows="4"
                                      cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after each column:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[cols][html_after]" rows="4"
                                      cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                if ($has_filter) {
                ?>
            </div>
        <?php
        }
        ?>
            <h3><?php _e('CSS classes for columns', 'layotter'); ?></h3>
            <p class="layotter-settings-paragraph">
                <?php _e("Enter a class name for each type of column so you'll be able to target them via CSS.", 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/columns/classes')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/columns/classes</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <table class="form-table">
                <tbody>
                <?php
                foreach (self::$default_settings['cols']['classes'] as $col => $default_class) {

                    ?>
                    <tr valign="top">
                        <th scope="row">
                            <?php echo self::$col_class_translations[ $col ]; ?>
                        </th>
                        <td>
                            <input type="text" name="layotter_settings[cols][classes][<?php echo $col; ?>]"
                                   value="<?php echo $settings['classes'][ $col ]; ?>">
                            <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span>
                            <code class="layotter-default-value"
                                  title="<?php _e('Click to reset', 'layotter'); ?>"><?php echo $default_class; ?></code>
                        </td>
                    </tr>
                    <?php

                }
                ?>
                </tbody>
            </table>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for element settings
     */
    public static function settings_elements() {
        // first, get current settings
        if (isset(self::$current_settings['elements'])) {
            $settings = self::$current_settings['elements'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-elements"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-elements' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('HTML wrapper', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Enter HTML code to wrap around each element.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <?php
                $has_filter = false;
                if (has_filter('layotter/view/element')) {
                    $has_filter = true;
                    ?>
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("These settings currently have no effect because they're overwritten by a %s filter used in your code.", 'layotter'), '<code>layotter/view/element</code>');
                } else {
                    ?>
                    <i class="fa fa-info-circle"></i>
                    <?php
                    printf(__('You can use <a href="%s" target="_blank">view filters</a> for way more flexibility.', 'layotter'), self::$docs_view_filters_link);
                }
                ?>
            </p>
            <?php
            if ($has_filter) {
            ?>
            <div class="layotter-shaded">
                <?php
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each element:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div class="lo-element"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[elements][html_before]" rows="4"
                                      cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after each element:', 'layotter'); ?>
                            <p>
                                <span class="description layotter-description"><?php _e('Default:', 'layotter'); ?></span><br><code
                                        class="layotter-default-value"
                                        title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="layotter_settings[elements][html_after]" rows="4"
                                      cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                if ($has_filter) {
                ?>
            </div>
        <?php
        }
        ?>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
        </div>
        <?php
    }

    /**
     * Outputs form HTML for debug settings
     * This setting is actually a part of the general settings data structure, but has its own tab for clarity.
     */
    public static function settings_debug() {
        // first, get current settings
        if (isset(self::$current_settings['general'])) {
            $settings = self::$current_settings['general'];
        } else {
            $settings = [];
        }

        ?>
        <div id="layotter-settings-debug"
             class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-debug' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('Debug mode', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('With debug mode enabled you can inspect and manually edit the JSON structure generated by Layotter. Enable debug mode for these user roles:', 'layotter'); ?>
            </p>
            <?php
            if (has_filter('layotter/debug_mode_roles')) {
                ?>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    printf(__("There's a %s filter in your code that might be modifying these settings.", 'layotter'), '<code>layotter/debug_mode_roles</code>');
                    ?>
                </p>
                <?php
            }
            ?>
            <div class="layotter-settings-checkboxes">
                <?php
                foreach (get_editable_roles() as $role_key => $role) {

                    ?>
                    <p>
                        <label>
                            <input type="checkbox"
                                   name="layotter_settings[general][debug_mode][<?php echo $role_key; ?>]"
                                   value="1" <?php if (isset($settings['debug_mode'][ $role_key ])) {
                                checked($settings['debug_mode'][ $role_key ]);
                            } ?>>
                            <?php echo translate_user_role($role['name']); ?>
                        </label>
                    </p>
                    <?php

                }
                ?>
            </div>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
        </div>
        <?php
    }

}
