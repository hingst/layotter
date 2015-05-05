<?php


Layotter_Settings::init();

/**
 * Creates settings page and sets default settings on plugin activation. Completely self-contained.
 */
class Layotter_Settings {
    
    private static
        $last_edited_tab,
        $current_settings,
        $default_settings,
        $col_class_translations;
    
    
    public static function init() {
        // do stuff on plugin activation
        register_activation_hook(dirname(__DIR__) . '/index.php', array(__CLASS__, 'set_defaults_on_activation'));


        // do stuff on plugin deactivation
        // use the following line FOR DEBUGGING ONLY
        // REMOVES ALL SETTINGS on plugin deactivation
        register_deactivation_hook(dirname(__DIR__) . '/index.php', array(__CLASS__, 'remove_all_settings'));


        // translate labels on admin_init - otherwise translations wouldn't be available yet
        add_action('admin_init', array(__CLASS__, 'translate_labels'));


        // enable settings page only if we're in the backend
        if (is_admin()) {
            // register settings, create menu entry, load assets
            add_action('admin_init', array(__CLASS__, 'admin_init'));
            add_action('admin_menu', array(__CLASS__, 'admin_menu'));
            add_action('admin_head', array(__CLASS__, 'admin_head'));
        }
        
        self::$default_settings = array(
            'basic' => array(
                'enable_for' => array(
                    'page' => '1'
                ),
                'enable_default_element_type' => '1'
            ),
            'general' => array(
                'enable_post_layouts' => '1',
                'enable_element_templates' => '1',
                'debug_mode' => array(
                    'administrator' => '0'
                )
            ),
            'wrapper' => array(
                'html_before' => '<div id="content">',
                'html_after' => '</div>'
            ),
            'rows' => array(
                'html_before' => '<div class="row">',
                'html_after' => '</div>',
                'allow' => array(
                    '1/1' => '1',
                    '1/2 1/2' => '1',
                    '1/3 1/3 1/3' => '1',
                    '1/3 2/3' => '1',
                    '2/3 1/3' => '1',
                    '1/4 1/4 1/4 1/4' => '0',
                    '3/4 1/4' => '0',
                    '1/4 3/4' => '0',
                ),
                'default_layout' => '1/3 1/3 1/3'
            ),
            'cols' => array(
                'html_before' => '<div class="%%CLASS%%">',
                'html_after' => '</div>',
                'classes' => array(
                    '1/1' => 'col size12of12',
                    '1/2' => 'col size6of12',
                    '1/3' => 'col size4of12',
                    '2/3' => 'col size8of12',
                    '1/4' => 'col size3of12',
                    '3/4' => 'col size9of12',
                    '1/6' => 'col size2of12',
                    '1/12' => 'col size1of12',
                    '5/12' => 'col size5of12',
                    '7/12' => 'col size7of12',
                    '11/12' => 'col size11of12',
                ),
            ),
            'elements' => array(
                'html_before' => '<div class="element">',
                'html_after' => '</div>'
            )
        );
    }


    public static function translate_labels() {
        self::$col_class_translations = array(
            '1/1' => __('Full width', 'layotter'),
            '1/2' => __('A half', 'layotter'),
            '1/3' => __('A third', 'layotter'),
            '2/3' => __('Two thirds', 'layotter'),
            '1/4' => __('A fourth', 'layotter'),
            '3/4' => __('Three fourths', 'layotter'),
            '1/6' => __('A sixth', 'layotter'),
            '1/12' => __('A twelfth', 'layotter'),
            '5/12' => __('Five twelfths', 'layotter'),
            '7/12' => __('Seven twelfths', 'layotter'),
            '11/12' => __('Eleven twelfths', 'layotter')
        );
    }


    public static function get_settings($which = '') {
        $settings = get_option('layotter_settings');
        if (!is_array($settings)) {
            return array();
        } else if (isset($settings[$which])) {
            return $settings[$which];
        } else {
            return $settings;
        }
    }


