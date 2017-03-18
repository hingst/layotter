<?php

namespace Layotter\Views;

/**
 * Replaces the drag-and-drop editor if an upgrade is required
 */
class UpgradeNotice {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div id="layotter-upgrade-post">
            <div id="layotter-upgrade-post-icons">
                <i class="fa fa-database"></i>
            </div>
            <div id="layotter-upgrade-post-notice">
                <h1><?php _e('A database upgrade is required before you can edit this page.', 'layotter'); ?></h1>
                <p>
                    <?php _e('Your site is still working normally for your visitors. But to continue making changes, a database upgrade is required.', 'layotter'); ?>
                </p>
                <p>
                    <?php
                    if (current_user_can('activate_plugins')) {
                        ?>
                        <a href="<?php echo admin_url('admin.php?page=layotter-upgrade'); ?>" class="button button-large button-primary"><?php _e('Go to the upgrade page', 'layotter'); ?></a>
                        <?php
                    } else {
                        _e('Please ask the site admin to run the upgrade.', 'layotter');
                    }
                    ?>

                </p>
            </div>
        </div>
        <?php
    }
}
