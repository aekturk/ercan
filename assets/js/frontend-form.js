jQuery(function($){
  let step = 1;
  let kalemler = [];
  let seciliUrun = null, seciliMarka = null, seciliRenk = null;

  function goToStep(s) {
    $('.form-step').removeClass('active');
    $('.form-step[data-step="'+s+'"]').addClass('active');
    step = s;
  }

  // Adım 1'den 2'ye geçiş
  $('.form-step[data-step="1"] .step-next').click(function(){
    goToStep(2);
  });

  // Adım 2: Kalem eklemek ister misin?
  $('.add-kalem').click(function(){
    goToStep(3);
    urunleriYukle();
  });
  $('#no-kalem').click(function(){
    if (kalemler.length === 0) {
      alert("En az bir kalem eklemelisiniz.");
      return;
    }
    goToStep(4); guncelleOzet();
  });

  // Adım 3: Ürün/Marka/Renk görsel seçimli
  function urunleriYukle() {
    $.post(efajax.ajaxurl, {action:'egemer_get_urunler'}, function(veriler){
      $('#urunler').html('');
      $.each(veriler, function(i, u){
        $('#urunler').append(
          '<div class="secimli urun" data-id="'+u.id+'"><img src="'+u.gorsel+'" alt="'+u.ad+'"><div>'+u.ad+'</div></div>'
        );
      });
      seciliUrun = null; seciliMarka = null; seciliRenk = null;
      $('#markalar').empty(); $('#renkler').empty();
    });
  }
  $('#urunler').on('click', '.urun', function(){
    $('#urunler .secimli').removeClass('selected');
    $(this).addClass('selected');
    seciliUrun = $(this).data('id');
    // Markaları yükle
    $.post(efajax.ajaxurl, {action:'egemer_get_markalar', urun_id: seciliUrun}, function(veriler){
      $('#markalar').html('');
      $.each(veriler, function(i, m){
        $('#markalar').append(
          '<div class="secimli marka" data-id="'+m.id+'"><img src="'+m.gorsel+'" alt="'+m.ad+'"><div>'+m.ad+'</div></div>'
        );
      });
      seciliMarka = null; seciliRenk = null;
      $('#renkler').empty();
    });
  });
  $('#markalar').on('click', '.marka', function(){
    $('#markalar .secimli').removeClass('selected');
    $(this).addClass('selected');
    seciliMarka = $(this).data('id');
    // Renkleri yükle
    $.post(efajax.ajaxurl, {action:'egemer_get_renkler', marka_id: seciliMarka}, function(veriler){
      $('#renkler').html('');
      $.each(veriler, function(i, r){
        $('#renkler').append(
          '<div class="secimli renk" data-id="'+r.id+'"><img src="'+r.gorsel+'" alt="'+r.ad+'"><div>'+r.ad+'</div></div>'
        );
      });
      seciliRenk = null;
    });
  });
  $('#renkler').on('click', '.renk', function(){
    $('#renkler .secimli').removeClass('selected');
    $(this).addClass('selected');
    seciliRenk = $(this).data('id');
  });

  // Kalem ekle
  $('.add-this-kalem').click(function(){
    // Tüm seçimler zorunlu!
    if (!seciliUrun || !seciliMarka || !seciliRenk) { alert("Lütfen ürün, marka ve renk seçin."); return; }
    let adet = parseInt($('#adet').val()) || 1;
    let fiyat = parseFloat($('#fiyat').val()) || 0;
    let olcu = $('#olcu').val();
    let notlar = $('#notlar').val();
    let kalem = {
      urun_id: seciliUrun,
      marka_id: seciliMarka,
      renk_id: seciliRenk,
      adet: adet,
      fiyat: fiyat,
      olcu: olcu,
      notlar: notlar
    };
    kalemler.push(kalem);
    $('.eklenen-kalemler').append('<div>Kalem '+kalemler.length+': Ürün '+seciliUrun+' - Marka '+seciliMarka+' - Renk '+seciliRenk+' - Adet '+adet+'</div>');
    goToStep(2);
  });

  // Geri butonları
  $('.step-prev').click(function(){ goToStep(step-1); });

  // Sipariş özeti
  function guncelleOzet() {
    let html = '';
    kalemler.forEach(function(k,i){
      html += '<div>'+(i+1)+'. Kalem: Ürün: '+k.urun_id+' | Marka: '+k.marka_id+' | Renk: '+k.renk_id+' | Adet: '+k.adet+'</div>';
    });
    $('.siparis-ozet').html(html);
  }

  // Form submit
  $('#egemer-multistep-form').submit(function(e){
    e.preventDefault();
    let musteri = {
      adsoyad: $('[name="adsoyad"]').val(),
      adres: $('[name="adres"]').val(),
      telefon: $('[name="telefon"]').val(),
      email: $('[name="email"]').val(),
      vergi_dairesi: $('[name="vergi_dairesi"]').val(),
      vergi_no: $('[name="vergi_no"]').val()
    };
    if (kalemler.length === 0) {
      alert('En az bir sipariş kalemi eklemelisiniz.');
      return;
    }
    $.post(efajax.ajaxurl, {action:'egemer_teklif_kaydet', musteri: musteri, kalemler: kalemler}, function(resp){
      if(resp.success) {
        $('#form-sonuc').html('<div class="success">Teklif kaydedildi! ID: '+resp.data.teklif_id+'</div>');
        $('#egemer-multistep-form').trigger("reset");
        kalemler = [];
      } else {
        $('#form-sonuc').html('<div class="error">Hata oluştu.</div>');
      }
    });
  });
});