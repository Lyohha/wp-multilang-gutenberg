<?php

class WPM_Gutenberg_ACF {
    public function __construct() {
        add_action( 'acf/update_value', array( $this, 'update_value' ), 100, 3);
    }

    /**
     * Clear translate tag from block editor fields.
     * @version 1.2.0
     */
    public function update_value($value, $post_id, $field) {
        if(strpos($post_id, 'block') !== false) {
            if($field['type'] == 'wysiwyg' || $field['type'] == 'text' || $field['type'] == 'textarea' || $field['type'] == 'number' || $field['type'] == 'url' || $field['type'] == 'password') {
                $value = preg_replace('#\[:([a-z-]*)\]#im', '', $value);
            }
        }

        return $value;
    }
}

?>