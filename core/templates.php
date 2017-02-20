<?php


/**
 * Manages element templates
 *
 * Not to be confused with "views"! In Layotter's terminology, "templates" refers to saved elements (the ones you can
 * save to the sidebar and reuse on other pages).
 */
class Layotter_Templates {

    /**
     * Get array representations of blank element instances for all saved templates that are available for a specific post
     *
     * @param int $post_id Post ID
     * @return array Array representations of element instances for all templates
     */
    public static function get_all_for_post($post_id) {
        $template_posts = get_posts(array(
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_key' => Layotter_Element::IS_TEMPLATE_META_FIELD,
            'meta_value' => '1'
        ));

        $templates = array();

        foreach ($template_posts as $template) {
            $element = Layotter::assemble_element($template->ID);
            if ($element->is_enabled_for($post_id)) {
                $templates[] = $element->to_array();
            }
        }

        return $templates;
    }

}