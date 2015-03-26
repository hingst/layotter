<?php


/**
 * Enqueue styles and scripts, provide l10n for use with Javascript
 */
add_action('admin_enqueue_scripts', 'eddditor_assets_admin_enqueue_scripts');
function eddditor_assets_admin_enqueue_scripts() {

    // load assets only if necessary
    if (!Eddditor::is_enabled()) {
        return;
    }


    // styles
    wp_enqueue_style(
        'eddditor',
        plugins_url('assets/css/editor.css', __FILE__)
    );
    wp_enqueue_style(
        'eddditor-font-awesome',
        plugins_url('assets/css/font-awesome.min.css', __FILE__)
    );


    // jQuery plugin used to serialize form data
    wp_enqueue_script(
        'eddditor-serialize',
        plugins_url('assets/js/vendor/jquery.serialize-object.compiled.js', __FILE__),
        array('jquery')
    );


    // Angular scripts
    $scripts = array(
        'angular' => 'assets/js/vendor/angular.js',
        'angular-animate' => 'assets/js/vendor/angular-animate.js',
        'angular-sanitize' => 'assets/js/vendor/angular-sanitize.js',
        'angular-ui-sortable' => 'assets/js/vendor/angular-ui-sortable.js',
        'eddditor' => 'assets/js/app/app.js',
        'eddditor-controller-editor' => 'assets/js/app/controllers/editor.js',
        'eddditor-controller-templates' => 'assets/js/app/controllers/templates.js',
        'eddditor-controller-form' => 'assets/js/app/controllers/form.js',
        'eddditor-service-state' => 'assets/js/app/services/state.js',
        'eddditor-service-data' => 'assets/js/app/services/data.js',
        'eddditor-service-content' => 'assets/js/app/services/content.js',
        'eddditor-service-templates' => 'assets/js/app/services/templates.js',
        'eddditor-service-layouts' => 'assets/js/app/services/layouts.js',
        'eddditor-service-view' => 'assets/js/app/services/view.js',
        'eddditor-service-forms' => 'assets/js/app/services/forms.js',
        'eddditor-service-modals' => 'assets/js/app/services/modals.js'
    );
    foreach ($scripts as $name => $path) {
        wp_enqueue_script(
            $name,
            plugins_url($path, __FILE__)
        );
    }
    
    
    // fetch allowed row layouts and default layout
    $allowed_row_layouts = Eddditor_Settings::get_allowed_row_layouts();
    $default_row_layout = Eddditor_Settings::get_default_row_layout();
    
    
    // fetch default values for post, row and element options
    $default_post_options = new Eddditor_Options('post');
    $default_row_options = new Eddditor_Options('row');
    $default_col_options = new Eddditor_Options('col');
    $default_element_options = new Eddditor_Options('element');
    
    
    // fetch content structure for the current post
    $post_id = get_the_ID();
    $content_structure = new Eddditor_Post($post_id);


    // fetch post layouts and element templates
    $saved_layouts = Eddditor_Layouts::get_all();
    $saved_templates = Eddditor_Templates::get_all();
    
    
    // inject data for use with Javascript
    wp_localize_script(
        'eddditor',
        'eddditorData',
        array(
            'postID' => $post_id,
            'contentStructure' => $content_structure->to_array(),
            'allowedRowLayouts' => $allowed_row_layouts,
            'defaultRowLayout' => $default_row_layout,
            'savedLayouts' => $saved_layouts,
            'savedTemplates' => $saved_templates,
            'options' => array(
                'post' => array(
                    'enabled' => $default_post_options->is_enabled(),
                    'defaults' => $default_post_options->get_clean_values(),
                ),
                'row' => array(
                    'enabled' => $default_row_options->is_enabled(),
                    'defaults' => $default_row_options->get_clean_values(),
                ),
                'col' => array(
                    'enabled' => $default_col_options->is_enabled(),
                    'defaults' => $default_col_options->get_clean_values(),
                ),
                'element' => array(
                    'enabled' => $default_element_options->is_enabled(),
                    'defaults' => $default_element_options->get_clean_values(),
                )
            ),
            'i18n' => array(
                'delete_row' => __('Delete row', 'eddditor'),
                'delete_element' => __('Delete element', 'eddditor'),
                'delete_template' => __('Delete favorite', 'eddditor'),
                'cancel' => __('Cancel', 'eddditor'),
                'discard_changes' => __('Discard changes', 'eddditor'),
                'discard_changes_confirmation' => __('Do you want to cancel and discard all changes?', 'eddditor'),
                'delete_row_confirmation' => __('Do you want to delete this row and all its elements? This action can not be undone.', 'eddditor'),
                'delete_element_confirmation' => __('Do you want to delete this element? This action can not be undone.', 'eddditor'),
                'delete_template_confirmation' => __('Do you want to delete this template? This action can not be undone.', 'eddditor'),
                'save_new_layout_confirmation' => __('Please enter a name for your layout:', 'eddditor'),
                'save_layout' => __('Save layout', 'eddditor'),
                'rename_layout_confirmation' => __('Please enter the new name for this layout:', 'eddditor'),
                'rename_layout' => __('Rename layout', 'eddditor'),
                'delete_layout_confirmation' => __('Do want to delete this layout? This action can not be undone.', 'eddditor'),
                'delete_layout' => __('Delete layout', 'eddditor'),
                'load_layout_confirmation' => __('Do want to load this layout and overwrite the existing content? This action can not be undone.', 'eddditor'),
                'load_layout' => __('Load layout', 'eddditor'),
            )
        )
    );

}