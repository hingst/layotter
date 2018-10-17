<?php

namespace Layotter\Tests;

/**
 * Sets up view filters for tests
 */
class ViewFilters {

    /**
     * Registers view filters
     */
    public static function register() {
        add_filter('layotter/view/element', [__CLASS__, 'element'], 10, 5);
        add_filter('layotter/view/column', [__CLASS__, 'column'], 10, 5);
        add_filter('layotter/view/row', [__CLASS__, 'row'], 10, 5);
        add_filter('layotter/view/post', [__CLASS__, 'post'], 10, 5);
    }

    /**
     * View filter for elements
     *
     * @param string $view
     * @param array $options
     * @param array $column_options
     * @param array $row_options
     * @param array $post_options
     * @return string
     */
    public static function element($view, $options, $column_options, $row_options, $post_options) {
        return '<div class="layotter-test-element">' . $options['option'] . '|' . $column_options['option'] . '|' . $row_options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
    }

    /**
     * View filter for columns
     *
     * @param string $view
     * @param string $class
     * @param array $options
     * @param array $row_options
     * @param array $post_options
     * @return string
     */
    public static function column($view, $class, $options, $row_options, $post_options) {
        return '<div class="layotter-test-column ' . $class . '">' . $options['option'] . '|' . $row_options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
    }

    /**
     * View filter for rows
     *
     * @param string $view
     * @param array $options
     * @param array $post_options
     * @return string
     */
    public static function row($view, $options, $post_options) {
        return '<div class="layotter-test-row">' . $options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
    }

    /**
     * View filter for posts
     *
     * @param string $view
     * @param array $options
     * @return string
     */
    public static function post($view, $options) {
        return '<div class="layotter-test-post">' . $options['option'] . '|' . $view . '</div>';
    }
}
