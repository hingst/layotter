<?php


Eddditor_Settings::init();


/**
 * Creates settings pages and sets default settings on plugin activation. Completely self-contained.
 */
class Eddditor_Settings {
    
    private static
        $default_settings,
        $col_class_titles,
        $row_layout_titles;
    
    
    public static function init() {
        // do stuff on plugin activation
        register_activation_hook(__DIR__ . '/index.php', array(__CLASS__, 'set_defaults_on_activation'));


        // do stuff on plugin deactivation
        // use the following line FOR DEBUGGING ONLY
        // REMOVES ALL SETTINGS on plugin deactivation
        register_deactivation_hook(__DIR__ . '/index.php', array(__CLASS__, 'remove_all_settings'));


        // translate labels after plugins_loaded - otherwise translations would not be available yet
        add_action('plugins_loaded', array(__CLASS__, 'translate_labels'));


        // enable settings page only if we're in the backend
        if (is_admin()) {
            // register settings, create menu entry, load assets
            add_action('admin_init', array(__CLASS__, 'admin_init'));
            add_action('admin_menu', array(__CLASS__, 'admin_menu'));
            add_action('admin_head', array(__CLASS__, 'admin_head'));
        }
        
        self::$default_settings = array(
            'general' => array(
                'enable_for' => array(
                    'page' => '1'
                ),
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
                    'full' => '1',
                    'half half' => '1',
                    'third third third' => '1',
                    'fourth fourth fourth fourth' => '1',
                    'fifth fifth fifth fifth fifth' => '0',
                    'sixth sixth sixth sixth sixth sixth' => '0',
                    'twothirds third' => '1',
                    'third twothirds' => '1',
                    'threefourths fourth' => '0',
                    'fourth threefourths' => '0',
                    'half fourth fourth' => '0',
                    'fourth fourth half' => '0',
                    'fourth half fourth' => '0'
                ),
                'default_layout' => 'third third third'
            ),
            'cols' => array(
                'html_before' => '<div class="%%CLASS%%">',
                'html_after' => '</div>',
                'classes' => array(
                    'full' => 'col full',
                    'half' => 'col half',
                    'third' => 'col third',
                    'twothirds' => 'col twothirds',
                    'fourth' => 'col fourth',
                    'threefourths' => 'col threefourths',
                    'fifth' => 'col fifth',
                    'sixth' => 'col sixth'
                ),
            ),
            'elements' => array(
                'html_before' => '<div class="element">',
                'html_after' => '</div>'
            )
        );
    }


    public static function translate_labels() {
        self::$col_class_titles = array(
            'full' => __('Full width', 'eddditor'),
            'half' => __('A half', 'eddditor'),
            'third' => __('A third', 'eddditor'),
            'twothirds' => __('Two thirds', 'eddditor'),
            'fourth' => __('A fourth', 'eddditor'),
            'threefourths' => __('Three fourths', 'eddditor'),
            'fifth' => __('A fifth', 'eddditor'),
            'sixth' => __('A sixth', 'eddditor')
        );

        self::$row_layout_titles = array(
            'full' => __('Single column', 'eddditor'),
            'half half' => __('Two halves', 'eddditor'),
            'third third third' => __('Thirds', 'eddditor'),
            'fourth fourth fourth fourth' => __('Fourths', 'eddditor'),
            'fifth fifth fifth fifth fifth' => __('Fifths', 'eddditor'),
            'sixth sixth sixth sixth sixth sixth' => __('Sixths', 'eddditor'),
            'twothirds third' => __('Two thirds & one third', 'eddditor'),
            'third twothirds' => __('One third & two thirds', 'eddditor'),
            'threefourths fourth' => __('Three fourths & a fourth', 'eddditor'),
            'fourth threefourths' => __('A fourth & three fourths', 'eddditor'),
            'half fourth fourth' => __('A half & two fourths', 'eddditor'),
            'fourth fourth half' => __('Two fourths & a half', 'eddditor'),
            'fourth half fourth' => __('A fourth & a half & a fourth', 'eddditor')
        );
    }


    public static function get_allowed_row_layouts() {
        $options = get_option('eddditor_settings_rows');
        $allowed_layouts = array();

        foreach ($options['allow'] as $col_type => $allowed) {
            if ($allowed == '1') {
                $allowed_layouts[] = array(
                    'title' => self::$row_layout_titles[$col_type],
                    'layout' => $col_type
                );
            }
        }

        return $allowed_layouts;
    }
    
    
    public static function get_default_row_layout() {
        $options = get_option('eddditor_settings_rows');
        return $options['default_layout'];
    }
    
    
    /**
     * Take a col layout and return user-provided class
     * 
     * @param string $layout Col layout string (e.g. 'half', 'third')
     * @return string CSS class for the col layout, as provided by the user
     */
    public static function get_col_layout_class($layout) {
        $options = get_option('eddditor_settings_cols');
        return $options['classes'][$layout];
    }
    
    
    /**
     * Remove all settings on plugin deactivation
     *
     * For debugging only!
     */
    public static function remove_all_settings() {
        delete_option('eddditor_settings_general');
        delete_option('eddditor_settings_wrapper');
        delete_option('eddditor_settings_rows');
        delete_option('eddditor_settings_cols');
        delete_option('eddditor_settings_elements');
    }
    
    
    /**
     * Set default settings on plugin activation
     */
    public static function set_defaults_on_activation() {
        // save default settings to the database
        foreach (self::$default_settings as $settings_group_name => $settings) {
            // add_option makes sure existing settings will not be overwritten (as opposed to update_option)
            add_option('eddditor_settings_' . $settings_group_name, $settings);
        }
    }
    
    
    /**
     * Register settings groups
     */
    public static function admin_init() {
        register_setting(
            'eddditor_settings_general',
            'eddditor_settings_general'
        );
        register_setting(
            'eddditor_settings_wrapper',
            'eddditor_settings_wrapper'
        );
        register_setting(
            'eddditor_settings_rows',
            'eddditor_settings_rows'
        );
        register_setting(
            'eddditor_settings_cols',
            'eddditor_settings_cols'
        );
        register_setting(
            'eddditor_settings_elements',
            'eddditor_settings_elements'
        );
    }
    
    
    /**
     * Load scripts and styles and show message if settings have just been saved
     */
    public static function admin_head() {
        // load assets only if necessary
        $current_screen = get_current_screen();
        if ($current_screen->id !== 'toplevel_page_eddditor-settings') {
            return;
        }
        
        wp_enqueue_script(
            'eddditor-settings',
            plugins_url('js/settings.js', __FILE__),
            array('jquery')
        );
        wp_enqueue_style(
            'eddditor-settings',
            plugins_url('css/editor.css', __FILE__)
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
            <div class="updated">
                <p><?php _e('Your settings have been saved.', 'eddditor'); ?></p>
            </div>
        <?php
    }
    
    
    /**
     * Create an admin menu entry for Eddditor
     */
    public static function admin_menu() {
        add_menu_page(
            __('Eddditor Settings', 'eddditor'), // title
            'Eddditor', // menu name
            'activate_plugins', // capability
            'eddditor-settings', // page name
            array(__CLASS__, 'settings_page'), // callback
            'dashicons-tagcloud', // icon
            null // position
       );
    }
    
    
    /**
     * Output HTML for the settings page
     */
    public static function settings_page() {
        // settings are divided into five sections, each with their own page and form
        $tab = ''; // empty means "general settings"
        if (isset($_GET['tab']) AND in_array($_GET['tab'], array('wrapper', 'rows', 'cols', 'elements'))) {
            $tab = $_GET['tab'];
        }
            
        ?>
            <div class="wrap">
                <div id="icon-themes" class="icon32"></div>
                <h2><?php _e('Eddditor Settings', 'eddditor'); ?></h2>
                <h2 class="nav-tab-wrapper">
                    <a href="?page=eddditor-settings" class="nav-tab <?php echo $tab == '' ? 'nav-tab-active' : ''; ?>"><?php
                        _e('General', 'eddditor');
                    ?></a>
                    <a href="?page=eddditor-settings&tab=wrapper" class="nav-tab <?php echo $tab == 'wrapper' ? 'nav-tab-active' : ''; ?>"><?php
                        _e('Wrapper', 'eddditor');
                    ?></a>
                    <a href="?page=eddditor-settings&tab=rows" class="nav-tab <?php echo $tab == 'rows' ? 'nav-tab-active' : ''; ?>"><?php
                        _e('Rows', 'eddditor');
                    ?></a>
                    <a href="?page=eddditor-settings&tab=cols" class="nav-tab <?php echo $tab == 'cols' ? 'nav-tab-active' : ''; ?>"><?php
                        _e('Columns', 'eddditor');
                    ?></a>
                    <a href="?page=eddditor-settings&tab=elements" class="nav-tab <?php echo $tab == 'elements' ? 'nav-tab-active' : ''; ?>"><?php
                        _e('Elements', 'eddditor');
                    ?></a>
                </h2>
                <form action="options.php" method="post">
                    <?php
                        
                    // to keep things clean, each form has its own method.
                    // settings_fields() creates a nonce and other necessary form elements
                    // Wordpress takes care of saving form data, which is nice

                    if ($tab == 'wrapper') {
                        settings_fields('eddditor_settings_wrapper');
                        self::settings_wrapper();
                        $submit_button_text = __('Save Wrapper Settings', 'eddditor');
                    } else if ($tab == 'rows') {
                        settings_fields('eddditor_settings_rows');
                        self::settings_rows();
                        $submit_button_text = __('Save Row Settings', 'eddditor');
                    } else if ($tab == 'cols') {
                        settings_fields('eddditor_settings_cols');
                        self::settings_cols();
                        $submit_button_text = __('Save Column Settings', 'eddditor');
                    } else if ($tab == 'elements') {
                        settings_fields('eddditor_settings_elements');
                        self::settings_elements();
                        $submit_button_text = __('Save Element Settings', 'eddditor');
                    } else {
                        settings_fields('eddditor_settings_general');
                        self::settings_general();
                        $submit_button_text = __('Save General Settings', 'eddditor');
                    }

                    submit_button($submit_button_text);
                        
                    ?>
                </form>
            </div>
        <?php
    }
    
    
    /**
     * Outputs form HTML for general settings
     */
    public static function settings_general() {
        // first, get current settings
        $settings = get_option('eddditor_settings_general');
        
        ?>
            <h3>
                <?php _e('Post types', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Select the post types for which Eddditor should be enabled.', 'eddditor'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <td>
                            <p>
                                <label>
                                    <input type="checkbox" name="eddditor_settings_general[enable_for][post]" value="1" <?php if(isset($settings['enable_for']['post'])) { checked($settings['enable_for']['post']); } ?>>
                                    <?php _e('Posts', 'eddditor'); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="eddditor_settings_general[enable_for][page]" value="1" <?php if(isset($settings['enable_for']['page'])) { checked($settings['enable_for']['page']); } ?>>
                                    <?php _e('Pages','eddditor'); ?>
                                </label>
                            </p>
                            <?php
                            $post_types = get_post_types(array('_builtin' => false, 'show_ui' => true), 'objects');
                            foreach ($post_types as $post_type) {

                                // exclude acf field groups
                                if ($post_type->name == 'acf-field-group') {
                                    continue;
                                }

                                ?>
                                <p>
                                    <label>
                                        <input type="checkbox" name="eddditor_settings_general[enable_for][<?php echo $post_type->name; ?>]" value="1" <?php if(isset($settings['enable_for'][$post_type->name])) { checked($settings['enable_for'][$post_type->name]); } ?>>
                                        <?php echo $post_type->label; ?>
                                    </label>
                                </p>
                                <?php

                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3>
                <?php _e('Debug mode', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('With debug mode enabled you can inspect and manually edit the JSON structure generated by Eddditor. Enable debug mode for these user roles:', 'eddditor'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <td>
                            <?php
                            foreach (get_editable_roles() as $role_key => $role) {

                                // subscribers will never even see the backend
                                if ($role_key == 'subscriber') {
                                    continue;
                                }

                                ?>
                                <p>
                                    <label>
                                        <input type="checkbox" name="eddditor_settings_general[debug_mode][<?php echo $role_key; ?>]" value="1" <?php if(isset($settings['debug_mode'][$role_key])) { checked($settings['debug_mode'][$role_key]); } ?>>
                                        <?php echo translate_user_role($role['name']); ?>
                                    </label>
                                </p>
                                <?php

                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
    }
    
    
    /**
     * Outputs form HTML for wrapper settings
     */
    public static function settings_wrapper() {
        // first, get current settings
        $settings = get_option('eddditor_settings_wrapper');
        
        ?>
            <h3>
                <?php _e('HTML wrapper', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Enter HTML code to wrap around the whole content.', 'eddditor'); ?>
            </p>
            <p>
                <strong><?php _e('Tip:', 'eddditor'); ?></strong>
                <?php printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'eddditor'), '#', '#'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before content:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;div id="content"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_wrapper[html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after content:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_wrapper[html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
    }
    
    
    /**
     * Outputs form HTML for row settings
     */
    public static function settings_rows() {
        // first, get current settings
        $settings = get_option('eddditor_settings_rows');
        
        ?>
            <h3>
                <?php _e('HTML wrapper', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Enter HTML code to wrap around each row.', 'eddditor'); ?>
            </p>
            <p>
                <strong><?php _e('Tip:', 'eddditor'); ?></strong>
                <?php printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'eddditor'), '#', '#'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each row:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;div class="row"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_rows[html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php  _e('HTML after each row:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_rows[html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3>
                <?php _e('Allowed layouts', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Choose the row layouts you want to use. Disabling a layout means it will not be available for newly created rows from now on &ndash; existing rows with that layout will stay the way they are until you change them by hand.', 'eddditor'); ?>
            </p>
            <fieldset id="eddditor-row-layouts">
                <?php
                foreach (self::$default_settings['rows']['allow'] as $layout => $default_value) {

                    ?>
                    <p>
                        <label>
                            <input type="checkbox" data-layout="<?php echo $layout; ?>" name="eddditor_settings_rows[allow][<?php echo $layout; ?>]" value="1" <?php checked($settings['allow'][$layout]); ?>>
                            <span class="eddditor-row-layout-option" data-layout="<?php echo $layout; ?>" alt="">
                                <?php echo self::$row_layout_titles[$layout]; ?> <span class="eddditor-default-row-layout-message description">&ndash; <?php _e('default for new rows', 'eddditor'); ?></span>
                            </span>
                        </label>
                    </p>
                    <?php

                }
                ?>
            </fieldset>
            <h3>
                <?php _e('Default layout', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Select a default layout that will be used in all newly created rows.', 'eddditor');?>
            </p>
            <select id="eddditor-default-row-layout" name="eddditor_settings_rows[default_layout]">
                <?php
                foreach (self::$default_settings['rows']['allow'] as $layout => $default_value) {

                    ?>
                    <option value="<?php echo $layout; ?>" <?php selected($settings['default_layout'] == $layout); ?>><?php echo self::$row_layout_titles[$layout]; ?></option>
                    <?php

                }
                ?>
            </select>
        <?php
    }
    
    
    /**
     * Outputs form HTML for column settings
     */
    public static function settings_cols() {
        // first, get current settings
        $settings = get_option('eddditor_settings_cols');
        
        ?>
            <h3>
                <?php _e('HTML wrapper', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Enter HTML code to wrap around each column. You can use the variable %%CLASS%% which will be replaced with the corresponding class name entered below.', 'eddditor'); ?>
            </p>
            <p>
                <strong><?php _e('Tip:', 'eddditor'); ?></strong>
                <?php printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'eddditor'), '#', '#'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each column:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;div class="%%CLASS%%"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_cols[html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after each column:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_cols[html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3><?php _e('Class attributes for columns', 'eddditor'); ?></h3>
            <p>
                <?php  _e('Enter a class attribute for each type of column so you\'ll be able to target them via CSS.', 'eddditor'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <?php
                    foreach (self::$default_settings['cols']['classes'] as $col => $default_class) {
                        
                        ?>
                        <tr valign="top">
                            <th scope="row">
                                <?php echo self::$col_class_titles[$col]; ?>
                            </th>
                            <td>
                                <input type="text" name="eddditor_settings_cols[classes][<?php echo $col; ?>]" value="<?php echo $settings['classes'][$col]; ?>">
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span> <code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>"><?php echo $default_class; ?></code>
                            </td>
                        </tr>
                        <?php
                        
                    }
                    ?>
                </tbody>
            </table>
        <?php
    }
    
    
    /**
     * Outputs form HTML for element settings
     */
    public static function settings_elements() {
        // first, get current settings
        $settings = get_option('eddditor_settings_elements');
        
        ?>
            <h3>
                <?php _e('HTML wrapper', 'eddditor'); ?>
            </h3>
            <p>
                <?php _e('Enter HTML code to wrap around each element.', 'eddditor'); ?>
            </p>
            <p>
                <strong><?php _e('Tip:', 'eddditor'); ?></strong>
                <?php printf(__('Use <a href="%s" target="_blank">filters</a> for way more flexibility! Take a look at <a href="%s" target="_blank">the docs</a> to see what\'s possible.', 'eddditor'), '#', '#'); ?>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML before each element:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;div class="element"&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_elements[html_before]" rows="4" cols="60"><?php echo $settings['html_before']; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('HTML after each element:', 'eddditor'); ?>
                            <p>
                                <span class="description"><?php _e('Default:', 'eddditor'); ?></span><br><code class="eddditor-default-value" title="<?php _e('Click to reset', 'eddditor'); ?>">&lt;/div&gt;</code>
                            </p>
                        </th>
                        <td>
                            <textarea name="eddditor_settings_elements[html_after]" rows="4" cols="60"><?php echo $settings['html_after']; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
    }

    
}