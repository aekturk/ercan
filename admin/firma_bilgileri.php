<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_firma_bilgileri_page() {
    global $wpdb;
    $user_id = get_current_user_id();
    $table = $wpdb->prefix . 'egemer_firma_bilgileri';

    // Varsayılan sıralama
    $default_order = [
        'firma_unvani',
        'adres',
        'telefon',
        'mobil',
        'email',
        'web',
        'logo_id',
        'teklif_form_no'
    ];

    // Kayıt çek veya ilk kayıt oluştur
    $firma = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id=%d", $user_id));
    if (!$firma) {
        $wpdb->insert($table, [
            'user_id' => $user_id,
            'firma_unvani' => '',
            'adres' => '',
            'telefon' => '',
            'mobil' => '',
            'email' => '',
            'web' => '',
            'logo_id' => 0,
            'teklif_form_no' => 1,
            'pdf_ustbilgi_siralama' => implode(',', $default_order)
        ]);
        $firma = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id=%d", $user_id));
    }

    // Güncelleme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['egemer_fb_save'])) {
        $logo_id = $firma->logo_id;
        if (!empty($_FILES['logo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $logo_id = media_handle_upload('logo', 0);
            if (is_wp_error($logo_id)) $logo_id = $firma->logo_id;
        }

        $wpdb->update($table, [
            'firma_unvani' => sanitize_text_field($_POST['firma_unvani']),
            'adres' => sanitize_textarea_field($_POST['adres']),
            'telefon' => sanitize_text_field($_POST['telefon']),
            'mobil' => sanitize_text_field($_POST['mobil']),
            'email' => sanitize_email($_POST['email']),
            'web' => esc_url_raw($_POST['web']),
            'logo_id' => $logo_id,
            'teklif_form_no' => intval($_POST['teklif_form_no']),
            'pdf_ustbilgi_siralama' => sanitize_text_field($_POST['pdf_ustbilgi_siralama'])
        ], ['user_id' => $user_id]);
        echo '<div class="updated notice"><p>Firma bilgileri kaydedildi!</p></div>';
        $firma = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id=%d", $user_id));
    }

    // Sıralama
    $order = $firma->pdf_ustbilgi_siralama ? explode(',', $firma->pdf_ustbilgi_siralama) : $default_order;
    $fields = [
        'firma_unvani'    => 'Firma Ünvanı',
        'adres'           => 'Adres',
        'telefon'         => 'Telefon',
        'mobil'           => 'Mobil',
        'email'           => 'E-Posta',
        'web'             => 'Web Adresi',
        'logo_id'         => 'Firma Logosu',
        'teklif_form_no'  => 'Teklif Form No'
    ];
    ?>
    <div class="wrap">
        <h1>Firma Bilgileri ve PDF Üst Bilgi Tasarımı</h1>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr><th>Firma Ünvanı</th><td><input type="text" name="firma_unvani" value="<?= esc_attr($firma->firma_unvani) ?>" required></td></tr>
                <tr><th>Adres</th><td><textarea name="adres" rows="3"><?= esc_textarea($firma->adres) ?></textarea></td></tr>
                <tr><th>Telefon</th><td><input type="text" name="telefon" value="<?= esc_attr($firma->telefon) ?>"></td></tr>
                <tr><th>Mobil</th><td><input type="text" name="mobil" value="<?= esc_attr($firma->mobil) ?>"></td></tr>
                <tr><th>E-Posta</th><td><input type="email" name="email" value="<?= esc_attr($firma->email) ?>"></td></tr>
                <tr><th>Web</th><td><input type="url" name="web" value="<?= esc_attr($firma->web) ?>"></td></tr>
                <tr>
                    <th>Firma Logosu</th>
                    <td>
                        <input type="file" name="logo">
                        <?php
                        if ($firma->logo_id) echo '<br>' . wp_get_attachment_image($firma->logo_id, [80,80]);
                        ?>
                    </td>
                </tr>
                <tr><th>Teklif Form No</th><td><input type="number" name="teklif_form_no" value="<?= esc_attr($firma->teklif_form_no) ?>" min="1"></td></tr>
            </table>
            <h2>PDF Üst Bilgi Sıralaması (Sürükle bırak ile değiştir)</h2>
            <ul class="egemer-sortable-ustbilgi" style="max-width:350px;">
                <?php foreach ($order as $f): ?>
                    <li id="<?= $f ?>"><?= $fields[$f] ?? $f ?></li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" name="pdf_ustbilgi_siralama" id="egemer_ustbilgi_siralama" value="<?= esc_attr($firma->pdf_ustbilgi_siralama) ?>">
            <p><button type="submit" name="egemer_fb_save" class="button-primary">Kaydet</button></p>
        </form>
    </div>
    <?php
}