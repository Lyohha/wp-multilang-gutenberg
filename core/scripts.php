<?php

class WPM_Gutenberg_Scripts {
    private $dir;

    public function __construct($dir) {
        $this->dir = $dir;
        add_action('admin_enqueue_scripts', array($this,'admin_scripts'));
    }

    /**
     * Add scripts for post edit page
     * @version 1.3.0
     */
    public function admin_scripts() {
        wp_enqueue_script('wpm_guttenberg-main', plugin_dir_url($this->dir) . 'wp-multilang-gutenberg/assets/scripts/main.js', array('jquery'), '1.0');
    }
}

?>