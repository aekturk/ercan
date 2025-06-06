<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_kategoriler_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'egemer_kategoriler';

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated notice">Kategori silindi.</div>';
    }
    // Ekle/düzenle işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['egemer_kategori_submit'])) {
        $resim_id = 0;
        if (!empty($_FILES['resim']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $resim_id = media_handle_upload('resim', 0);
            if (is_wp_error($resim_id)) $resim_id = 0;
        }
        $data = [
            'ad' => sanitize_text_field($_POST['ad']),
            'resim_id' => $resim_id
        ];
        if (isset($_POST['guncelle_id']) && $_POST['guncelle_id']) {
            $wpdb->update($table, $data, ['id' => intval($_POST['guncelle_id'])]);
            echo '<div class="updated notice">Kategori güncellendi.</div>';
        } else {
            $wpdb->insert($table, $data);
            echo '<div class="updated notice">Kategori eklendi.</div>';
        }
    }
    $kategoriler = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
        <h1>Kategoriler</h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Resim</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($kategoriler as $k): ?>
                <tr>
                    <td><?= $k->id ?></td>
                    <td><?= esc_html($k->ad) ?></td>
                    <td><?php if($k->resim_id) echo wp_get_attachment_image($k->resim_id, [50,50]); ?></td>
                    <td>
                        <a href="?page=egemer-kategoriler&duzenle=<?= $k->id ?>" class="button">Düzenle</a>
                        <a href="?page=egemer-kategoriler&delete=<?= $k->id ?>" class="button" onclick="return confirm('Silinsin mi?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <h2><?= isset($_GET['duzenle']) ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' ?></h2>
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
                    <th>Resim</th>
                    <td>
                        <input type="file" name="resim">
                        <?php if($duzenle && $duzenle->resim_id) echo '<br>' . wp_get_attachment_image($duzenle->resim_id, [50,50]); ?>
                    </td>
                </tr>
            </table>
            <p><button type="submit" name="egemer_kategori_submit" class="button-primary">Kaydet</button></p>
        </form>
    </div>
    <?php
}