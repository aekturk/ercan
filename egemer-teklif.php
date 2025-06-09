<?php
/*
Plugin Name: Egemer Teklif ve Ürün Yönetimi
Description: Müşterilerinizin web sitenizden, ürün gruplarınıza özgü marka, renk, işçilik, kategori, süpürgelik yüksekliği, eviye tipi gibi seçimlerle çok adımlı ve yönetilebilir bir teklif talep formunu kolayca doldurabilmesini sağlar. Tüm içerikler admin panelinden yönetilebilir ve dinamik olarak güncellenir.
Version: 1.9.2
Author: Ercan CEVİZ (info@ercanceviz.com.tr)
Author URI: https://ercanceviz.com.tr
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
*/

if (!defined('ABSPATH')) exit;

define('EGEMER_TEKLIF_VERSION', '1.9.1');

// Otomatik tablo kurulumu ve upgrade kontrolü
require_once plugin_dir_path(__FILE__) . 'includes/install.php';

// Menü ve modüller
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';

// AJAX işlemleri
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';

// Modül dosyaları
require_once plugin_dir_path(__FILE__) . 'admin/teklifler.php';
require_once plugin_dir_path(__FILE__) . 'admin/urunler.php';
require_once plugin_dir_path(__FILE__) . 'admin/markalar.php';
require_once plugin_dir_path(__FILE__) . 'admin/renkler.php';
require_once plugin_dir_path(__FILE__) . 'admin/iscilik_detay.php';
require_once plugin_dir_path(__FILE__) . 'admin/kategoriler.php';
require_once plugin_dir_path(__FILE__) . 'admin/supurgelik_yukseklikleri.php';
require_once plugin_dir_path(__FILE__) . 'admin/eviye_tipleri.php';
require_once plugin_dir_path(__FILE__) . 'admin/firma_bilgileri.php';

// Admin CSS/JS
add_action('admin_enqueue_scripts', function($hook) {
    wp_enqueue_style('egemer_admin_style', plugin_dir_url(__FILE__). 'assets/css/admin-style.css');
    wp_enqueue_script('egemer_admin_script', plugin_dir_url(__FILE__). 'assets/js/admin-script.js', ['jquery'], false, true);
    wp_enqueue_script('egemer_pdf_builder', plugin_dir_url(__FILE__). 'assets/js/pdf-builder.js', ['jquery', 'jquery-ui-sortable'], false, true);
});

// FRONTEND TEKLİF FORMU CSS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('egemer-teklifform', plugin_dir_url(__FILE__) . 'assets/css/egemer-teklifform.css', [], EGEMER_TEKLIF_VERSION);
});

// FRONTEND TEKLİF FORMU SHORTCODE EKLEME
require_once plugin_dir_path(__FILE__) . 'includes/shortcode-teklifform.php';