<?php

add_filter('layotter/view/element', 'layotter_test_element_filter', 10, 5);
function layotter_test_element_filter($view, $options, $column_options, $row_options, $post_options) {
    return '<div class="layotter-test-element">' . $options['option'] . '|' . $column_options['option'] . '|' . $row_options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
}

add_filter('layotter/view/column', 'layotter_test_column_filter', 10, 5);
function layotter_test_column_filter($view, $class, $options, $row_options, $post_options) {
    return '<div class="layotter-test-column ' . $class . '">' . $options['option'] . '|' . $row_options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
}

add_filter('layotter/view/row', 'layotter_test_row_filter', 10, 3);
function layotter_test_row_filter($view, $options, $post_options) {
    return '<div class="layotter-test-row">' . $options['option'] . '|' . $post_options['option'] . '|' . $view . '</div>';
}

add_filter('layotter/view/post', 'layotter_test_post_filter', 10, 2);
function layotter_test_post_filter($view, $options) {
    return '<div class="layotter-test-post">' . $options['option'] . '|' . $view . '</div>';
}