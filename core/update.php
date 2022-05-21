<?php

/**
 * @version 1.2.0
 */

class WPM_Gutenberg_Update {
    private $slug;
    private $version;
    private $key;

    public function __construct($slug, $version) {

        $this->slug = $slug;
        $this->version = $version;
        $this->key = $slug . '_json';

        add_filter('plugins_api', array($this, 'info'), 20, 3);
        add_filter('site_transient_update_plugins', array( $this, 'update'));
    }

    private function json() {

        $request = get_transient($this->key);

        if($request === false) {

            $request = wp_remote_get( 
                'https://lyohha.github.io/plugin-releases/updates.json', 
                array(
                    'timeout' => 10,
                    'headers' => array(
                        'Accept' => 'application/json'
                    ) 
                )
            );
        
            // do nothing if we don't get the correct response from the server
            if( 
                is_wp_error($request)
                || 200 !== wp_remote_retrieve_response_code($request)
                || empty( wp_remote_retrieve_body($request))
            ) {
                return null;	
            }

            set_transient($this->key, $request, DAY_IN_SECONDS);
        }

        $json = json_decode(wp_remote_retrieve_body($request), true);

        $json = $json[$this->slug];

        return $json;
    }

    public function info($res, $action, $args) {

        if('plugin_information' !== $action) {
            return $res;
        }
    
        // do nothing if it is not our plugin
        if($this->slug !== $args->slug) {
            return $res;
        }

        // request plugin info
        $json = $this->json();

        if(!is_array($json)) {
            return $res;
        }

        $res = new stdClass();

        $res->name = $json['name'];
        $res->slug = $json['slug'];
        $res->author = $json['author'];
        $res->author_profile = $json['author_profile'];
        $res->version = $json['version'];
        $res->tested = $json['tested'];
        $res->requires = $json['requires'];
        $res->requires_php = $json['requires_php'];
        $res->download_link = $json['download_url'];
        $res->trunk = $json['download_url'];
        $res->last_updated = $json['last_updated'];
        $res->sections = array(
            'description' => $json['sections']['description'],
            'installation' => $json['sections']['installation'],
            'changelog' => $json['sections']['changelog'],
            // you can add your custom sections (tabs) here
        );

        // in case you want the screenshots tab, use the following HTML format for its content:
        // <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
        if(!empty($json['sections']['screenshots'])) {
            $res->sections[ 'screenshots' ] = $json['sections']['screenshots'];
        }

        $res->banners = array(
            'low' => $json['banners']['low'],
            'high' => $json['banners']['high'],
        );
        
        return $res;
    }

    public function update($transient) {
        if (empty( $transient->checked)) {
            return $transient;
        }
    
        // request plugin info
        $json = $this->json();

        if (
            is_array($json) &&
            version_compare($this->version, $json['version'], '<') && 
            version_compare($json['requires'], get_bloginfo( 'version' ), '<=') && 
            version_compare($json['requires_php'], PHP_VERSION, '<=')
        )  {
            $res = new stdClass();
            $res->slug = $json['slug'];
            $res->plugin = $this->slug . '/' . $this->slug . '.php'; // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
            $res->new_version = $json['version'];
            $res->tested = $json['tested'];
            $res->package = $json['download_url'];
            $transient->response[$res->plugin] = $res;
            
            //$transient->checked[$res->plugin] = $remote->version;
            // var_export($transient);
        }

        return $transient;
    }


}

?>