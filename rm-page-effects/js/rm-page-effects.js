(function( $ ){

  $.fn.accordian = function( ) {  

    return this.each(function() {

      	var $this = $(this);	
      	//$this.addClass('rm-accordian');
      	
  		$this.children('ul').children('li').each(function(index) {

  			var link = $(this).find('a:eq(0)').detach();
  			link.click(function(e) {
  				jQuery('#content-'+index).slideToggle();
  				e.preventDefault();
  			});
  			link = jQuery('<div />').append(link);
  			
  			$(this).contents().wrapAll('<div class="rm-accordian-content" />');
  			$(this).prepend(link);
  			$(this).find('.rm-accordian-content').each(function() {
  			 	$(this).attr('id', 'content-'+index);
  			 	$(this).hide();
  			});
  			
 
  		});
			
    });

  };
  
  $.fn.notes = function( ) {  

    return this.each(function() {

      	var $this = $(this);	
      	
      	var list = $this.find('ol').detach();
      	$this.empty();
      	$this.append('<hr />');
      	
      	var start = $this.data('start')-1 || 0;
      	
  		list.find('li').each(function(index) {
  			
  			var num = index+1 + start;
  		
  			var item = $('<p />');
  			item.append('<sup id="note-'+num+'">'+num+'</sup>');
  			item.append($(this).contents());
  			
  			$this.append(item);
  			
 
  		});
			
    });
    
  };
  
  $.fn.tabs = function( ) {  
  
  	$('.rm-tab-content').hide();

    return this.each(function() {
    
    	var id = $(this).data('id');    
    	var toggle = $(this).data('toggle') || 'simple';	
    	var action = $(this).data('action');
    	
    	var use_links = $(this).data('use_links') || false;

      	$(this).find('li').each(function(index) {
      		$(this).addClass('rm-tab ui');
      		
      		if (use_links) {
      		
      			$(this).click(function() {
      				rm.goto($(this).data('href'));
      			});
      		
      		} else {
      		
      			$(this).click(function() {
  			
	  			
	  				$('.rm-tab').removeClass('over');
	      			$(this).addClass('over');
	      			
	      			var prev = $('.rm-tab-content.open');
	      			var cur = $('#'+id+'-'+(index+1));
	      			
	      			prev.removeClass('open');
	      			cur.addClass('open');
	      			
	      			if (toggle == 'slide') {
	      				prev.slideUp(function() {
	      					cur.slideDown();
	      				});
	      			} else {
	      				prev.hide();
	      				cur.show();
	      			}
	      			
	      			if (action) {
	      				$.globalEval(action);
	      			}
	      			
	      			
	      		});
      		
      		
      		}
      		
      		
      	
      	});
      	
      	var def = $(this).data('default') || 1;
      	var def_tab = $(this).find('li:eq('+(def-1)+')');
      	
      	if (!use_links) {
      		def_tab.click();
      	} else {
      	
      		$('.rm-tab').removeClass('over');
	      	def_tab.addClass('over');
 
      		var prev = $('.rm-tab-content.open');
  			var cur = $('#'+id+'-'+(def));
  			
  			console.log(cur);
  			
  			prev.removeClass('open');
  			cur.addClass('open');
  			
  			if (toggle == 'slide') {
  				prev.slideUp(function() {
  					cur.slideDown();
  				});
  			} else {
  				prev.hide();
  				cur.show();
  			}
      	}
			
    });
    
  };
  
  $.fn.more = function( ) {  

    return this.each(function() {
    
    	var o = $(this).find('span.open');
    	var c = $(this).find('span.close');
    	var more = $(this).next('.rm-more');
    	
    	o.click(function() {
    		$(this).hide();
    		c.show();
    		more.slideToggle();
    	});
    	c.click(function() {
    		$(this).hide();
    		o.show();
    		more.slideToggle();
    	});
      	
			
    });
    
  };
  
  $.fn.embed = function( ops ) {  

    return this.each(function() {

      	var $this = $(this);
  
		switch(ops.type) {
			case 'gmap':
				var html = '<iframe width="'+ops.w+'" height="'+ops.h+'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+ops.url+'&amp;output=embed"></iframe>';
				break;
			case 'vimeo':
				var html = '<iframe src="http://player.vimeo.com/video/'+ops.id+'" width="'+ops.w+'" height="'+ops.h+'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
				break;
			case 'youtube':
			default:
				var html = '<iframe width="'+ops.w+'" height="'+ops.h+'" src="http://www.youtube.com/embed/'+ops.id+'?rel=0" frameborder="0" allowfullscreen></iframe>';
				break;
		}
		$this.html(html);

    });

  };
  
  
  $(document).ready(function() {
  
  	$('.rm-accordian').accordian();
  	$('.rm-notes').notes();
  	$('.rm-tabs').tabs();
  	$('.rm-more-toggle').more();
  
  });

  
})( jQuery );