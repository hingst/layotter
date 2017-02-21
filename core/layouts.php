<?php


/**
 * Manages post layouts
 */
class Layotter_Layouts {


    /**
     * Makes sure all necessary fields are present and in the correct format
     *
     * @param array|null $structure Structure as fetched from the database
     * @return array Clean structure or empty array
     */
    private static function validate_structure($structure) {
        // deleted post layouts remain in the database as a null value
        if (!is_array($structure)) {
            return array();
        }

        if (!isset($structure['layout_id']) OR !is_int($structure['layout_id'])) {
            $structure['layout_id'] = -1;
        }

        if (!isset($structure['name']) OR !is_string($structure['name'])) {
            $structure['name'] = __('Unnamed layout', 'layotter');
        }

        if (!isset($structure['json']) OR !is_string($structure['json'])) {
            $structure['json'] = '';
        }

        if (!isset($structure['time_created']) OR !is_int($structure['time_created'])) {
            $structure['time_created'] = 0;
        }

        return $structure;
    }


    /**
     * Get a single post layout
     *
     * @param int $layout_id Layout ID to be fetched
     * @return bool|object Layotter_Post object, or false if the layout doesn't exist or was deleted
     */
    public static function get($layout_id) {
        $layouts = get_option('layotter_post_layouts');

        if (!is_array($layouts)) {
            return false;
        }

        // check if a layout exists for the ID
        // deleted layouts remain in the database as null values, so check with is_array()
        if (isset($layouts[$layout_id]) AND is_array($layouts[$layout_id])) {
            $layout = self::validate_structure($layouts[$layout_id]);
            $post = new Layotter_Post();
            $post->set_json($layout['json']);
            return $post;
        }

        return false;
    }


    /**
     * Get data of all post layouts
     *
     * Unlike self::get(), this returns post layouts' data as an array (instead of a Layotter_Post object)
     *
     * @return array Post layouts' data
     */
    public static function get_all() {
        $layouts = array();
        $saved_layouts = get_option('layotter_post_layouts');

        if (!is_array($saved_layouts)) {
            return array();
        }

        foreach ($saved_layouts as $layout) {
            $layout = self::validate_structure($layout);
            if (!empty($layout)) {
                $layouts[] = $layout;
            }
        }

        return $layouts;
    }


    /**
     * Save a new post layout to the database
     *
     * @param string $name Human-redable name for this layout
     * @param string $json JSON data for this layout
     * @return int|bool New layout ID, or false on failure
     */
    public static function save($name, $json) {
        $json_decoded = json_decode($json, true);
        if (!is_string($name) OR !is_array($json_decoded)) {
            return false;
        }

        $layouts = get_option('layotter_post_layouts');

        if (!is_array($layouts)) {
            $layouts = array();
        }

        $id = count($layouts);
        $layouts[$id] = array(
            'layout_id' => $id, // redundant, but simplifies handling in JS
            'name' => $name,
            'json' => $json,
            'time_created' => time()
        );

        update_option('layotter_post_layouts', $layouts);

        return $layouts[$id];
    }


    /**
     * Rename an existing post layout
     *
     * @param int $id Layout ID
     * @param string $name Human-readable new name
     * @return array|bool Array with new layout data, or false on failure
     */
    public static function rename($id, $name) {
        $layouts = get_option('layotter_post_layouts');

        if (!is_array($layouts)) {
            return false;
        }

        if (!is_int($id) OR !is_string($name)) {
            return false;
        }

        if (isset($layouts[$id]) AND is_array($layouts[$id])) {
            $layouts[$id]['name'] = $name;
            update_option('layotter_post_layouts', $layouts);
            return array(
                'name' => $name
            );
        }

        return false;
    }


    /**
     * Delete an existing post layout
     *
     * @param int $id Layout ID
     * @return bool Was the layout deleted?
     */
    public static function delete($id) {
        $layouts = get_option('layotter_post_layouts');

        if (!is_array($layouts)) {
            return false;
        }

        if (!is_int($id)) {
            return false;
        }

        if (isset($layouts[$id])) {
            $layouts[$id] = null;
            update_option('layotter_post_layouts', $layouts);
            return true;
        }

        return false;
    }


}