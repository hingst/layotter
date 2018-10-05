<?php

define('ABSPATH', TESTS_WP_PATH);

define('DB_NAME', TESTS_WP_DB_NAME);
define('DB_USER', TESTS_WP_DB_USER);
define('DB_PASSWORD', TESTS_WP_DB_PASSWORD);
define('DB_HOST', TESTS_WP_DB_HOST);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT', 'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT', 'put your unique phrase here');
define('NONCE_SALT', 'put your unique phrase here');

$table_prefix = 'wp_';

define('WP_TESTS_DOMAIN', 'localhost');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_TESTS_TITLE', 'Dummy');

define('WP_DEBUG', true);
