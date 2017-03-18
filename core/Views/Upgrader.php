<?php

namespace Layotter\Views;

/**
 * View for the main drag-and-drop editor
 */
class Upgrader {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Layotter Database Upgrade', 'layotter'); ?></h2>
            <p class="layotter-settings-paragraph">
                <strong><?php _e('A database upgrade is required to continue using Layotter.', 'layotter'); ?></strong>
            </p>
            <p class="layotter-settings-paragraph">
                <?php _e('Your site is still working normally for your visitors. But to continue making changes, a database upgrade is required.', 'layotter'); ?><?php _e('The upgrade is fully automatic, but may take a while to complete, depending on the amount of content your site has.', 'layotter'); ?>
            </p>
            <p class="layotter-settings-paragraph layotter-with-icon">
                <i class="fa fa-warning"></i>
                <?php
                _e("Please backup your database before you start. If something goes wrong, there's no built-in way to undo the upgrade!", 'layotter');
                ?>
            </p>
            <p class="layotter-settings-paragraph">
                <a href="#" class="button button-large button-primary"><?php _e('Run the upgrade now!', 'layotter'); ?></a>
            </p>
        </div>
        <?php
    }
}
