<?php
/**
 * Plugin Name:       WP Multilang Gutenberg
 * Plugin URI:        https://github.com/Lyohha/wp-multilang-gutenberg
 * GitHub Plugin URI: https://github.com/Lyohha/wp-multilang-gutenberg
 * Description:       Addon for fix translation in Gutenberg Editor
 * Author:            Lyohha
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-multilang-gutenberg
 * Domain Path:       /languages
 * Version:           1.3.0
 * Copyright:         © 2022 Lyohha
 *
 * @package  WPM
 * @category Addon
 * @author   Lyohha
 */

if (!defined( 'ABSPATH')) {
	exit; 
}

require 'core/core.php';

new WPM_Gutenberg(__DIR__, '1.3.0');
?>