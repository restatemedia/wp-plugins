(function( $ ){

  $.fn.langControls = function( ops ) {  

    return this.each(function() {

      	var $this = $(this).empty();	
      	
      	var list = $('<ul />');
      	
      	var cur_lang = lang_config.lang;
      	
      	var langs = $this.data('options').split(',');
      	$.each(langs, function(key, val) {
      		var l = val.split(':');
      		var item = $('<li class="'+l[0]+'" data-lang="'+l[0]+'">'+l[1]+'</li>');
      		if (l[0] == cur_lang) item.addClass('disabled');
      		
      		var url = lang_config.home + '?lang=' + l[0];
      		item.click(function() {
      			document.location.href = url;
      		});
      		
      		list.prepend(item);
      	});
      	
      	$this.append(list);
			
    });

  };
  
  $(document).ready(function() {
  	$('.lang-controls').langControls();
  });
  
})( jQuery );

