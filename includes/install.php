<?php
if (!defined('ABSPATH')) exit;

function egemer_install_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Müşteriler tablosu
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}egemer_musteriler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        adsoyad VARCHAR(255) NOT NULL,
        adres TEXT NOT NULL,
        telefon VARCHAR(32) NOT NULL,
        email VARCHAR(255) NOT NULL,
        vergi_dairesi VARCHAR(128),
        vergi_no VARCHAR(64),
        eklenme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;");

    // Teklifler tablosu
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}egemer_teklifler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        musteri_id INT NOT NULL,
        tarih DATETIME DEFAULT CURRENT_TIMESTAMP,
        toplam_tutar DECIMAL(12,2) DEFAULT 0,
        durum VARCHAR(32) DEFAULT 'beklemede'
    ) $charset_collate;");

    // Teklif kalemleri tablosu
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}egemer_teklif_kalemleri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        teklif_id INT NOT NULL,
        urun_id INT NOT NULL,
        marka_id INT NOT NULL,
        renk_id INT NOT NULL,
        adet INT NOT NULL,
        olcu VARCHAR(128),
        fiyat DECIMAL(12,2) DEFAULT 0,
        tutar DECIMAL(12,2) DEFAULT 0,
        notlar TEXT
    ) $charset_collate;");
}