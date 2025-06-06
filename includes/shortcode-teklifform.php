<?php
function get_products() {
    global $wpdb;
    $table = $wpdb->prefix . "egemer_urunler";
    return $wpdb->get_results("SELECT id, urun_adi, urun_resmi FROM $table WHERE aktif=1 AND silindi=0 ORDER BY urun_adi ASC");
}

function egemer_teklif_form_shortcode() {
    $urunler = get_products();
    ob_start();
    ?>
    <form id="egemer-teklif-form-slider">
        <!-- Adım 1: Kullanıcı Bilgileri -->
        <div class="egemer-form-step active" data-step="1">
            <h2 class="egemer-step-title">Bilgileriniz</h2>
            <div class="egemer-form-row">
                <span class="egemer-icon">👤</span>
                <input type="text" name="ad" placeholder="Adınız" required>
                <span class="egemer-icon">👤</span>
                <input type="text" name="soyad" placeholder="Soyadınız" required>
            </div>
            <div class="egemer-form-row">
                <span class="egemer-icon">📱</span>
                <input type="tel" name="telefon" placeholder="Telefon" required>
                <span class="egemer-icon">✉️</span>
                <input type="email" name="eposta" placeholder="E-posta" required>
            </div>
            <div class="egemer-form-row">
                <span class="egemer-icon">📍</span>
                <input type="text" name="adres" placeholder="Adresiniz">
            </div>
            <div class="egemer-form-row">
                <span class="egemer-icon">🏦</span>
                <input type="text" name="vergi_dairesi" placeholder="Vergi Dairesi">
                <span class="egemer-icon">#️⃣</span>
                <input type="text" name="vergi_no" placeholder="Vergi Numarası">
            </div>
            <div class="egemer-form-actions">
                <button type="button" class="egemer-next-step" disabled>Sonraki</button>
            </div>
        </div>
        <!-- Adım 2: Ürün seçimi -->
        <div class="egemer-form-step" data-step="2">
            <h2 class="egemer-step-title">Ürün</h2>
            <div class="egemer-product-grid">
                <?php if (empty($urunler)): ?>
                    <div class="egemer-product-empty">Ürün bulunamadı.</div>
                <?php else: ?>
                    <?php foreach ($urunler as $urun): ?>
                        <div class="egemer-product-item" data-urun-id="<?php echo esc_attr($urun->id); ?>">
                            <img src="<?php echo esc_url($urun->urun_resmi); ?>" alt="<?php echo esc_attr($urun->urun_adi); ?>" />
                            <span class="egemer-product-caption"><?php echo esc_html($urun->urun_adi); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="egemer-form-actions">
                <button type="button" class="egemer-prev-step">Geri</button>
                <button type="button" class="egemer-next-step" disabled>Sonraki</button>
            </div>
        </div>
        <!-- Devamı: Marka, renk, vs. -->
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 0;
        const steps = document.querySelectorAll('.egemer-form-step');
        function showStep(idx) {
            steps.forEach((el, i) => el.classList.toggle('active', i===idx));
        }

        // Kullanıcı bilgileri kontrolü (zorunlu alanlar)
        const userInputs = document.querySelectorAll('.egemer-form-step[data-step="1"] input[required]');
        const userNext = document.querySelector('.egemer-form-step[data-step="1"] .egemer-next-step');
        userInputs.forEach(inp => inp.addEventListener('input', () => {
            let filled = Array.from(userInputs).every(i => i.value.trim().length > 0);
            userNext.disabled = !filled;
        }));

        // Ürün seçimi
        const productItems = document.querySelectorAll('.egemer-product-item');
        const productNext = document.querySelector('.egemer-form-step[data-step="2"] .egemer-next-step');
        let selectedProduct = null;
        productItems.forEach(item => {
            item.addEventListener('click', function() {
                productItems.forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                selectedProduct = this.getAttribute('data-urun-id');
                productNext.disabled = false;
            });
        });

        // Adımlar arası geçişler
        document.querySelectorAll('.egemer-next-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep < steps.length-1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });
        document.querySelectorAll('.egemer-prev-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });
    });
    </script>
    <style>
    .egemer-form-row { display: flex; gap:8px; margin-bottom:8px; align-items:center; }
    .egemer-form-row input { flex:1; padding:8px 10px 8px 32px; border:1px solid #ccc; border-radius:5px; font-size:1em; }
    .egemer-icon { position:relative; left:25px; margin-right:-25px; font-size:1.1em; opacity:.5; }
    .egemer-product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap:12px 7px; margin:6px 0 18px 0; }
    .egemer-product-item { background:#fff; border:2px solid #d3d3e7; border-radius:7px; text-align:center; padding:10px 3px 8px 3px; font-size:1em; cursor:pointer; transition:box-shadow.1s,border-color.13s; }
    .egemer-product-item.selected { border-color:#6667ab; box-shadow:0 0 7px #6667ab22; background:#f5f5fc; }
    .egemer-product-item img { width:54px; height:54px; object-fit:contain; margin-bottom:3px; }
    .egemer-product-caption { color:#333; font-size:.98em; font-weight:500; }
    .egemer-product-empty { grid-column:1/-1; color:#a00; font-size:1.1em; text-align:center; }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('egemer_teklif_form', 'egemer_teklif_form_shortcode');
?>