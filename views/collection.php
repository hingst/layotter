<?php

/**
 * Include views HTML
 */
add_action('admin_footer', 'layotter_views_admin_footer');
function layotter_views_admin_footer() {
    if (!Layotter::is_enabled()) {
        return;
    }

    ?>
    <script type="text/ng-template" id="layotter-form">
        <?php

        require_once __DIR__ . '/form.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-add-element">
        <?php

        require_once __DIR__ . '/add-element.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-load-layout">
        <?php

        require_once __DIR__ . '/load-layout.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-modal-confirm">
        <?php

        require_once __DIR__ . '/confirm.php';

        ?>
    </script>
    <script type="text/ng-template" id="layotter-modal-prompt">
        <?php

        require_once __DIR__ . '/prompt.php';

        ?>
    </script>
    <?php

    require_once __DIR__ . '/templates.php';
}