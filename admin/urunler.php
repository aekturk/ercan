<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_urunler_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'egemer_urunler';
    $arama = isset($_GET['arama']) ? sanitize_text_field($_GET['arama']) : '';
    $where = $arama ? $wpdb->prepare("WHERE ad LIKE %s", '%'.$arama.'%') : '';
    $urunler = $wpdb->get_results("SELECT * FROM $table $where ORDER BY id DESC LIMIT 100");
    $birimler = [
        'adet'   => 'Adet',
        'mtul'   => 'Metretül',
        'mkare'  => 'Metrekare'
    ];

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated notice">Ürün silindi.</div>';
    }
    // Ekle/düzenle işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['egemer_urun_submit'])) {
        $resim_id = 0;
        if (!empty($_FILES['resim']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $resim_id = media_handle_upload('resim', 0);
            if (is_wp_error($resim_id)) $resim_id = 0;
        }
        $data = [
            'ad'    => sanitize_text_field($_POST['ad']),
            'fiyat' => floatval($_POST['fiyat']),
            'birim' => sanitize_text_field($_POST['birim']),
            'resim_id' => $resim_id
        ];
        if (isset($_POST['guncelle_id']) && $_POST['guncelle_id']) {
            $wpdb->update($table, $data, ['id' => intval($_POST['guncelle_id'])]);
            echo '<div class="updated notice">Ürün güncellendi.</div>';
        } else {
            $wpdb->insert($table, $data);
            echo '<div class="updated notice">Ürün eklendi.</div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>Ürünler</h1>
        <form method="get" style="margin-bottom:15px;">
            <input type="hidden" name="page" value="egemer-urunler">
            <input type="text" name="arama" value="<?= esc_attr($arama) ?>" placeholder="Ürün adı ara" style="width:200px;">
            <button type="submit" class="button">Ara</button>
            <a href="?page=egemer-urunler" class="button">Tümünü Göster</a>
        </form>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Fiyat</th>
                    <th>Birim</th>
                    <th>Resim</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($urunler as $u): ?>
                <tr>
                    <td><?= $u->id ?></td>
                    <td><?= esc_html($u->ad) ?></td>
                    <td><?= number_format($u->fiyat,2,',','.') ?> ₺</td>
                    <td><?= esc_html($birimler[$u->birim] ?? $u->birim) ?></td>
                    <td><?php if($u->resim_id) echo wp_get_attachment_image($u->resim_id, [50,50]); ?></td>
                    <td>
                        <a href="?page=egemer-urunler&duzenle=<?= $u->id ?>" class="button">Düzenle</a>
                        <a href="?page=egemer-urunler&delete=<?= $u->id ?>" class="button" onclick="return confirm('Silinsin mi?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <h2><?= isset($_GET['duzenle']) ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' ?></h2>
        <?php
        $duzenle = null;
        if (isset($_GET['duzenle'])) {
            $duzenle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", intval($_GET['duzenle'])));
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <?php if($duzenle): ?><input type="hidden" name="guncelle_id" value="<?= $duzenle->id ?>"><?php endif; ?>
            <table class="form-table">
                <tr>
                    <th>Ad</th>
                    <td><input type="text" name="ad" value="<?= $duzenle ? esc_attr($duzenle->ad) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Fiyat</th>
                    <td><input type="number" step="0.01" name="fiyat" value="<?= $duzenle ? esc_attr($duzenle->fiyat) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Birim</th>
                    <td>
                        <select name="birim">
                            <?php foreach($birimler as $k=>$v): ?>
                                <option value="<?= $k ?>" <?= ($duzenle && $duzenle->birim==$k)?'selected':'' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Resim</th>
                    <td>
                        <input type="file" name="resim">
                        <?php if($duzenle && $duzenle->resim_id) echo '<br>' . wp_get_attachment_image($duzenle->resim_id, [50,50]); ?>
                    </td>
                </tr>
            </table>
            <p><button type="submit" name="egemer_urun_submit" class="button-primary">Kaydet</button></p>
        </form>
    </div>
    <?php
}