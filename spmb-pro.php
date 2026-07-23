<?php
/**
 * Plugin Name: SPMB Pro
 * Description: Sistem Penerimaan Murid Baru untuk sekolah WordPress. Kelola jalur zonasi, afirmasi, prestasi, dan perpindahan dengan kuota, seleksi, dan pengumuman.
 * Version:     1.0.0
 * Requires at least: 6.7
 * Requires PHP: 8.2
 * Author:      WP Sekolah
 * Author URI:  https://wpsekolah.id
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spmb-pro
 * Domain Path: /lang
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SPMB_VERSION', '1.0.0');
define('SPMB_PLUGIN_FILE', __FILE__);
define('SPMB_PATH', plugin_dir_path(__FILE__));
define('SPMB_URL', plugin_dir_url(__FILE__));
define('SPMB_BASENAME', plugin_basename(__FILE__));

require_once SPMB_PATH . 'includes/autoload/class-spmb-autoloader.php';

SPMB_Autoloader::register();

register_activation_hook(__FILE__, ['SPMB_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['SPMB_Deactivator', 'deactivate']);

/**
 * Boot plugin utama.
 */
function spmb_pro_boot(): void {
    SPMB_Plugin::instance();
}

add_action('plugins_loaded', 'spmb_pro_boot');