    public static function get_allowed_row_layouts() {
        $allowed_layouts = array();

        if (has_filter('layotter/row_layouts')) {
            $allowed_layouts = apply_filters('layotter/row_layouts', array());
        } else {
            $settings = self::get_settings('rows');
            foreach ($settings['allow'] as $layout => $allowed) {
                if ($allowed == '1') {
                    $allowed_layouts[] = $layout;
                }
            }
        }

        return $allowed_layouts;
    }
    
    
    public static function get_default_row_layout() {
        $settings = self::get_settings('rows');
        return $settings['default_layout'];
    }
    
    
    /**
     * Take a col layout and return user-provided class
     * 
     * @param string $layout Col layout string (e.g. 'half', 'third')
     * @return string CSS class for the col layout, as provided by the user
     */
    public static function get_col_layout_class($layout) {
        $settings = self::get_settings('cols');
        return $settings['classes'][$layout];
    }
    
    
    /**
     * Set default settings on plugin activation
     */
    public static function set_defaults_on_activation() {
        // add_option makes sure existing settings will not be overwritten (as opposed to update_option)
        add_option('layotter_settings', self::$default_settings);
    }


    /**
     * Remove all settings on plugin deactivation
     *
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
        
        wp_enqueue_script(
            'layotter-settings',
            plugins_url('assets/js/settings.js', __DIR__),
            array('jquery')
        );
        wp_enqueue_style(
            'layotter-settings',
            plugins_url('assets/css/editor.css', __DIR__)
        );
        wp_enqueue_style(
            'layotter-font-awesome',
            plugins_url('assets/css/font-awesome.min.css', __DIR__)
        );
        
        // display notice if settings have been saved
        if (isset($_GET['settings-updated'])) {
            add_action('admin_notices', array(__CLASS__, 'settings_saved_notice'));
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
        add_menu_page(
            __('Layotter Settings', 'layotter'), // title
            'Layotter', // menu name
            'activate_plugins', // capability
            'layotter-settings', // page name
            array(__CLASS__, 'settings_page'), // callback
            'dashicons-tagcloud', // icon
            null // position
       );
    }
    
    
    /**
     * Output HTML for the settings page
     */
    public static function settings_page() {
        self::$current_settings = self::get_settings();

        self::$last_edited_tab = '#layotter-settings-basic';
        if (isset($_GET['settings-updated']) AND isset(self::$current_settings['internal']['last_edited_tab']) AND !empty(self::$current_settings['internal']['last_edited_tab'])) {
            self::$last_edited_tab = self::$current_settings['internal']['last_edited_tab'];
        }

        ?>
            <div class="wrap">
                <div id="icon-themes" class="icon32"></div>
                <h2><?php _e('Layotter Settings', 'layotter'); ?></h2>
                <h2 class="nav-tab-wrapper">
                    <a href="#layotter-settings-basic" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-basic' ? ' nav-tab-active' : ''; ?>"><?php _e('Basic', 'layotter'); ?></a>
                    <a href="#layotter-settings-general" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-general' ? ' nav-tab-active' : ''; ?>"><?php _e('General', 'layotter'); ?></a>
                    <a href="#layotter-settings-wrapper" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-wrapper' ? ' nav-tab-active' : ''; ?>"><?php _e('Wrapper', 'layotter'); ?></a>
                    <a href="#layotter-settings-rows" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-rows' ? ' nav-tab-active' : ''; ?>"><?php _e('Rows', 'layotter'); ?></a>
                    <a href="#layotter-settings-cols" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-cols' ? ' nav-tab-active' : ''; ?>"><?php _e('Columns', 'layotter'); ?></a>
                    <a href="#layotter-settings-elements" class="nav-tab<?php echo self::$last_edited_tab == '#layotter-settings-elements' ? ' nav-tab-active' : ''; ?>"><?php _e('Elements', 'layotter'); ?></a>
                </h2>
                <form action="options.php" method="post">
                    <?php
                        
                    // to keep things clean, each form has its own method.
                    // settings_fields() creates a nonce and other necessary form elements
                    // Wordpress takes care of saving form data, which is nice

                    settings_fields('layotter_settings');

                    self::settings_basic();
                    self::settings_general();
                    self::settings_wrapper();
                    self::settings_rows();
                    self::settings_cols();
                    self::settings_elements();
                        
                    ?>
                    <input type="hidden" id="layotter-last-edited-tab" name="layotter_settings[internal][last_edited_tab]" value="<?php echo self::$last_edited_tab; ?>">
                </form>
            </div>
        <?php
    }


