<?php


/**
 * Manages element templates
 */
class Eddditor_Templates {


    /**
     * Get template data for a given template ID
     *
     * @param string $id Template ID
     * @return mixed Template data on success, false on failure
     */
    public static function get($id) {
        $templates = get_option('eddditor_element_templates');

        if (isset($templates[$id])) {
            return $templates[$id];
        }

        return false;
    }


    /**
     * Get blank element instances for all saved templates
     *
     * @return array Element instances for all templates
     */
    public static function get_all() {
        $templates = array();
	    $templates[0] = new stdClass();
        $saved_templates = get_option('eddditor_element_templates');
        if (!is_array($saved_templates)) {
            $saved_templates = array();
        }

        foreach ($saved_templates as $id => $template) {
            $template_object = Eddditor_Templates::create_element($id);
            if ($template_object) {
                $templates[$id] = $template_object->get('data');
            } else {
                $templates[$id] = new stdClass();
            }
        }

        return $templates;
    }


    /**
     * Save a new template
     *
     * @param array $data Element data (keys: type, values)
     * @return string New template's ID
     */
    public static function save($data) {
        $templates = get_option('eddditor_element_templates');
        $id = count($templates) + 1; // 0 is not present
        $templates[$id] = $data;
        update_option('eddditor_element_templates', $templates);
        return $id;
    }


    /**
     * Update an existing template's data
     *
     * @param string $id Template ID
     * @param array $data Element data (keys: type, values)
     * @return bool True if template has been updated, false on failure
     */
    public static function update($id, $data) {
        $templates = get_option('eddditor_element_templates');

        if (isset($templates[$id])) {
            $templates[$id] = $data;
            update_option('eddditor_element_templates', $templates);
            return true;
        }

        return false;
    }


    /**
     * Delete an existing template
     *
     * @param string $id Template ID
     * @return bool True if template has been deleted, false on failure
     */
    public static function delete($id) {
        $templates = get_option('eddditor_element_templates');

        if (isset($templates[$id])) {
            $templates[$id]['disabled'] = true;
            update_option('eddditor_element_templates', $templates);
            return true;
        }

        return false;
    }


    /**
     * Create a new element instance from a saved template
     *
     * @param string $id Template ID
     * @param array $options Option values
     * @return mixed New element instance, or false on failure
     */
    public static function create_element($id, $options = array()) {
        $templates = get_option('eddditor_element_templates');
        if (!is_array($templates) OR !isset($templates[$id]) OR !is_array($templates[$id])) {
            return false;
        }

        $type = $templates[$id]['type'];
        $values = $templates[$id]['values'];

        $element = Eddditor::create_element($type, $values, $options);
        if (!$element) {
            return false;
        }

	    if (!isset($templates[$id]['disabled'])) {
		    $element->set_template($id);
	    }
        return $element;
    }


}