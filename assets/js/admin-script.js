jQuery(function($){
  // Resim önizleme
  $('input[type="file"]').on('change', function(e){
    let input = $(this);
    if (this.files && this.files[0]) {
      let reader = new FileReader();
      reader.onload = function(ev){
        let preview = input.closest('td').find('.egemer-img-preview');
        if (!preview.length) {
          preview = $('<div class="egemer-img-preview"></div>').appendTo(input.closest('td'));
        }
        preview.html('<img src="'+ev.target.result+'" class="admin-egemer-image-thumb" />');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Marka formunda ürün seçilmeden ekleme engelle
  $('#egemer_marka_form').on('submit', function(){
    if ($('#egemer_marka_urun').val() == '') {
      alert('Lütfen önce bir ürün seçin.');
      return false;
    }
  });
  // Renk formunda marka seçilmeden ekleme engelle
  $('#egemer_renk_form').on('submit', function(){
    if ($('#egemer_renk_marka').val() == '') {
      alert('Lütfen önce bir marka seçin.');
      return false;
    }
  });

  // Ürün seçilince markaları getir
  $('#egemer_marka_urun').on('change', function(){
    let urun_id = $(this).val();
    let marka_select = $('#egemer_marka_ad');
    marka_select.empty();
    if (!urun_id) return;
    $.post(ajaxurl, {action:'egemer_get_markalar', urun_id:urun_id}, function(r){
      marka_select.append('<option value="">--- Seçiniz ---</option>');
      $.each(r, function(i, m){
        marka_select.append('<option value="'+m.id+'">'+m.ad+'</option>');
      });
    });
  });
  // Marka seçilince renkleri getir
  $('#egemer_renk_marka').on('change', function(){
    let marka_id = $(this).val();
    let renk_select = $('#egemer_renk_ad');
    renk_select.empty();
    if (!marka_id) return;
    $.post(ajaxurl, {action:'egemer_get_renkler', marka_id:marka_id}, function(r){
      renk_select.append('<option value="">--- Seçiniz ---</option>');
      $.each(r, function(i, m){
        renk_select.append('<option value="'+m.id+'">'+m.ad+'</option>');
      });
    });
  });
});