    /**
     * Outputs form HTML for basic settings
     */
    public static function settings_basic() {
        // first, get current settings
        if (isset(self::$current_settings['basic'])) {
            $settings = self::$current_settings['basic'];
        } else {
            $settings = array();
        }

        ?>
        <div id="layotter-settings-basic" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-basic' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('Post types', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('Select the post types for which Layotter should be enabled.', 'layotter'); ?>
            </p>
            <div class="layotter-settings-checkboxes">
                <p>
                    <label>
                        <input type="checkbox" name="layotter_settings[basic][enable_for][post]" value="1" <?php if(isset($settings['enable_for']['post'])) { checked($settings['enable_for']['post']); } ?>>
                        <?php _e('Posts', 'layotter'); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="layotter_settings[basic][enable_for][page]" value="1" <?php if(isset($settings['enable_for']['page'])) { checked($settings['enable_for']['page']); } ?>>
                        <?php _e('Pages','layotter'); ?>
                    </label>
                </p>
                <?php

                $post_types = get_post_types(array(
                    '_builtin' => false,
                    'show_ui' => true
                ), 'objects');

                foreach ($post_types as $post_type) {

                    // exclude ACF field groups
                    if ($post_type->name == 'acf-field-group') {
                        continue;
                    }

                    ?>
                    <p>
                        <label>
                            <input type="checkbox" name="layotter_settings[basic][enable_for][<?php echo $post_type->name; ?>]" value="1" <?php if(isset($settings['enable_for'][$post_type->name])) { checked($settings['enable_for'][$post_type->name]); } ?>>
                            <?php echo $post_type->label; ?>
                        </label>
                    </p>
                <?php

                }
                ?>
            </div>
            <h3>
                <?php _e('Default element type', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e("Layotter comes with an example element type to get started and try out the editor. If you don't want to use the default element type, you can disable it here.", 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <i class="fa fa-warning"></i>
                <?php _e('When disabling, any elements you may have created with the default element type will disappear.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[basic][use_default_element_type]" value="1" <?php if(isset($settings['use_default_element_type'])) { checked($settings['use_default_element_type']); } ?>>
                    <?php _e('Enable default element type', 'layotter'); ?>
                </label>
            </p>
            <?php
            submit_button(__('Save settings', 'layotter'));
            ?>
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
            $settings = array();
        }

        ?>
        <div id="layotter-settings-general" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-general' ? ' hidden' : ''; ?>">
            <h3>
                <?php _e('Post layouts', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('This feature lets you save entire post layouts as templates to be used as a starting point for new posts.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_post_layouts]" value="1" <?php if(isset($settings['enable_post_layouts'])) { checked($settings['enable_post_layouts']); } ?>>
                    <?php _e('Enable post layouts', 'layotter'); ?>
                </label>
            </p>
            <h3>
                <?php _e('Element templates', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e("This feature lets you save elements as templates. Element templates look just like regular elements, but if you edit them on one page, they'll be updated on every other page as well.", 'layotter'); ?>
            </p>
            <p class="layotter-settings-checkboxes">
                <label>
                    <input type="checkbox" name="layotter_settings[general][enable_element_templates]" value="1" <?php if(isset($settings['enable_element_templates'])) { checked($settings['enable_element_templates']); } ?>>
                    <?php _e('Enable element templates', 'layotter'); ?>
                </label>
            </p>
            <h3>
                <?php _e('Debug mode', 'layotter'); ?>
            </h3>
            <p class="layotter-settings-paragraph">
                <?php _e('With debug mode enabled you can inspect and manually edit the JSON structure generated by Layotter. Enable debug mode for these user roles:', 'layotter'); ?>
            </p>
            <div class="layotter-settings-checkboxes">
                <?php
                foreach (get_editable_roles() as $role_key => $role) {

                    // subscribers will never even see the backend
                    if ($role_key == 'subscriber') {
                        continue;
                    }

                    ?>
                    <p>
                        <label>
                            <input type="checkbox" name="layotter_settings[general][debug_mode][<?php echo $role_key; ?>]" value="1" <?php if(isset($settings['debug_mode'][$role_key])) { checked($settings['debug_mode'][$role_key]); } ?>>
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
    
    
    /**
     * Outputs form HTML for wrapper settings
     */
    public static function settings_wrapper() {
        // first, get current settings
        if (isset(self::$current_settings['wrapper'])) {
            $settings = self::$current_settings['wrapper'];
        } else {
            $settings = array();
        }
        
        ?>
            <div id="layotter-settings-wrapper" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-wrapper' ? ' hidden' : ''; ?>">
                <h3>
                    <?php _e('HTML wrapper', 'layotter'); ?>
                </h3>
                <p class="layotter-settings-paragraph">
                    <?php _e('Enter HTML code to wrap around the whole content.', 'layotter'); ?>
                </p>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <?php
                    if (has_filter('layotter/view/post')) {
                        ?>
                        <i class="fa fa-warning"></i>
                        <?php
                        printf(__('These settings currently have no effect because they\'re overwritten by a %s filter used in your code. See <a href="%s" target="_blank">the documentation</a> for more info.', 'layotter'), '<code>layotter/view/post</code>', '#');
                    } else {
                        ?>
                        <i class="fa fa-info"></i>
                        <?php
                        printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'layotter'), '#', '#');
                    }
                    ?>
                </p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML before content:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div id="content"&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[wrapper][html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML after content:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[wrapper][html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php
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
            $settings = array();
        }
        
        ?>
            <div id="layotter-settings-rows" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-rows' ? ' hidden' : ''; ?>">
                <h3>
                    <?php _e('HTML wrapper', 'layotter'); ?>
                </h3>
                <p class="layotter-settings-paragraph">
                    <?php _e('Enter HTML code to wrap around each row.', 'layotter'); ?>
                </p>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <?php
                    if (has_filter('layotter/view/row')) {
                        ?>
                        <i class="fa fa-warning"></i>
                        <?php
                        printf(__('These settings currently have no effect because they\'re overwritten by a %s filter used in your code. See <a href="%s" target="_blank">the documentation</a> for more info.', 'layotter'), '<code>layotter/view/row</code>', '#');
                    } else {
                        ?>
                        <i class="fa fa-info"></i>
                        <?php
                        printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'layotter'), '#', '#');
                    }
                    ?>
                </p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML before each row:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div class="row"&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[rows][html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php  _e('HTML after each row:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[rows][html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>
                    <?php _e('Allowed layouts', 'layotter'); ?>
                </h3>
                <p class="layotter-settings-paragraph">
                    <?php _e('Choose the row layouts you want to use. Disabling a layout means it will not be available for newly created rows from now on &ndash; existing rows with that layout will stay the way they are until you change them by hand.', 'layotter'); ?>
                </p>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <?php
                    if (has_filter('layotter/row_layouts')) {
                        ?>
                        <i class="fa fa-warning"></i>
                        <?php
                        printf(__('These settings currently have no effect because they\'re overwritten by a %s filter used in your code. See <a href="%s" target="_blank">the documentation</a> for more info.', 'layotter'), '<code>layotter/row_layouts</code>', '#');
                    } else {
                        ?>
                        <i class="fa fa-info"></i>
                        <?php
                        printf(__('Create more complex layouts using <a href="%s" target="_blank">filters</a>! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'layotter'), '#', '#');
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
                                <input type="checkbox" data-layout="<?php echo $layout; ?>" name="layotter_settings[rows][allow][<?php echo $layout; ?>]" value="1" <?php if(isset($settings['allow'][$layout])) { checked($settings['allow'][$layout]); } ?>>
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
                    <?php _e('Select a default layout that will be used in all newly created rows.', 'layotter');?>
                </p>
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
            $settings = array();
        }
        
        ?>
            <div id="layotter-settings-cols" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-cols' ? ' hidden' : ''; ?>">
                <h3>
                    <?php _e('HTML wrapper', 'layotter'); ?>
                </h3>
                <p class="layotter-settings-paragraph">
                    <?php printf(__('Enter HTML code to wrap around each column. You can use the variable %s which will be replaced with the corresponding class name entered below.', 'layotter'), '<code>%%CLASS%%</code>'); ?>
                </p>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <?php
                    if (has_filter('layotter/view/column')) {
                        ?>
                        <i class="fa fa-warning"></i>
                        <?php
                        printf(__('These settings currently have no effect because they\'re overwritten by a %s filter used in your code. See <a href="%s" target="_blank">the documentation</a> for more info.', 'layotter'), '<code>layotter/view/column</code>', '#');
                    } else {
                        ?>
                        <i class="fa fa-info"></i>
                        <?php
                        printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'layotter'), '#', '#');
                    }
                    ?>
                </p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML before each column:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div class="%%CLASS%%"&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[cols][html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML after each column:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[cols][html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3><?php _e('Class attributes for columns', 'layotter'); ?></h3>
                <p class="layotter-settings-paragraph">
                    <?php  _e('Enter a class attribute for each type of column so you\'ll be able to target them via CSS.', 'layotter'); ?>
                </p>
                <table class="form-table">
                    <tbody>
                        <?php
                        foreach (self::$default_settings['cols']['classes'] as $col => $default_class) {

                            ?>
                            <tr valign="top">
                                <th scope="row">
                                    <?php echo self::$col_class_translations[$col]; ?>
                                </th>
                                <td>
                                    <input type="text" name="layotter_settings[cols][classes][<?php echo $col; ?>]" value="<?php echo $settings['classes'][$col]; ?>">
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span> <code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>"><?php echo $default_class; ?></code>
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
            $settings = array();
        }
        
        ?>
            <div id="layotter-settings-elements" class="layotter-settings-tab-content<?php echo self::$last_edited_tab != '#layotter-settings-elements' ? ' hidden' : ''; ?>">
                <h3>
                    <?php _e('HTML wrapper', 'layotter'); ?>
                </h3>
                <p class="layotter-settings-paragraph">
                    <?php _e('Enter HTML code to wrap around each element.', 'layotter'); ?>
                </p>
                <p class="layotter-settings-paragraph layotter-with-icon">
                    <?php
                    if (has_filter('layotter/view/element')) {
                        ?>
                        <i class="fa fa-warning"></i>
                        <?php
                        printf(__('These settings currently have no effect because they\'re overwritten by a %s filter used in your code. See <a href="%s" target="_blank">the documentation</a> for more info.', 'layotter'), '<code>layotter/view/element</code>', '#');
                    } else {
                        ?>
                        <i class="fa fa-info"></i>
                        <?php
                        printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'layotter'), '#', '#');
                    }
                    ?>
                </p>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML before each element:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;div class="element"&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[elements][html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('HTML after each element:', 'layotter'); ?>
                                <p>
                                    <span class="description"><?php _e('Default:', 'layotter'); ?></span><br><code class="layotter-default-value" title="<?php _e('Click to reset', 'layotter'); ?>">&lt;/div&gt;</code>
                                </p>
                            </th>
                            <td>
                                <textarea name="layotter_settings[elements][html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php
                submit_button(__('Save settings', 'layotter'));
                ?>
            </div>
        <?php
    }

    
}