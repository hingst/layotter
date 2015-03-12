<?php


/**
 * Enqueue styles and scripts, provide l10n for use with Javascript
 */
add_action('admin_head', 'eddditor_assets_admin_head');
function eddditor_assets_admin_head() {

    // load assets only if necessary
    if (!Eddditor::is_enabled()) {
        return;
    }


    // styles
    wp_enqueue_style(
        'eddditor',
        plugins_url('css/editor.css', __FILE__)
    );
    wp_enqueue_style(
        'eddditor-font-awesome',
        plugins_url('css/font-awesome.min.css', __FILE__)
    );


    // jQuery plugin used to serialize form data
    wp_enqueue_script(
        'eddditor-serialize',
        plugins_url('js/vendor/jquery.serialize-object.compiled.js', __FILE__),
        array('jquery')
    );
    
    
    // angular core
    wp_enqueue_script(
        'angular',
        plugins_url('js/vendor/angular.js', __FILE__)
    );
    wp_enqueue_script(
        'angular-animate',
        plugins_url('js/vendor/angular-animate.js', __FILE__)
    );
    wp_enqueue_script(
        'angular-sanitize',
        plugins_url('js/vendor/angular-sanitize.js', __FILE__)
    );


    // angular 3rd party
    wp_enqueue_script(
        'angular-ui-sortable',
        plugins_url('js/vendor/angular-ui-sortable.js', __FILE__)
    );
    
    
    // angular app
    wp_enqueue_script(
        'eddditor',
        plugins_url('js/app/app.js', __FILE__)
    );
    
    
    // angular controllers
    wp_enqueue_script(
        'eddditor-controller-editor',
        plugins_url('js/app/controllers/editor.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-controller-templates',
        plugins_url('js/app/controllers/templates.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-controller-form',
        plugins_url('js/app/controllers/form.js', __FILE__)
    );
    
    
    // angular services
    wp_enqueue_script(
        'eddditor-service-state',
        plugins_url('js/app/services/state.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-data',
        plugins_url('js/app/services/data.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-content',
        plugins_url('js/app/services/content.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-templates',
        plugins_url('js/app/services/templates.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-interface',
        plugins_url('js/app/services/view.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-forms',
        plugins_url('js/app/services/forms.js', __FILE__)
    );
    wp_enqueue_script(
        'eddditor-service-modals',
        plugins_url('js/app/services/modals.js', __FILE__)
    );
    
    
    // fetch allowed row layouts and default layout
    $allowed_row_layouts = Eddditor_Settings::get_allowed_row_layouts();
    $default_row_layout = Eddditor_Settings::get_default_row_layout();
    
    
    // fetch default values for post, row and element options
    $default_post_options = new Eddditor_Options('post');
    $default_row_options = new Eddditor_Options('row');
    $default_element_options = new Eddditor_Options('element');
    
    
    // fetch content structure for the current post
    $content_structure = Eddditor::get_content_structure(get_the_ID(), 'backend');
    
    
    // fetch saved element templates (aka element gallery)
    $element_templates = Eddditor_Templates::get_all();
    
    
    // inject data for use with Javascript
    wp_localize_script(
        'eddditor',
        'eddditorData',
        array(
            'contentStructure' => $content_structure,
            'allowedRowLayouts' => $allowed_row_layouts,
            'defaultRowLayout' => $default_row_layout,
            'options' => array(
                'post' => array(
                    'enabled' => $default_post_options->is_enabled(),
                    'defaults' => $default_post_options->get('data'),
                ),
                'row' => array(
                    'enabled' => $default_row_options->is_enabled(),
                    'defaults' => $default_row_options->get('data'),
                ),
                'element' => array(
                    'enabled' => $default_element_options->is_enabled(),
                    'defaults' => $default_element_options->get('data'),
                )
            ),
            'savedTemplates' => $element_templates,
            'i18n' => array(
                'delete_row' => __('Delete row', 'eddditor'),
                'delete_element' => __('Delete element', 'eddditor'),
                'delete_template' => __('Delete favorite', 'eddditor'),
                'cancel' => __('Cancel', 'eddditor'),
                'discard_changes' => __('Discard changes', 'eddditor'),
                'discard_changes_confirmation' => __('Are you sure you want to cancel and discard all changes?', 'eddditor'),
                'delete_row_confirmation' => __('Are you sure you want to delete this row and all its elements? This action can not be undone.', 'eddditor'),
                'delete_element_confirmation' => __('Are you sure you want to delete this element? This action can not be undone.', 'eddditor'),
                'delete_template_confirmation' => __('Are you sure you want to delete this favorite? This action can not be undone.', 'eddditor')
            )
        )
    );

}