<?php

namespace Layotter;

use Exception;
use Layotter\Repositories\ElementRepository;
use Layotter\Repositories\LayoutRepository;
use Layotter\Repositories\ElementTypeRepository;
use Layotter\Repositories\OptionsRepository;
use Layotter\Repositories\PostRepository;
use Layotter\Serialization\ElementSerializer;
use Layotter\Serialization\ElementTypeSerializer;
use Layotter\Serialization\LayoutSerializer;
use Layotter\Serialization\PostSerializer;
use Layotter\Services\OptionsFieldsService;
use Layotter\Views\AddElement;
use Layotter\Views\Confirm;
use Layotter\Views\Form;
use Layotter\Views\LoadLayout;
use Layotter\Views\Prompt;
use Layotter\Views\Templates;

class Assets {

    public static function enqueue_backend_assets() {
        if (!Editor::is_enabled_for_screen()) {
            return;
        }

        wp_enqueue_style('layotter', plugins_url('assets/css/editor.min.css', __DIR__));
        wp_enqueue_style('layotter-font-awesome', plugins_url('assets/css/font-awesome.min.css', __DIR__));

        wp_enqueue_script('app', plugins_url('assets/js/vue.min.js', __DIR__), [], false, true);

        /*
        $scripts = [
            'angular' => 'assets/js/vendor/angular.js',
            'angular-animate' => 'assets/js/vendor/angular-animate.js',
            'angular-sanitize' => 'assets/js/vendor/angular-sanitize.js',
            'angular-ui-sortable' => 'assets/js/vendor/angular-ui-sortable.js',
            //'layotter' => 'assets/js/app.min.js',
            'layotter' => 'assets/js/app/app.js',
            'layotter-c-editor' => 'assets/js/app/controllers/editor.js',
            'layotter-c-form' => 'assets/js/app/controllers/form.js',
            'layotter-c-templates' => 'assets/js/app/controllers/templates.js',
            'layotter-s-content' => 'assets/js/app/services/content.js',
            'layotter-s-data' => 'assets/js/app/services/data.js',
            'layotter-s-forms' => 'assets/js/app/services/forms.js',
            'layotter-s-history' => 'assets/js/app/services/history.js',
            'layotter-s-layouts' => 'assets/js/app/services/layouts.js',
            'layotter-s-modals' => 'assets/js/app/services/modals.js',
            'layotter-s-state' => 'assets/js/app/services/state.js',
            'layotter-s-templates' => 'assets/js/app/services/templates.js',
            'layotter-s-view' => 'assets/js/app/services/view.js',
        ];

        foreach ($scripts as $name => $path) {
            wp_enqueue_script($name, plugins_url($path, __DIR__));
        }
        */
    }

    /**
     * @throws Exception
     */
    public static function backend_localization() {
        if (!Editor::is_enabled_for_screen()) {
            return;
        }

        $id = get_the_ID();
        $post_type = get_post_type();
        $post = PostRepository::load($id);

        $post_options = OptionsRepository::create('post');
        $row_options = OptionsRepository::create('row');
        $col_options = OptionsRepository::create('col');
        $element_options = OptionsRepository::create('element');
        $post_options->set_post_type_context($post_type);
        $row_options->set_post_type_context($post_type);
        $col_options->set_post_type_context($post_type);
        $element_options->set_post_type_context($post_type);

        $saved_layouts = array_map(function($model) {
            return new LayoutSerializer($model);
        }, LayoutRepository::get_allowed_for_post_type($post_type));

        $saved_templates = array_map(function($model) {
            return new ElementSerializer($model);
        }, ElementRepository::get_available_templates_for_post($id));

        $element_types = array_map(function($model) {
            return new ElementTypeSerializer($model);
        }, ElementTypeRepository::get_allowed_for_post($id));

        $data = [
            'postID' => $id,
            'postType' => $post_type,
            'contentStructure' => new PostSerializer($post),
            'allowedRowLayouts' => Settings::get_allowed_row_layouts(),
            'defaultRowLayout' => Settings::get_default_row_layout(),
            'savedLayouts' => $saved_layouts,
            'savedTemplates' => $saved_templates,
            'enablePostLayouts' => Settings::post_layouts_enabled(),
            'enableElementTemplates' => Settings::element_templates_enabled(),
            'elementTypes' => $element_types,
            'isOptionsEnabled' => [
                'post' => OptionsFieldsService::has_fields($post_options),
                'row' => OptionsFieldsService::has_fields($row_options),
                'col' => OptionsFieldsService::has_fields($col_options),
                'element' => OptionsFieldsService::has_fields($element_options)
            ],
            'i18n' => [
                'delete_row' => __('Delete row', 'layotter'),
                'delete_element' => __('Delete element', 'layotter'),
                'delete_template' => __('Delete template', 'layotter'),
                'edit_template' => __('Edit template', 'layotter'),
                'cancel' => __('Cancel', 'layotter'),
                'discard_changes' => __('Discard changes', 'layotter'),
                'discard_changes_confirmation' => __('Do you want to cancel and discard all changes?', 'layotter'),
                'discard_changes_go_back_confirmation' => __('Do you want to go back and discard all changes?', 'layotter'),
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
                'history' => [
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
                ],
                'upgrades' => [
                    'confirm' => __('Please confirm that you have a created a database backup and want to run the upgrade now.', 'layotter'),
                    'posts' => __('Updating posts', 'layotter'),
                    'layouts' => __('Updating post layouts', 'layotter'),
                    'templates' => __('Updating element templates', 'layotter'),
                ]
            ]
        ];

        echo '<script>window.layotterData = ' . json_encode($data) . '</script>';
    }

    public static function enqueue_frontend_assets() {
        if (!is_admin() && Settings::default_css_enabled()) {
            wp_enqueue_style('layotter-frontend', plugins_url('assets/css/frontend.min.css', __DIR__));
        }
    }

    public static function print_views() {
        if (!Editor::is_enabled_for_screen()) {
            return;
        }

        ?>
        <script type="text/ng-template" id="layotter-form">
            <?php Form::view(); ?>
        </script>
        <script type="text/ng-template" id="layotter-add-element">
            <?php AddElement::view(); ?>
        </script>
        <script type="text/ng-template" id="layotter-load-layout">
            <?php LoadLayout::view() ?>
        </script>
        <script type="text/ng-template" id="layotter-modal-confirm">
            <?php Confirm::view(); ?>
        </script>
        <script type="text/ng-template" id="layotter-modal-prompt">
            <?php Prompt::view(); ?>
        </script>
        <?php

        Templates::view();
    }
}
