<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_renkler_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'egemer_renkler';
    $markalar = $wpdb->get_results("SELECT id, ad FROM {$wpdb->prefix}egemer_markalar ORDER BY ad ASC");
    $arama = isset($_GET['arama']) ? sanitize_text_field($_GET['arama']) : '';
    $where = $arama ? $wpdb->prepare("WHERE r.ad LIKE %s", '%' . $arama . '%') : '';
    $renkler = $wpdb->get_results("SELECT r.*, m.ad as marka_adi FROM $table r LEFT JOIN {$wpdb->prefix}egemer_markalar m ON r.marka_id=m.id $where ORDER BY r.id DESC LIMIT 100");

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated notice">Renk silindi.</div>';
    }
    // Toplu ekleme
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['toplu_renk_ekle'])) {
            $marka_id = intval($_POST['toplu_marka_id']);
            if (!$marka_id) {
                echo '<div class="error notice"><p>Lütfen önce bir marka seçin.</p></div>';
            } else {
                for ($i = 0; $i < 10; $i++) {
                    $ad = trim($_POST['toplu_ad'][$i]);
                    if ($ad != '') {
                        $logo_id = 0;
                        if (!empty($_FILES['toplu_resim']['name'][$i])) {
                            $_FILES['tekresim'] = [
                                'name' => $_FILES['toplu_resim']['name'][$i],
                                'type' => $_FILES['toplu_resim']['type'][$i],
                                'tmp_name' => $_FILES['toplu_resim']['tmp_name'][$i],
                                'error' => $_FILES['toplu_resim']['error'][$i],
                                'size' => $_FILES['toplu_resim']['size'][$i]
                            ];
                            require_once(ABSPATH . 'wp-admin/includes/file.php');
                            require_once(ABSPATH . 'wp-admin/includes/media.php');
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $logo_id = media_handle_upload('tekresim', 0);
                            if (is_wp_error($logo_id)) $logo_id = 0;
                        }
                        $wpdb->insert($table, [
                            'marka_id' => $marka_id,
                            'ad' => sanitize_text_field($ad),
                            'resim_id' => $logo_id
                        ]);
                    }
                }
                echo '<div class="updated notice">Toplu renk eklendi.</div>';
            }
        }
        // Tekli ekleme/düzenleme
        else if (isset($_POST['egemer_renk_submit'])) {
            $marka_id = intval($_POST['marka_id']);
            if (!$marka_id) {
                echo '<div class="error notice"><p>Lütfen önce bir marka seçin.</p></div>';
            } else {
                $logo_id = 0;
                if (!empty($_FILES['resim']['name'])) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $logo_id = media_handle_upload('resim', 0);
                    if (is_wp_error($logo_id)) $logo_id = 0;
                }
                $data = [
                    'marka_id' => $marka_id,
                    'ad' => sanitize_text_field($_POST['ad']),
                    'resim_id' => $logo_id
                ];
                if (isset($_POST['guncelle_id']) && $_POST['guncelle_id']) {
                    $wpdb->update($table, $data, ['id' => intval($_POST['guncelle_id'])]);
                    echo '<div class="updated notice">Renk güncellendi.</div>';
                } else {
                    $wpdb->insert($table, $data);
                    echo '<div class="updated notice">Renk eklendi.</div>';
                }
            }
        }
    }

    ?>
    <div class="wrap">
        <h1>Renkler</h1>
        <form method="get" style="margin-bottom:15px;">
            <input type="hidden" name="page" value="egemer-renkler">
            <input type="text" name="arama" value="<?= esc_attr($arama) ?>" placeholder="Renk adı ara" style="width:200px;">
            <button type="submit" class="button">Ara</button>
            <a href="?page=egemer-renkler" class="button">Tümünü Göster</a>
        </form>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marka</th>
                    <th>Ad</th>
                    <th>Resim</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($renkler as $r): ?>
                <tr>
                    <td><?= $r->id ?></td>
                    <td><?= esc_html($r->marka_adi) ?></td>
                    <td><?= esc_html($r->ad) ?></td>
                    <td><?php if($r->resim_id) echo wp_get_attachment_image($r->resim_id, [50,50]); ?></td>
                    <td>
                        <a href="?page=egemer-renkler&duzenle=<?= $r->id ?>" class="button">Düzenle</a>
                        <a href="?page=egemer-renkler&delete=<?= $r->id ?>" class="button" onclick="return confirm('Silinsin mi?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <h2><?= isset($_GET['duzenle']) ? 'Renk Düzenle' : 'Yeni Renk Ekle' ?></h2>
        <?php
        $duzenle = null;
        if (isset($_GET['duzenle'])) {
            $duzenle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", intval($_GET['duzenle'])));
        }
        ?>
        <form method="post" enctype="multipart/form-data" id="egemer_renk_form">
            <?php if($duzenle): ?><input type="hidden" name="guncelle_id" value="<?= $duzenle->id ?>"><?php endif; ?>
            <table class="form-table">
                <tr>
                    <th>Marka</th>
                    <td>
                        <select name="marka_id" id="egemer_renk_marka" required>
                            <option value="">-- Marka Seçin --</option>
                            <?php foreach($markalar as $m): ?>
                                <option value="<?= $m->id ?>" <?= ($duzenle && $duzenle->marka_id==$m->id)?'selected':'' ?>><?= esc_html($m->ad) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Ad</th>
                    <td><input type="text" name="ad" value="<?= $duzenle ? esc_attr($duzenle->ad) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Resim</th>
                    <td>
                        <input type="file" name="resim">
                        <?php if($duzenle && $duzenle->resim_id) echo '<br>' . wp_get_attachment_image($duzenle->resim_id, [50,50]); ?>
                    </td>
                </tr>
            </table>
            <p><button type="submit" name="egemer_renk_submit" class="button-primary">Kaydet</button></p>
        </form>

        <hr>
        <h2>Toplu Renk Ekle</h2>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th>Marka</th>
                    <td>
                        <select name="toplu_marka_id" required>
                            <option value="">-- Marka Seçin --</option>
                            <?php foreach($markalar as $m): ?>
                                <option value="<?= $m->id ?>"><?= esc_html($m->ad) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php for($i=0;$i<10;$i++): ?>
                <tr>
                    <th>Renk Adı <?= $i+1 ?></th>
                    <td>
                        <input type="text" name="toplu_ad[]" style="width:200px;">
                        <input type="file" name="toplu_resim[]">
                    </td>
                </tr>
                <?php endfor; ?>
            </table>
            <p><button type="submit" name="toplu_renk_ekle" class="button-primary">Toplu Kaydet</button></p>
        </form>
    </div>
    <?php
}