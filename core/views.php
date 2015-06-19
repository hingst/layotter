<?php

/**
 * Include views HTML
 */
add_action('admin_footer-post.php', 'layotter_views_admin_footer');
add_action('admin_footer-post-new.php', 'layotter_views_admin_footer');
function layotter_views_admin_footer() {
    ?>
    <script type="text/ng-template" id="layotter-form">
        <?php

        require_once __DIR__ . '/../views/form.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-add-element">
        <?php

        require_once __DIR__ . '/../views/add-element.php';

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