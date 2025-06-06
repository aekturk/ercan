<?php
if (!defined('ABSPATH')) exit;
require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
use Dompdf\Dompdf;

function egemer_teklif_pdf($teklif_id, $public = false) {
    global $wpdb;
    $teklif = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}egemer_teklifler WHERE id=%d", $teklif_id));
    if (!$teklif) wp_die('Teklif bulunamadı');
    $fb = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}egemer_firma_bilgileri LIMIT 1");
    $order = $fb && $fb->pdf_ustbilgi_siralama ? explode(',', $fb->pdf_ustbilgi_siralama) : [];

    ob_start();
    ?><html><body>
    <style>
    body { font-family: DejaVu Sans, sans-serif; font-size:12px; }
    .logo { height:60px; }
    .ustbilgi { margin-bottom:15px; }
    </style>
    <h2>Teklif #<?= $teklif->id ?></h2>
    <div class="ustbilgi">
    <table>
    <?php
    $fields = [
        'firma_unvani'    => $fb->firma_unvani ?? '',
        'adres'           => $fb->adres ?? '',
        'telefon'         => $fb->telefon ?? '',
        'mobil'           => $fb->mobil ?? '',
        'email'           => $fb->email ?? '',
        'web'             => $fb->web ?? '',
        'logo_id'         => $fb->logo_id ? wp_get_attachment_url($fb->logo_id) : '',
        'teklif_form_no'  => $fb->teklif_form_no ?? ''
    ];
    foreach($order as $key){
        if($key == 'logo_id' && $fields['logo_id']) {
            echo '<tr><td colspan="2"><img class="logo" src="'.$fields['logo_id'].'" /></td></tr>';
        } elseif ($key == 'teklif_form_no') {
            echo '<tr><td><b>Teklif No:</b></td><td>' . esc_html($fields['teklif_form_no']) . '</td></tr>';
        } elseif(isset($fields[$key]) && $fields[$key]){
            $label = [
                'firma_unvani'=>'Firma Ünvanı','adres'=>'Adres','telefon'=>'Telefon',
                'mobil'=>'Mobil','email'=>'E-Posta','web'=>'Web'
            ][$key] ?? $key;
            echo '<tr><td><b>'.$label.':</b></td><td>'.esc_html($fields[$key]).'</td></tr>';
        }
    }
    ?>
    </table>
    </div>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <th>Ad</th>
            <th>Müşteri</th>
            <th>Telefon</th>
            <th>Email</th>
            <th>Açıklama</th>
            <th>Tarih</th>
        </tr>
        <tr>
            <td><?= esc_html($teklif->ad) ?></td>
            <td><?= esc_html($teklif->musteri) ?></td>
            <td><?= esc_html($teklif->telefon ?? '') ?></td>
            <td><?= esc_html($teklif->email ?? '') ?></td>
            <td><?= esc_html($teklif->aciklama ?? '') ?></td>
            <td><?= date('d.m.Y', strtotime($teklif->tarih)) ?></td>
        </tr>
    </table>
    </body></html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("teklif-{$teklif->id}.pdf", ['Attachment'=>true]);
    exit;
}