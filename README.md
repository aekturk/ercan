# Egemer Teklif Formu Plugini

**Plug-in Version:** 1.9.2  
**Minimum PHP Gereksinimi:** 7.2+  
**Minimum WordPress Gereksinimi:** 5.5+  

---

## Amaç
Müşterilerinizin web sitenizden, ürün gruplarınıza özgü marka, renk, işçilik, kategori, süpürgelik yüksekliği, eviye tipi gibi seçimlerle çok adımlı ve yönetilebilir bir teklif talep formunu kolayca doldurabilmesini sağlar. Tüm içerikler admin panelinden yönetilebilir ve dinamik olarak güncellenir.

---

## Özellikler

- **Çok Adımlı ve Animasyonlu Form:** Kullanıcılar için modern, akıcı ve adım adım ilerleyen form.
- **Dinamik İçerik:** Ürün, marka, renk, işçilik, kategori, süpürgelik yüksekliği ve eviye tipi verileri admin panelinden yönetilir ve forma otomatik yansır.
- **Görsel Destekli Seçim:** Tüm seçimlerde görsel ve yazılı kutular ile kolay ve şık bir kullanıcı deneyimi.
- **Responsive Tasarım:** Mobil ve masaüstünde uyumlu, genişliği %85’e kadar büyüyebilen modern görünüm.
- **Kullanıcı Bilgileri Grid:** 1/3 başlıklar, 2/3 inputlar ile modern grid düzeni.
- **AJAX ile Yükleme:** Tüm seçenekler (ürün, marka, renk, işçilik, vb.) hızlıca yüklenir, sayfa yenilemeye gerek kalmaz.
- **Formdan Teklif Alma:** Kullanıcı seçimleri ve bilgileri ile teklif talebi oluşturulabilir.

---

## Kurulum

1. Tüm plug-in dosyalarını `wp-content/plugins/egemer-teklif` altına yükleyin.
2. Yönetici panelinden eklentiyi aktif edin.
3. Veritabanı tabloları otomatik olarak oluşturulmaz, ilk kurulumda admin panelinden örnek veri ekleyin veya tablo yapısını aşağıdan inceleyin.
4. Formu göstermek için herhangi bir sayfaya `[egemer_teklif_form]` shortcode’unu ekleyin.

---

## Admin Panel Özellikleri

- **Ürünler:** Ürün adı, fiyatı, birimi ve görseli ile yönetilir.  
- **Markalar:** Her ürün için marka eklenebilir, logo ile birlikte yönetilir.  
- **Renkler:** Her marka için renk ve renk görseli eklenebilir.  
- **İşçilik Detayları:** Yönetilebilir, görselli işçilik türleri seçilebilir.
- **Kategoriler:** Yönetilebilir ürün kategorileri.
- **Süpürgelik Yüksekliği:** Farklı yükseklik seçenekleri tanımlanabilir.
- **Eviye Tipleri:** Eviye tipleri görsel ile birlikte seçilebilir.
- **Toplu Ekleme:** Markalar ve renkler için toplu ekleme desteği.
- **Düzenle & Sil:** Tüm içerik kolayca güncellenebilir veya silinebilir.

---

## Ekstra Özellikler

- **Animasyonlu adım geçişleri**
- **Seçilmeden sonraki adıma geçilememe kontrolü**
- **Tüm seçimlerde görsellerin admin panelindekiyle birebir eşleşmesi**
- **Yönetici tarafından eklenen her yeni ürün/marka/renk/işçilik vb. otomatik forma yansır**
- **Kısa kod ile istenilen sayfada kolayca kullanılabilir**

---

## Veritabanı Yapısı

Aşağıdaki tablolar kullanılır (prefix genellikle `wp_`):

- **egemer_urunler**  
  - `id`, `ad`, `fiyat`, `birim`, `resim_id`
- **egemer_markalar**  
  - `id`, `urun_id`, `ad`, `resim_id`
- **egemer_renkler**  
  - `id`, `marka_id`, `ad`, `resim_id`
- **egemer_iscilik_detay**  
  - `id`, `ad`, `resim_id`
- **egemer_kategoriler**  
  - `id`, `ad`, `resim_id`
- **egemer_supurgelik_yuksekligi**  
  - `id`, `ad`, `resim_id`
- **egemer_eviye_tipleri**  
  - `id`, `ad`, `resim_id`

**Görsellerin tamamı WordPress medya kütüphanesine yüklenir ve attachment olarak saklanır.**

---

## Kullanım

- Herhangi bir sayfaya `[egemer_teklif_form]` ekleyin.
- Formda kullanıcı bilgileri ve tüm seçim adımları (Ürün, Marka, Renk, İşçilik Detayı, Kategori, Süpürgelik, Eviye Tipi) adım adım doldurulur.
- Admin panelinden ürün, marka, renk, işçilik detayı, kategori, süpürgelik yüksekliği ve eviye tiplerini istediğiniz gibi ekleyin/çıkartın/düzenleyin — forma otomatik yansır.

---

## Sürüm Notu ve Destek

- Yeni özellik eklendikçe bu dosya otomatik güncellenecektir.
- Sorularınız için lütfen eklenti geliştiricisine ulaşınız.
