<?php

class WPM_Gutenberg_Ajax {
    private $content_copy = '';
    private $lang_to = '';

    public function __construct() {
        add_action('wp_ajax_wpm_guttenber_copy', array($this,'wpm_guttenber_copy'));
    }

    /**
     * Copy content to language
     * @version 1.3.0
     */
    public function wpm_guttenber_copy() {
        if(check_ajax_referer('wpm_guttenberg_nonce') === false) {
            $result = array(
                'result'    => 'error',
            );
    
            echo json_encode($result);
            die;
        }

        if(!isset($_POST['id']) || !isset($_POST['from']) || !isset($_POST['to'])) {
            $result = array(
                'result'    => 'error',
            );
    
            echo json_encode($result);
            die;
        }

        $post = get_post($_POST['id']);

        if($post == null) {
            $result = array(
                'result'    => 'error',
            );
    
            echo json_encode($result);
            die;
        }

        $content = wpm_translate_string($post->post_content, $_POST['from']);

        $content = wp_slash($content);

        $this->content_copy = $content;
        $this->lang_to = $_POST['to'];

        add_filter('wp_insert_post_data', array($this, 'wpm_guttenber_copy_save_post'), 100, 2);

        wp_update_post(wp_slash(array(
            'ID'            => $_POST['id'],
            'post_content'  => $content,
        )));

        $result = array(
            'result'    => 'ok',
            'post'      => $content,
        );

        echo json_encode($result);
        die;
    }

    /**
     * Filter for insert copy data
     * @version 1.3.0
     */
    public function wpm_guttenber_copy_save_post($data, $postarr) {
        $post_id = isset( $data['ID'] ) ? wpm_clean( $data['ID'] ) : ( isset( $postarr['ID'] ) ? wpm_clean( $postarr['ID'] ) : 0 );

        $post_config = wpm_get_post_config( $data['post_type'] );

        $post_field_config = apply_filters("wpm_post_{$data['post_type']}_field_post_content_config", $post_config['post_content'], $this->content_copy);
        $post_field_config = apply_filters("wpm_post_field_post_content_config", $post_field_config, $this->content_copy);

        $data['post_content'] = wpm_set_new_value($data['post_content'], $this->content_copy, $post_field_config, $this->lang_to);

        

        return $data;
    }
}

?>