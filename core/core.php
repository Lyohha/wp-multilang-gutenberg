<?php

require __DIR__ . '/acf.php';
require __DIR__ . '/update.php';
require __DIR__ . '/metaboxes.php';
require __DIR__ . '/scripts.php';
require __DIR__ . '/ajax.php';

class WPM_Gutenberg {

    public function __construct($dir, $version) {
        new WPM_Gutenberg_ACF();
        new WPM_Gutenberg_MetaBoxes();
        new WPM_Gutenberg_Update(plugin_basename($dir), $version);
        new WPM_Gutenberg_Scripts(plugin_basename($dir));
        new WPM_Gutenberg_Ajax();

        $this->setup();
    }

    private function setup() {
        add_action( 'init', array( $this, 'init' ), 99);
    }

    /**
     * Add new filter for post content
     */
    public function init() {
        $post_types = get_post_types(array('_builtin' => true));
        foreach($post_types as $post_type) {
            if($post_type == 'menu' || $post_type == 'attachment')
                continue;
            add_filter('rest_prepare_' . $post_type, array($this, 'rest_prepare'), 10, 3);
        }
    }

    /**
     * Change link in Gutenberg Editor
     * @version 1.1.0
     * @since 1.1.1
     */
    public function rest_prepare($response, $post, $request ) {

        if(isset($_GET['_locale']) && $_GET['_locale'] == 'user') {
            $user_id = get_post_meta($post->ID, '_edit_last', true);
            if($user_id !== false && $user_id !== '') {
                $lang = get_user_meta( $user_id, 'edit_lang', true );
                if($lang !== false && $lang != '') {
                    $response->data['link'] = $this->translate_url($response->data['link'], $lang);
                    /**
                     * Update
                     * @version 1.1.1
                     */
                    $response->data['permalink_template'] = $this->translate_url($response->data['permalink_template'], $lang);
                }
            }
        }

        return $response;
    }

    /**
     * Translate url without some checks
     * @version 1.1.0
     */
    function translate_url( $url, $language = '' ) {

        $host = wpm_get_orig_home_url();
    
        if ( strpos( $url, $host ) === false ) {
            return $url;
        }
        
        if ( is_admin_url( $url ) || preg_match( '/^.*\.php$/i', wp_parse_url( $url, PHP_URL_PATH ) ) ) {
            return add_query_arg( 'lang', $language, $url );
        }
    
        $url         = remove_query_arg( 'lang', $url );
        $default_uri = str_replace( $host, '', $url );
        $default_uri = $default_uri ? $default_uri : '/';
        $languages   = wpm_get_languages();
        $parts       = explode( '/', ltrim( trailingslashit( $default_uri ), '/' ) );
        $url_lang    = $parts[0];
    
        if ( isset( $languages[ $url_lang ] ) ) {
            $default_uri = preg_replace( '!^/' . $url_lang . '(/|$)!i', '/', $default_uri );
        }
    
        $default_language    = wpm_get_default_language();
        $default_lang_prefix = get_option( 'wpm_use_prefix', 'no' ) === 'yes' ? $default_language : '';
    
        if ( $language === $default_language ) {
            $new_uri = '/' . $default_lang_prefix . $default_uri;
        } else {
            $new_uri = '/' . $language . $default_uri;
        }
    
        $new_uri = preg_replace( '/(\/+)/', '/', $new_uri );
    
        if ( '/' !== $new_uri ) {
            $new_url = $host . $new_uri;
        } else {
            $new_url = $host;
        }
    
        return apply_filters( 'wpm_translate_url', $new_url, $language, $url );
    }
}

?>