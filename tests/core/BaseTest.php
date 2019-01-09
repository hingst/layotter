<?php

namespace Layotter\Tests;

/**
 * Base class for tests
 */
abstract class BaseTest extends \WP_UnitTestCase {

    protected static $attachment_id;

    protected static function upload_attachment() {
        $upload_dir_info = wp_upload_dir();
        $file_path = $upload_dir_info['basedir'] . '/' . TESTS_UPLOAD_FILE_NAME;
        $file_info = wp_check_filetype(basename($file_path), null);

        $attachment = [
            'guid' => $upload_dir_info['url'] . '/' . basename($file_path),
            'post_mime_type' => $file_info['type'],
            'post_title' => 'Empty',
            'post_content' => '',
            'post_status' => 'publish'
        ];

        self::$attachment_id = wp_insert_attachment($attachment, $file_path);
    }

    protected static function delete_attachment() {
        wp_delete_attachment(self::$attachment_id);
    }
}