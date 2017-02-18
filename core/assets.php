<?php


/**
 * Enqueue styles and scripts, provide l10n for use with Javascript
 */
add_action('admin_enqueue_scripts', 'layotter_assets_admin_enqueue_scripts');
function layotter_assets_admin_enqueue_scripts() {

    // load assets only if necessary
    if (!Layotter::is_enabled()) {
        return;
    }


    // styles
    wp_enqueue_style(
        'layotter',
        plugins_url('assets/css/editor.css', __DIR__)
    );
    wp_enqueue_style(
        'layotter-font-awesome',
        plugins_url('assets/css/font-awesome.min.css', __DIR__)
    );


    // jQuery plugin used to serialize form data
    wp_enqueue_script(
        'layotter-serialize',
        plugins_url('assets/js/vendor/jquery.serialize-object.compiled.js', __DIR__),
        array('jquery')
    );


    // Angular scripts
    $scripts = array(
        'angular' => 'assets/js/vendor/angular.js',
        'angular-animate' => 'assets/js/vendor/angular-animate.js',
        'angular-sanitize' => 'assets/js/vendor/angular-sanitize.js',
        'angular-ui-sortable' => 'assets/js/vendor/angular-ui-sortable.js',
        'layotter' => 'assets/js/app/app.js',
        'layotter-controller-editor' => 'assets/js/app/controllers/editor.js',
        'layotter-controller-templates' => 'assets/js/app/controllers/templates.js',
        'layotter-controller-form' => 'assets/js/app/controllers/form.js',
        'layotter-service-state' => 'assets/js/app/services/state.js',
        'layotter-service-data' => 'assets/js/app/services/data.js',
        'layotter-service-content' => 'assets/js/app/services/content.js',
        'layotter-service-templates' => 'assets/js/app/services/templates.js',
        'layotter-service-layouts' => 'assets/js/app/services/layouts.js',
        'layotter-service-view' => 'assets/js/app/services/view.js',
        'layotter-service-forms' => 'assets/js/app/services/forms.js',
        'layotter-service-modals' => 'assets/js/app/services/modals.js',
        'layotter-service-history' => 'assets/js/app/services/history.js'
    );
    foreach ($scripts as $name => $path) {
        wp_enqueue_script(
            $name,
            plugins_url($path, __DIR__)
        );
    }
    
    
    // fetch allowed row layouts and default layout
    $allowed_row_layouts = Layotter_Settings::get_allowed_row_layouts();
    $default_row_layout = Layotter_Settings::get_default_row_layout();
    
    
    // fetch default values for post, row and element options
    $default_post_options = Layotter::assemble_new_options('post');
    $default_row_options = Layotter::assemble_new_options('row');
    $default_col_options = Layotter::assemble_new_options('col');
    $default_element_options = Layotter::assemble_new_options('element');
    
    
    // fetch content structure for the current post
    $post_id = get_the_ID();
    $content_structure = new Layotter_Post($post_id);


    // fetch post layouts and element templates
    $saved_layouts = Layotter_Layouts::get_all();
    $saved_templates = Layotter_Templates::get_all_for_post($post_id);


    // fetch available element types
    $element_objects = Layotter::get_filtered_element_types($post_id);
    $element_types = array();

    foreach ($element_objects as $element_object) {
        $element_types[] = $element_object->get_metadata();
    }


    // fetch general settings
    $enable_post_layouts = Layotter_Settings::post_layouts_enabled();
    $enable_element_templates = Layotter_Settings::element_templates_enabled();

    
    // inject data for use with Javascript
    wp_localize_script(
        'layotter',
        'layotterData',
        array(
            'postID' => $post_id,
            'isACFPro' => Layotter_ACF::is_pro_installed(),
            'contentStructure' => $content_structure->to_array(),
            'allowedRowLayouts' => $allowed_row_layouts,
            'defaultRowLayout' => $default_row_layout,
            'savedLayouts' => $saved_layouts,
            'savedTemplates' => $saved_templates,
            'enablePostLayouts' => $enable_post_layouts,
            'enableElementTemplates' => $enable_element_templates,
            'elementTypes' => $element_types,
            'options' => array(
                'post' => array(
                    'enabled' => $default_post_options->is_enabled(),
                    'defaults' => $default_post_options->get_values(),
                ),
                'row' => array(
                    'enabled' => $default_row_options->is_enabled(),
                    'defaults' => $default_row_options->get_values(),
                ),
                'col' => array(
                    'enabled' => $default_col_options->is_enabled(),
                    'defaults' => $default_col_options->get_values(),
                ),
                'element' => array(
                    'enabled' => $default_element_options->is_enabled(),
                    'defaults' => $default_element_options->get_values(),
                )
            ),
            'i18n' => array(
                'delete_row' => __('Delete row', 'layotter'),
                'delete_element' => __('Delete element', 'layotter'),
                'delete_template' => __('Delete template', 'layotter'),
                'edit_template' => __('Edit template', 'layotter'),
                'cancel' => __('Cancel', 'layotter'),
                'discard_changes' => __('Discard changes', 'layotter'),
                'discard_changes_confirmation' => __('Do you want to cancel and discard all changes?', 'layotter'),
                'delete_row_confirmation' => __('Do you want to delete this row and all its elements?', 'layotter'),
                'delete_element_confirmation' => __('Do you want to delete this element?', 'layotter'),
                'delete_template_confirmation' => __('Do you want to delete this template? You can not undo this action.', 'layotter'),
                'edit_template_confirmation' => __('When editing a template, your changes will be reflected on all pages that are using it. Do you want to edit this template?', 'layotter'),
                'save_new_layout_confirmation' => __('Please enter a name for your layout:', 'layotter'),
                'save_layout' => __('Save layout', 'layotter'),
                'rename_layout_confirmation' => __('Please enter the new name for this layout:', 'layotter'),
                'rename_layout' => __('Rename layout', 'layotter'),
                'delete_layout_confirmation' => __('Do want to delete this layout? You can not undo this action.', 'layotter'),
                'delete_layout' => __('Delete layout', 'layotter'),
                'load_layout_confirmation' => __('Do want to load this layout and overwrite the existing content?', 'layotter'),
                'load_layout' => __('Load layout', 'layotter'),
                'history' => array(
                    'undo' => __('Undo:', 'layotter'),
                    'redo' => __('Redo:', 'layotter'),
                    'add_element' => __('Add element', 'layotter'),
                    'edit_element' => __('Edit element', 'layotter'),
                    'duplicate_element' => __('Duplicate element', 'layotter'),
                    'delete_element' => __('Delete element', 'layotter'),
                    'move_element' => __('Move element', 'layotter'),
                    'save_element_as_template' => __('Save element as template', 'layotter'),
                    'create_element_from_template' => __('Create element from template', 'layotter'),
                    'add_row' => __('Add row', 'layotter'),
                    'change_row_layout' => __('Change row layout', 'layotter'),
                    'duplicate_row' => __('Duplicate row', 'layotter'),
                    'delete_row' => __('Delete row', 'layotter'),
                    'move_row' => __('Move row', 'layotter'),
                    'edit_post_options' => __('Edit post options', 'layotter'),
                    'edit_row_options' => __('Edit row options', 'layotter'),
                    'edit_column_options' => __('Edit column options', 'layotter'),
                    'edit_element_options' => __('Edit element options', 'layotter'),
                    'load_post_layout' => __('Load layout', 'layotter')
                )
            )
        )
    );

}


/**
 * Include basic CSS in the frontend if enabled in settings
 */
add_action('wp_enqueue_scripts', 'layotter_frontend_assets');
function layotter_frontend_assets() {
    if (!is_admin() AND Layotter_Settings::default_css_enabled()) {
        wp_enqueue_style('layotter-frontend', plugins_url('assets/css/frontend.css', __DIR__));
    }
}