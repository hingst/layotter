<?php

namespace Layotter\ElementTypes;

use InvalidArgumentException;
use Layotter\Editor;
use Layotter\Models\ElementType;

/**
 * Base class for all element types.
 */
abstract class BaseElementType {

    /**
     * @var string Unique type name, must not ever be changed in derived classes
     */
    protected $name;

    /**
     * @var string Human-readable title
     */
    protected $title;

    /**
     * @var string Human-readable description
     */
    protected $description;

    /**
     * @var string Icon name from the Font Awesome set
     */
    protected $icon;

    /**
     * @var int|string ACF field group (ID or slug)
     */
    protected $field_group;

    /**
     * @var int Order relative to other element types
     */
    protected $order = 0;

    /**
     * @var bool Used to make sure that assets are only enqueued once
     */
    protected static $frontend_assets_enqueued = false;

    /**
     * Must set $this->title, $this->description, $this->icon and $this->field_group. Can set $this->order to override
     * alphabetical ordering in the "Add Element" screen.
     */
    abstract protected function attributes();

    /**
     * Should print element's backend HTML.
     *
     * @param array $fields
     */
    abstract protected function backend_view($fields);

    /**
     * Should print element's frontend HTML.
     *
     * @param array $fields
     */
    abstract protected function frontend_view($fields);

    /**
     * Can be overwritten to enqueue scripts and styles for the backend.
     */
    public static function backend_assets() {
    }

    /**
     * Can be overwritten to enqueue scripts and styles for the frontend.
     */
    public static function frontend_assets() {
    }

    /**
     * @param $type
     */
    public function __construct($type) {
        if (!is_string($type)) {
            throw new InvalidArgumentException();
        }

        $this->name = $type;
        $this->attributes();
        self::register_frontend_hooks();
    }

    /**
     * Called when an element type is registered, allows enqueuing scripts and styles in the backend.
     */
    public static function register_backend_hooks() {
        add_action('admin_footer', [get_called_class(), 'register_backend_hooks_internal']);
    }

    public static function register_backend_hooks_internal() {
        if (Editor::is_enabled_for_screen()) {
            call_user_func([get_called_class(), 'backend_assets']);
        }
    }

    /**
     * Called when an element type is instanciated, allows enqueuing scripts and styles in the frontend.
     */
    public static function register_frontend_hooks() {
        if (!self::$frontend_assets_enqueued && !is_admin()) {
            call_user_func([get_called_class(), 'frontend_assets']);
            self::$frontend_assets_enqueued = true;
        }
    }

    /**
     * @return string
     */
    public function render_backend_view() {
        ob_start();
        $args = func_get_args();
        call_user_func_array([$this, 'backend_view'], $args);
        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function render_frontend_view() {
        ob_start();
        $args = func_get_args();
        call_user_func_array([$this, 'frontend_view'], $args);
        return ob_get_clean();
    }

    /**
     * @return ElementType
     */
    public function get_model() {
        return new ElementType($this->name, $this->title, $this->description, $this->icon, $this->order);
    }

    /**
     * @return int|string
     */
    public function get_field_group() {
        return $this->field_group;
    }
}