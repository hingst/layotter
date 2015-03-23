<?php


/**
 * Manages post layouts
 */
class Eddditor_Layouts {


    private static function validate_structure($structure) {
        if (!is_array($structure)) {
            return false;
        }

        if (!isset($structure['layout_id']) OR !is_int($structure['layout_id'])) {
            $structure['layout_id'] = -1;
        }

        if (!isset($structure['name']) OR !is_string($structure['name'])) {
            $structure['name'] = __('Unnamed layout', 'eddditor');
        }

        if (!isset($structure['json']) OR !is_string($structure['json'])) {
            $structure['json'] = '';
        }

        if (!isset($structure['time_created']) OR !is_int($structure['time_created'])) {
            $structure['time_created'] = 0;
        }

        return $structure;
    }


    public static function get($layout_id) {
        $layouts = get_option('eddditor_post_layouts');
        if (!is_array($layouts)) {
            return false;
        }

        foreach ($layouts as $layout) {
            $layout = self::validate_structure($layout);
            if ($layout['layout_id'] == $layout_id) {
                $post = new Eddditor_Post($layout['json']);
                return $post;
            }
        }

        return false;
    }


    public static function get_all() {
        $layouts = array();
        $saved_layouts = get_option('eddditor_post_layouts');

        if (!is_array($saved_layouts)) {
            $saved_layouts = array();
        }

        foreach ($saved_layouts as $layout) {
            $layout = self::validate_structure($layout);
            if ($layout) {
                $layouts[] = $layout;
            }
        }

        return $layouts;
    }


    public static function save($name, $json) {
        $json_decoded = json_decode($json, true);
        if (!is_string($name) OR !is_array($json_decoded)) {
            return false;
        }

        $layouts = get_option('eddditor_post_layouts');
        if (!is_array($layouts)) {
            $layouts = array();
        }

        $id = count($layouts);
        $layouts[$id] = array(
            'layout_id' => $id,
            'name' => $name,
            'json' => $json,
            'time_created' => time() // converted in Javascript, use microtime() instead of time()
        );

        update_option('eddditor_post_layouts', $layouts);

        return $layouts[$id];
    }


    public static function rename($id, $name) {
        $layouts = get_option('eddditor_post_layouts');

        if (!is_int($id)) {
            return false;
        }

        if (!is_string($name)) {
            return false;
        }

        if (isset($layouts[$id])) {
            $layouts[$id]['name'] = $name;
            update_option('eddditor_post_layouts', $layouts);
            return array(
                'name' => $name
            );
        }

        return false;
    }


    public static function delete($id) {
        $layouts = get_option('eddditor_post_layouts');

        if (!is_int($id)) {
            return false;
        }

        if (isset($layouts[$id])) {
            $layouts[$id] = null;
            update_option('eddditor_post_layouts', $layouts);
            return true;
        }

        return false;
    }


}