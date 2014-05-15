var file_frame;
var rm = {
	
	initUI: function(ob) {
		var me = this;
		ob = (ob) ? ob : jQuery('.ui');
		ob.each(function() {
			me.addHover(this);
			var el = jQuery(this);
  			if (el.data('action')) {
  				el.click(function() {
  					jQuery.globalEval(jQuery(this).data('action'));
  				});
			} else if (el.data('href')){
				el.click(function() {
					var item = jQuery(this);
					rm.goto(item.data('href'), item.data('target'));
				});
			}
		});
	},
	
	goto: function(url, target) {
		if(target == '_blank') window.open(url);
		else document.location.href = url;
	},
	
	addHover: function(item) {
		jQuery(item).hover(function() {
			jQuery(this).addClass('over');
		}, function() {
			jQuery(this).removeClass('over');
		});
	},
	
	scrollPageTo: function(num, time) {
		if (time) {
			jQuery("html, body").animate({ scrollTop: num }, time);
		} else {
			jQuery("html, body").scrollTop(num);
		}
		
	},
	
	openMediaBrowser: function(ob, options, callback) {

			
		event.preventDefault();
		
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}
		
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: options.title || jQuery( this ).data( 'uploader_title' ),
		  button: {
		    text: options.button || jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: options.multiple || false  // Set to true to allow multiple files to be selected
		});
		
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();
		
		  callback(attachment);

		});
		
		// Finally, open the modal
		file_frame.open();
			
		
	}

};

jQuery(document).ready(function() {
	rm.initUI();
});


(function( $ ){

  $.fn.imagePicker = function( ) {  

    return this.each(function() {

      	var $this = $(this);
      	var data = $this.data();
      
      	$this.append('<div class="label">Select image...</div>');
      	$this.append('<div class="image" />');
      	$this.append('<input type="hidden" name="'+data.name+'" value="'+data.id+'" />');
      	
      	$this.click(function(e) {
      	
      		var picker = $(this);
      		
      		if (picker.find('.image').is(':visible')) {
      			picker.find('.image').hide();
      			picker.find('input').val('');
      		} else {
      			rm.openMediaBrowser(picker, {title:picker.attr('title'), button:"Select", multiple:false},
      			function(attachment) {
      				if (data.callback) {
      					$.globalEval(data.callback+'(attachment);');
      				} else {
      					picker.find('.image').css('background-image', 'url('+attachment.url+')')
      						                 .show();
      					picker.find('input').val(attachment.id);
      				}
      			});
      		}
      	});
		
		if (data.url) {
      		$this.find('.image').css('background-image', 'url('+data.url+')');	
      		$this.find('p').show();
      	} else {
      		$this.find('p').hide();
      	}
      	
      	if (data.id) {
      		$this.find('input').val(data.id);
      	}
			
    });

  };
  
  $.fn.wait = function(time, type) {
	    time = time || 1000;
	    type = type || "fx";
	    return this.queue(type, function() {
	        var self = this;
	        setTimeout(function() {
	            $(self).dequeue();
	        }, time);
	    });
  };
  
  $.fn.calcWidth = function() {
  
  	return this.each(function() {
  	
	  	var $this = $(this);
	  	
	  	var w = 0;
	  	$this.children().each(function() {
	  		w += $(this).outerWidth(true);
	  	});
	  	
	  	$this.outerWidth(w);
	  	
  	});
  	
  };
  
  $.fn.pager = function() {
  	
  	return this.each(function() {
  	  	
	  	var $this = $(this);
	  	var data = $this.data();
	  	var items = [];
	  		  	
	  	$this.hide();
	  	
	  	if (data.pages > 1) {
	  	
	  		// Check if the items are static
	  		if ($this.children().length > 0) {
	  			$this.children().each(function(index) {
	  				var item = {label:$(this).html(), url:$(this).data('href')};
	  				items.push(item);
	  			});	  				  			
	  		} 
	  			
  			$this.addClass('pager');
  	
	  		$this.show();
	  		$this.empty();
	  		
	  		if (!data.cur) data.cur = 1;
	  		
	  		if (data.cur_label) {
	  		
	  			var item = items[data.cur-1] || {label:''};
	  			var current = $('<ul class="cur_page" />');
	  			current.append($('<li>'+data.cur_label.replace('%s', item.label)+'</li>'));
	  			
	  			$this.append(current);
	  			$this.find('.cur_page').calcWidth();
	  		
	  		} else {
	  		
	  			var pages = $('<ul class="pages" />');
		  		for(var i=0; i<data.pages; i++) {
		  			var p = i+1;
		  			var item = $('<li class="ui" data-value="'+p+'">'+p+'</li>');
		  			
		  			if (p == data.cur) item.addClass('on');
		  			
		  			item.click(function() {
		  				var page = jQuery(this).data('value');
		  				document.location.href = (data.url.indexOf('?id=') != -1) 
		  										 ? data.url+'&page='+page
		  										 : data.url+'?page='+page;
		  																					
		  			});
		  			
		  			pages.append(item);
		  			
		  		}
		  		
		  		$this.append(pages);
		  		$this.find('.pages').calcWidth();
	  		
	  		
	  		}
	  		
	  		
	  		var next = jQuery('<div class="nav next" />');
	  		var next_item = items[data.cur] || false;
	  		if (data.next) {
	  			if (next_item) {
	  				next.append('<label>'+data.next.replace('%s', next_item.label)+'</label>'); 
	  			} else {
	  				next.append('<label>'+data.next+'</label>');
	  			}
	  			
	  		} else {
	  			next.append('<div class="icon" />');
	  		}
	  		
	  		if (data.cur < data.pages) {
	  			if (next_item) {
	  				next.click(function() {
		  				document.location.href = next_item.url;
			  		});
	  			} else {
		  			next.click(function() {
		  				var page = data.cur+1;
		  				var symbol = (data.url.indexOf('?') != -1) ? '&' : '?';
		  				document.location.href = data.url+symbol+'page='+page;
			  		});
		  		}
	  		} else {
	  			next.addClass('disabled');
	  		}
	  		$this.append(next);
	  		
	  		var prev = jQuery('<div class="nav prev" />');
	  		var prev_item = items[data.cur-2] || false;
	  		if (data.prev) {
	  			if (prev_item) {
	  				prev.append('<label>'+data.prev.replace('%s', prev_item.label)+'</label>'); 
	  			} else {
	  				prev.append('<label>'+data.prev+'</label>');
	  			}
	  		} else {
	  			prev.append('<div class="icon" />');
	  		}
	  		if (data.cur > 1) {
	  			if (prev_item) {
	  				prev.click(function() {
		  				document.location.href = prev_item.url;
			  		});
	  			} else {
	  				prev.click(function() {
		  				var page = data.cur-1;
		  				var symbol = (data.url.indexOf('?') != -1) ? '&' : '?';
		  				document.location.href = data.url+symbol+'page='+page;
			  		});
	  			}
	  			
	  		} else {
	  			prev.addClass('disabled');
	  		}
	  		$this.append(prev);
	  			
	  		
	  		
	  	
	  	
	  		
	  		
	  	}
	  	
  	});
  	
  };
  
  $(document).ready(function() {
  	$('.rm-image-picker').imagePicker();
  	$('.rm-calc-width').calcWidth();
  	$('.rm-pager').pager();
  });

  
})( jQuery );

		
