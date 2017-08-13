<?php

//add_filter('layotter/view/element', 'layotter_test_element_filter', 10, 2);
function layotter_test_element_filter($view, $options) {
    return $view;
}