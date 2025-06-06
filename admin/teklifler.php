<?php
if (!defined('ABSPATH')) exit;

function egemer_admin_teklifler_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'egemer_teklifler';

    // Sütunları ekle (bir defa çalışır)
    $col = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'ana_durum'");
    if (!$col) $wpdb->query("ALTER TABLE $table ADD ana_durum VARCHAR(40) DEFAULT 'Oluşturuldu'");
    $col = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'surecler'");
    if (!$col) $wpdb->query("ALTER TABLE $table ADD surecler VARCHAR(255) DEFAULT ''");
    $col = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'aciklama'");
    if (!$col) $wpdb->query("ALTER TABLE $table ADD aciklama TEXT DEFAULT ''");

    $arama = isset($_GET['arama']) ? sanitize_text_field($_GET['arama']) : '';
    $where = $arama ? $wpdb->prepare("WHERE ad LIKE %s OR musteri LIKE %s", '%'.$arama.'%', '%'.$arama.'%') : '';
    $teklifler = $wpdb->get_results("SELECT * FROM $table $where ORDER BY id DESC LIMIT 100");

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated notice">Teklif silindi.</div>';
    }
    // Ekle/Düzenle işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['egemer_teklif_submit'])) {
        $data = [
            'ad' => sanitize_text_field($_POST['ad']),
            'musteri' => sanitize_text_field($_POST['musteri']),
            'ana_durum' => sanitize_text_field($_POST['ana_durum']),
            'surecler' => is_array($_POST['surecler']) ? implode(',', $_POST['surecler']) : '',
            'aciklama' => sanitize_text_field($_POST['aciklama']),
            'durum' => '', // eski alan, istersen kaldırabilirsin
            'toplam' => floatval($_POST['toplam']),
            'tarih' => sanitize_text_field($_POST['tarih']),
        ];
        if (isset($_POST['guncelle_id']) && $_POST['guncelle_id']) {
            $wpdb->update($table, $data, ['id' => intval($_POST['guncelle_id'])]);
            echo '<div class="updated notice">Teklif güncellendi.</div>';
        } else {
            $wpdb->insert($table, $data);
            echo '<div class="updated notice">Teklif eklendi.</div>';
        }
    }

    // Durum radio ve süreç checkbox değerleri
    $ana_durumlar = ['Oluşturuldu','Onaylandı','Ödeme Yapıldı','Ödeme Yapılmadı'];
    $surec_list = [
        'Tedarik Süreci',
        'Üretim Süreci',
        'Montaj Süreci',
        'Teslim Süreci',
        'Kontrol ve Servis Süreci'
    ];
    ?>
    <div class="wrap">
        <h1>Teklifler</h1>
        <form method="get" style="margin-bottom:15px;">
            <input type="hidden" name="page" value="egemer-teklifler">
            <input type="text" name="arama" value="<?= esc_attr($arama) ?>" placeholder="Ad veya müşteri ara" style="width:200px;">
            <button type="submit" class="button">Ara</button>
            <a href="?page=egemer-teklifler" class="button">Tümünü Göster</a>
        </form>
        <table class="widefat" id="teklifler-tablo">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Müşteri</th>
                    <th>Ana Durum</th>
                    <th>Süreçler</th>
                    <th>Açıklama</th>
                    <th>Toplam</th>
                    <th>Tarih</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($teklifler as $t): 
                    $aktif_surecler = array_map('trim', explode(',', $t->surecler ?: ''));
                ?>
                <tr data-id="<?= $t->id ?>">
                    <td><?= $t->id ?></td>
                    <td><?= esc_html($t->ad) ?></td>
                    <td><?= esc_html($t->musteri) ?></td>
                    <td>
                        <?php foreach($ana_durumlar as $d): ?>
                            <label><input type="radio" name="ana_durum_<?= $t->id ?>" class="ana-durum-radio" value="<?= $d ?>" <?= $t->ana_durum == $d ? 'checked' : '' ?> data-id="<?= $t->id ?>"> <?= $d ?></label><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach($surec_list as $s): ?>
                            <label><input type="checkbox" name="surec_<?= $t->id ?>[]" class="surec-checkbox" value="<?= $s ?>" <?= in_array($s,$aktif_surecler)?'checked':'' ?> data-id="<?= $t->id ?>"> <?= $s ?></label><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <input type="text" class="aciklama-input" data-id="<?= $t->id ?>" value="<?= esc_attr($t->aciklama) ?>" style="width:120px;">
                    </td>
                    <td><?= number_format($t->toplam,2,',','.') ?> ₺</td>
                    <td><?= date('d.m.Y', strtotime($t->tarih)) ?></td>
                    <td>
                        <a href="?page=egemer-teklifler&duzenle=<?= $t->id ?>" class="button">Düzenle</a>
                        <a href="?page=egemer-teklifler&delete=<?= $t->id ?>" class="button" onclick="return confirm('Silinsin mi?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <h2><?= isset($_GET['duzenle']) ? 'Teklif Düzenle' : 'Yeni Teklif Ekle' ?></h2>
        <?php
        $duzenle = null;
        if (isset($_GET['duzenle'])) {
            $duzenle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", intval($_GET['duzenle'])));
        }
        ?>
        <form method="post">
            <?php if($duzenle): ?><input type="hidden" name="guncelle_id" value="<?= $duzenle->id ?>"><?php endif; ?>
            <table class="form-table">
                <tr>
                    <th>Ad</th>
                    <td><input type="text" name="ad" value="<?= $duzenle ? esc_attr($duzenle->ad) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Müşteri</th>
                    <td><input type="text" name="musteri" value="<?= $duzenle ? esc_attr($duzenle->musteri) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Ana Durum</th>
                    <td>
                        <?php foreach($ana_durumlar as $d): ?>
                            <label><input type="radio" name="ana_durum" value="<?= $d ?>" <?= ($duzenle && $duzenle->ana_durum==$d) || (!$duzenle && $d=="Oluşturuldu")?'checked':'' ?>> <?= $d ?></label>&nbsp;
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th>Süreçler</th>
                    <td>
                        <?php foreach($surec_list as $s): ?>
                            <label><input type="checkbox" name="surecler[]" value="<?= $s ?>"
                            <?php if($duzenle && in_array($s, explode(',', $duzenle->surecler))) echo 'checked'; ?>
                            > <?= $s ?></label>&nbsp;
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th>Açıklama</th>
                    <td><input type="text" name="aciklama" value="<?= $duzenle ? esc_attr($duzenle->aciklama) : '' ?>"></td>
                </tr>
                <tr>
                    <th>Toplam</th>
                    <td><input type="number" step="0.01" name="toplam" value="<?= $duzenle ? esc_attr($duzenle->toplam) : '' ?>" required></td>
                </tr>
                <tr>
                    <th>Tarih</th>
                    <td><input type="date" name="tarih" value="<?= $duzenle ? esc_attr($duzenle->tarih) : date('Y-m-d') ?>" required></td>
                </tr>
            </table>
            <p><button type="submit" name="egemer_teklif_submit" class="button-primary">Kaydet</button></p>
        </form>
    </div>
    <script>
    // AJAX ile ana durum/süreç/açıklama güncelleme
    document.addEventListener('DOMContentLoaded', function(){
        // Ana durum (radio)
        document.querySelectorAll('.ana-durum-radio').forEach(function(r){
            r.addEventListener('change', function(){
                var id = this.dataset.id;
                var val = this.value;
                var fd = new FormData();
                fd.append('action','teklif_ana_durum_update');
                fd.append('id',id);
                fd.append('ana_durum',val);
                fetch(ajaxurl, {method:'POST', body:fd});
            });
        });
        // Süreçler (checkbox çoklu)
        document.querySelectorAll('.surec-checkbox').forEach(function(c){
            c.addEventListener('change', function(){
                var id = this.dataset.id;
                var vals = [];
                document.querySelectorAll('input.surec-checkbox[data-id="'+id+'"]:checked').forEach(function(cb){
                    vals.push(cb.value);
                });
                var fd = new FormData();
                fd.append('action','teklif_surecler_update');
                fd.append('id',id);
                fd.append('surecler',vals.join(','));
                fetch(ajaxurl, {method:'POST', body:fd});
            });
        });
        // Açıklama
        document.querySelectorAll('.aciklama-input').forEach(function(a){
            a.addEventListener('change', function(){
                var id = this.dataset.id;
                var val = this.value;
                var fd = new FormData();
                fd.append('action','teklif_aciklama_update');
                fd.append('id',id);
                fd.append('aciklama',val);
                fetch(ajaxurl, {method:'POST', body:fd});
            });
        });
    });
    </script>
    <?php
}