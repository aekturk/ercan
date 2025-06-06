<?php
if (!defined('ABSPATH')) exit;

// Ürünler
add_action('wp_ajax_egemer_get_urunler', 'egemer_get_urunler');
add_action('wp_ajax_nopriv_egemer_get_urunler', 'egemer_get_urunler');
function egemer_get_urunler(){
    global $wpdb;
    $urunler = $wpdb->get_results("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_urunler ORDER BY ad ASC");
    $dizi = [];
    foreach($urunler as $u){
        $dizi[] = [
            'id' => $u->id,
            'ad' => $u->ad,
            'resim' => $u->resim_id ? wp_get_attachment_url($u->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// Markalar
add_action('wp_ajax_egemer_get_markalar', 'egemer_get_markalar');
add_action('wp_ajax_nopriv_egemer_get_markalar', 'egemer_get_markalar');
function egemer_get_markalar(){
    global $wpdb;
    $urun_id = intval($_POST['urun_id']);
    $markalar = $wpdb->get_results($wpdb->prepare("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_markalar WHERE urun_id = %d ORDER BY ad ASC", $urun_id));
    $dizi = [];
    foreach($markalar as $m){
        $dizi[] = [
            'id' => $m->id,
            'ad' => $m->ad,
            'logo' => $m->resim_id ? wp_get_attachment_url($m->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// Renkler
add_action('wp_ajax_egemer_get_renkler', 'egemer_get_renkler');
add_action('wp_ajax_nopriv_egemer_get_renkler', 'egemer_get_renkler');
function egemer_get_renkler(){
    global $wpdb;
    $marka_id = intval($_POST['marka_id']);
    $renkler = $wpdb->get_results($wpdb->prepare("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_renkler WHERE marka_id=%d ORDER BY ad ASC", $marka_id));
    $dizi = [];
    foreach($renkler as $r){
        $dizi[] = [
            'id' => $r->id,
            'ad' => $r->ad,
            'gorsel' => $r->resim_id ? wp_get_attachment_url($r->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// İşçilik Detayları
add_action('wp_ajax_egemer_get_iscilikler', 'egemer_get_iscilikler');
add_action('wp_ajax_nopriv_egemer_get_iscilikler', 'egemer_get_iscilikler');
function egemer_get_iscilikler(){
    global $wpdb;
    $dizi = [];
    $rows = $wpdb->get_results("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_iscilik_detay ORDER BY ad ASC");
    foreach($rows as $row){
        $dizi[] = [
            'id'=>$row->id,
            'ad'=>$row->ad,
            'resim'=>$row->resim_id ? wp_get_attachment_url($row->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// Kategoriler
add_action('wp_ajax_egemer_get_kategoriler', 'egemer_get_kategoriler');
add_action('wp_ajax_nopriv_egemer_get_kategoriler', 'egemer_get_kategoriler');
function egemer_get_kategoriler(){
    global $wpdb;
    $dizi = [];
    $rows = $wpdb->get_results("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_kategoriler ORDER BY ad ASC");
    foreach($rows as $row){
        $dizi[] = [
            'id'=>$row->id,
            'ad'=>$row->ad,
            'resim'=>$row->resim_id ? wp_get_attachment_url($row->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// Süpürgelik Yüksekliği
add_action('wp_ajax_egemer_get_supurgelikler', 'egemer_get_supurgelikler');
add_action('wp_ajax_nopriv_egemer_get_supurgelikler', 'egemer_get_supurgelikler');
function egemer_get_supurgelikler(){
    global $wpdb;
    $dizi = [];
    $rows = $wpdb->get_results("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_supurgelik_yuksekligi ORDER BY ad ASC");
    foreach($rows as $row){
        $dizi[] = [
            'id'=>$row->id,
            'ad'=>$row->ad,
            'resim'=>$row->resim_id ? wp_get_attachment_url($row->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}

// Eviye Tipleri
add_action('wp_ajax_egemer_get_eviyetipleri', 'egemer_get_eviyetipleri');
add_action('wp_ajax_nopriv_egemer_get_eviyetipleri', 'egemer_get_eviyetipleri');
function egemer_get_eviyetipleri(){
    global $wpdb;
    $dizi = [];
    $rows = $wpdb->get_results("SELECT id, ad, resim_id FROM {$wpdb->prefix}egemer_eviye_tipleri ORDER BY ad ASC");
    foreach($rows as $row){
        $dizi[] = [
            'id'=>$row->id,
            'ad'=>$row->ad,
            'resim'=>$row->resim_id ? wp_get_attachment_url($row->resim_id) : ''
        ];
    }
    wp_send_json($dizi);
}