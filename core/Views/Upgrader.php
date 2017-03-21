<?php

namespace Layotter\Views;

/**
 * View for the database upgrade page
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
                <?php _e('Your site is still working normally for your visitors. But to continue making changes, you need to upgrade the database. The upgrade is fully automatic, but may take a while to complete, depending on the amount of content your site has.', 'layotter'); ?>
            </p>
            <div id="layotter-upgrade-button-wrapper">
                <p class="layotter-settings-paragraph layotter-with-icon layotter-important">
                    <i class="fa fa-warning"></i>
                    <?php
                    _e("Please backup your database before you start. If something goes wrong, there's no built-in way to undo the upgrade!", 'layotter');
                    ?>
                </p>
                <p class="layotter-settings-paragraph">
                    <button class="button button-large button-danger" id="layotter-upgrade-button"><?php _e('Run the upgrade now!', 'layotter'); ?></button>
                </p>
            </div>
            <div id="layotter-upgrade-loading-wrapper">
                <h3><?php _e('Running upgrade', 'layotter'); ?></h3>
                <div id="layotter-upgrade-loading">
                    <span id="layotter-upgrade-loading-bar"></span>
                    <span id="layotter-upgrade-loading-percent">30%</span>
                </div>
                <ul id="layotter-upgrade-tasks">
                </ul>
            </div>
            <div id="layotter-upgrade-complete-wrapper">
                <h3><?php _e('Upgrade complete', 'layotter'); ?></h3>
                <p class="layotter-settings-paragraph">
                    <?php _e("Your database has been upgraded and everything's back to normal. Happy editing!", 'layotter'); ?>
                </p>
            </div>
        </div>
        <?php
    }
}
