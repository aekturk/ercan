<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    add_menu_page('Egemer Teklif', 'Egemer Teklif', 'manage_options', 'egemer-ana', '__return_null', 'dashicons-media-spreadsheet', 25);
    add_submenu_page('egemer-ana', 'Teklifler', 'Teklifler', 'manage_options', 'egemer-teklifler', 'egemer_admin_teklifler_page');
    add_submenu_page('egemer-ana', 'Ürünler', 'Ürünler', 'manage_options', 'egemer-urunler', 'egemer_admin_urunler_page');
    add_submenu_page('egemer-ana', 'Markalar', 'Markalar', 'manage_options', 'egemer-markalar', 'egemer_admin_markalar_page');
    add_submenu_page('egemer-ana', 'Renkler', 'Renkler', 'manage_options', 'egemer-renkler', 'egemer_admin_renkler_page');
    add_submenu_page('egemer-ana', 'İşçilik Detay', 'İşçilik Detay', 'manage_options', 'egemer-iscilik', 'egemer_admin_iscilik_page');
    add_submenu_page('egemer-ana', 'Kategoriler', 'Kategoriler', 'manage_options', 'egemer-kategoriler', 'egemer_admin_kategoriler_page');
    add_submenu_page('egemer-ana', 'Süpürgelik Yüksekliği', 'Süpürgelik Yüksekliği', 'manage_options', 'egemer-supurgelik', 'egemer_admin_supurgelik_page');
    add_submenu_page('egemer-ana', 'Eviye Tipleri', 'Eviye Tipleri', 'manage_options', 'egemer-eviyeler', 'egemer_admin_eviyeler_page');
    add_submenu_page('egemer-ana', 'Firma Bilgileri', 'Firma Bilgileri', 'manage_options', 'egemer-firma-bilgileri', 'egemer_admin_firma_bilgileri_page');
});