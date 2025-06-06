<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_iscilik_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'egemer_iscilik';

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated notice">İşçilik silindi.</div>';
    }
    // Ekle/düzenle işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['egemer_iscilik_submit'])) {
        $data = [
            'grup' => sanitize_text_field($_POST['grup']),
            'ad'   => sanitize_text_field($_POST['ad']),
            'fiyat'=> floatval($_POST['fiyat'])
        ];
        if (isset($_POST['guncelle_id']) && $_POST['guncelle_id']) {
            $wpdb->update($table, $data, ['id' => intval($_POST['guncelle_id'])]);
            echo '<div class="updated notice">İşçilik güncellendi.</div>';
        } else {
            $wpdb->insert($table, $data);
            echo '<div class="updated notice">İşçilik eklendi.</div>';
        }
    }
    $gruplar = [
        'Montaj' => 'Montaj İşçiliği',
        'Kesim' => 'Kesim İşçiliği',
        'Yapıştırma' => 'Yapıştırma İşçiliği',
        'Taşıma' => 'Taşıma İşçiliği'
    ];
    ?>
    <div class="wrap">
        <h1>İşçilik Detay</h1>
        <h2>Yeni İşçilik Ekle / Düzenle</h2>
        <form method="post">
            <?php if(isset($_GET['duzenle'])): ?><input type="hidden" name="guncelle_id" value="<?= intval($_GET['duzenle']) ?>"><?php endif; ?>
            <table class="form-table">
                <tr>
                    <th>Grup</th>
                    <td>
                        <select name="grup" required>
                            <?php foreach($gruplar as $k=>$v): ?>
                                <option value="<?= $k ?>"><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Ad</th>
                    <td><input type="text" name="ad" required></td>
                </tr>
                <tr>
                    <th>Fiyat</th>
                    <td><input type="number" name="fiyat" step="0.01" required></td>
                </tr>
            </table>
            <p><button type="submit" name="egemer_iscilik_submit" class="button-primary">Kaydet</button></p>
        </form>
        <hr>
        <h2>İşçilik Listesi</h2>
        <?php foreach($gruplar as $k => $baslik): 
            $kayitlar = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE grup=%s ORDER BY id DESC", $k));
            if (!$kayitlar) continue;
        ?>
            <h3><?= esc_html($baslik) ?></h3>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Fiyat</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($kayitlar as $s): ?>
                    <tr>
                        <td><?= esc_html($s->ad) ?></td>
                        <td><?= number_format($s->fiyat,2,',','.') ?> ₺</td>
                        <td><a href="?page=egemer-iscilik&delete=<?= $s->id ?>" onclick="return confirm('Silinsin mi?')">Sil</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
    <?php
}