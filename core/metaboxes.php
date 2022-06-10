<?php

class WPM_Gutenberg_MetaBoxes {
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
    }

    /**
     * Add MetaBox for copy content to another languages
     * @version 1.3.0
     */
    public function add_meta_boxes($post_type) {
        if (('attachment' !== $post_type ) && null !== wpm_get_post_config( $post_type) && isset($_GET['post'])) {
            add_meta_box( "wpm-{$post_type}-wpm-guttenber-copy", __( 'Multilang Content Copy', 'wpm-guttenberg' ),array($this, 'copy_metabox'), $post_type, 'side' );
        }
    }

    /**
     * Show metabox for content copy
     * @version 1.3.0
     */
    public function copy_metabox( $post ) {
        $languages = wpm_get_languages();
        $current = wpm_get_language();
        ?>                
            <h4><?php _e( 'Copy to language:', 'wpm-guttenberg' ); ?></h4>
            <div wpm-guttenberg-copy>
                <input type="hidden" name="wpm_guttenberg_nonce" value="<?php echo wp_create_nonce('wpm_guttenberg_nonce'); ?>" />
                <input name="wpm-g-current" class="hidden" type="hidden" value="<?php echo $current; ?>"/>
                <select style="margin-bottom: 2px">
                    <?php
                        $first = true;
                        foreach($languages as $key => $lang) 
                        {
                            if($current == $key)
                                continue;
                    ?>
                        <option value="<?php echo $key; ?>" <?php echo $first ? 'selected' : ''; ?>><?php echo $lang['name']; ?></option>
                    <?php
                            $first = false;
                        }
                    ?>
                </select>
                <button class="components-button is-primary" style="padding: 0px 12px; height: 32px;">Copy</button>
            </div>
        <?php
    }
}

?>