<?php

/**
 * Include view templates for Angular
 *
 * Templates that aren't dynamically changed
 */
add_action('admin_footer-post.php', 'layotter_views_admin_footer');
add_action('admin_footer-post-new.php', 'layotter_views_admin_footer');
function layotter_views_admin_footer() {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views/twig');
    $twig = new Twig_Environment($loader);

    $post_id = get_the_ID();
    $element_types = Layotter::get_filtered_element_types($post_id);
    $element_types_for_template = array();

    foreach ($element_types as $element_type) {
        $element_types_for_template[] = array(
            'type' => $element_type->get('type'),
            'title' => $element_type->get('title'),
            'description' => $element_type->get('description'),
            'icon' => $element_type->get('icon'),
        );
    }

    ?>
    <script type="text/ng-template" id="layotter-add-element">
        <?php

        echo $twig->render('add-element.twig', array(
            'title' => __('Add element', 'layotter'),
            'element_types' => $element_types_for_template,
            'cancel' => __('Cancel', 'layotter')
        ));

        ?>
    </script>
    <script type="text/ng-template" id="layotter-load-layout">
        <?php

        require_once __DIR__ . '/../views/load-layout.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-modal-confirm">
        <?php

        require_once __DIR__ . '/../views/confirm.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-modal-prompt">
        <?php

        require_once __DIR__ . '/../views/prompt.php';

        ?>
    </script>
    <?php

    require_once __DIR__ . '/../views/templates.php';
}