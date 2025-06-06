jQuery(function($){
  let nowStep = 1;
  const totalSteps = $('.form-page').length;

  function goToStep(step) {
    $('.form-page').removeClass('active to-left to-right');
    $('.form-page').each(function(i){
      if(i===(step-1)){
        $(this).addClass('active');
      }
      else if(i < (step-1)){
        $(this).addClass('to-left');
      }
      else if(i > (step-1)){
        $(this).addClass('to-right');
      }
    });
    nowStep = step;
    checkSelection();
  }

  function checkSelection() {
    let selectors = [
      {step:2, sel:'.urun'},
      {step:3, sel:'.marka'},
      {step:4, sel:'.renk'},
      {step:5, sel:'.iscilik'},
      {step:6, sel:'.kategori'},
      {step:7, sel:'.supurgelik'},
      {step:8, sel:'.eviyetipi'}
    ];
    let found = selectors.find(x=>x.step===nowStep);
    if(found){
      let nextBtn = $('.form-page[data-step="'+nowStep+'"] .next-btn, .form-page[data-step="'+nowStep+'"] .submit-btn');
      if($('.form-page[data-step="'+nowStep+'"] '+found.sel+'.selected').length) {
        nextBtn.prop('disabled', false);
      } else {
        nextBtn.prop('disabled', true);
      }
    } else {
      $('.form-page.active .next-btn').prop('disabled', false);
    }
  }

  $('.next-btn').on('click',function(e){
    e.preventDefault();
    if($(this).prop('disabled')) return;
    if(nowStep<totalSteps) goToStep(nowStep+1);
  });
  $('.prev-btn').on('click',function(e){
    e.preventDefault();
    if(nowStep>1) goToStep(nowStep-1);
  });

  // Dinamik - Ürünler
  $.post(window.efajax.ajaxurl, {action:'egemer_get_urunler'}, function(veriler){
    $('#urunler').html('');
    $.each(veriler, function(i, u){
      $('#urunler').append(
        '<div class="secimli urun" data-id="'+u.id+'">' +
          (u.resim ? '<img src="'+u.resim+'" alt="'+u.ad+'">' : '') +
          '<div>'+u.ad+'</div>' +
        '</div>'
      );
    });
  });

  // Ürün seçimi
  $(document).on('click', '.urun', function(){
    $('.urun').removeClass('selected');
    $(this).addClass('selected');
    let urun_id = $(this).data('id');
    $('#markalar').html('<div>Yükleniyor...</div>');
    $.post(window.efajax.ajaxurl, {action:'egemer_get_markalar', urun_id: urun_id}, function(veriler){
      $('#markalar').html('');
      if(veriler.length===0) {
        $('#markalar').html('<div>Bu ürüne ait marka yok.</div>');
      } else {
        $.each(veriler, function(i, m){
          $('#markalar').append(
            '<div class="secimli marka" data-id="'+m.id+'">' +
              (m.logo ? '<img src="'+m.logo+'" alt="'+m.ad+'">' : '') +
              '<div>'+m.ad+'</div>' +
            '</div>'
          );
        });
      }
    });
    checkSelection();
  });

  // Marka seçimi
  $(document).on('click', '.marka', function(){
    $('.marka').removeClass('selected');
    $(this).addClass('selected');
    let marka_id = $(this).data('id');
    $('#renkler').html('<div>Yükleniyor...</div>');
    $.post(window.efajax.ajaxurl, {action:'egemer_get_renkler', marka_id: marka_id}, function(veriler){
      $('#renkler').html('');
      if(veriler.length===0) {
        $('#renkler').html('<div>Bu markaya ait renk yok.</div>');
      } else {
        $.each(veriler, function(i, r){
          $('#renkler').append(
            '<div class="secimli renk" data-id="'+r.id+'">' +
              (r.gorsel ? '<img src="'+r.gorsel+'" alt="'+r.ad+'">' : '') +
              '<div>'+r.ad+'</div>' +
            '</div>'
          );
        });
      }
    });
    checkSelection();
  });

  // Renk seçimi
  $(document).on('click', '.renk', function(){
    $('.renk').removeClass('selected');
    $(this).addClass('selected');
    checkSelection();
  });

  // İşçilik Detayı - Dinamik yükle
  $.post(window.efajax.ajaxurl, {action:'egemer_get_iscilikler'}, function(veriler){
    $('#iscilikler').html('');
    $.each(veriler, function(i, u){
      $('#iscilikler').append(
        '<div class="secimli iscilik" data-id="'+u.id+'">' +
          (u.resim ? '<img src="'+u.resim+'" alt="'+u.ad+'">' : '') +
          '<div>'+u.ad+'</div>' +
        '</div>'
      );
    });
  });
  $(document).on('click', '.iscilik', function(){
    $('.iscilik').removeClass('selected');
    $(this).addClass('selected');
    checkSelection();
  });

  // Kategoriler - Dinamik yükle
  $.post(window.efajax.ajaxurl, {action:'egemer_get_kategoriler'}, function(veriler){
    $('#kategoriler').html('');
    $.each(veriler, function(i, u){
      $('#kategoriler').append(
        '<div class="secimli kategori" data-id="'+u.id+'">' +
          (u.resim ? '<img src="'+u.resim+'" alt="'+u.ad+'">' : '') +
          '<div>'+u.ad+'</div>' +
        '</div>'
      );
    });
  });
  $(document).on('click', '.kategori', function(){
    $('.kategori').removeClass('selected');
    $(this).addClass('selected');
    checkSelection();
  });

  // Süpürgelik Yüksekliği - Dinamik yükle
  $.post(window.efajax.ajaxurl, {action:'egemer_get_supurgelikler'}, function(veriler){
    $('#supurgelikler').html('');
    $.each(veriler, function(i, u){
      $('#supurgelikler').append(
        '<div class="secimli supurgelik" data-id="'+u.id+'">' +
          (u.resim ? '<img src="'+u.resim+'" alt="'+u.ad+'">' : '') +
          '<div>'+u.ad+'</div>' +
        '</div>'
      );
    });
  });
  $(document).on('click', '.supurgelik', function(){
    $('.supurgelik').removeClass('selected');
    $(this).addClass('selected');
    checkSelection();
  });

  // Eviye Tipleri - Dinamik yükle
  $.post(window.efajax.ajaxurl, {action:'egemer_get_eviyetipleri'}, function(veriler){
    $('#eviyetipleri').html('');
    $.each(veriler, function(i, u){
      $('#eviyetipleri').append(
        '<div class="secimli eviyetipi" data-id="'+u.id+'">' +
          (u.resim ? '<img src="'+u.resim+'" alt="'+u.ad+'">' : '') +
          '<div>'+u.ad+'</div>' +
        '</div>'
      );
    });
  });
  $(document).on('click', '.eviyetipi', function(){
    $('.eviyetipi').removeClass('selected');
    $(this).addClass('selected');
    checkSelection();
  });

  // İlk yüklemede butonları kontrol et
  checkSelection();
});