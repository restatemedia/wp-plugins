(function( $ ){

  $.fn.faq = function( ops ) {  
  
  	var faq_speed = 400;

    return this.each(function() {

      	var $this = $(this);
  
  		var question = $this.find('li.question');
  		question.addClass('ui');
  		
  		question.click(function(e) {
  			var q = jQuery(this);
  			var answer = q.find('.answer');
  			if (ops.auto_collapse) {
  			
  				if (q.hasClass('on')) {
  				
  					q.removeClass('on');
  					answer.slideUp(faq_speed);
  				
  				} else {
  				
  					answers = jQuery('.question.on .answer');
	  				if (answers.length > 0) {
	  					answers.slideUp(faq_speed, function() {
		  					jQuery(this).parent().removeClass('on');
		  				});
		  				
	  				} 
	  				
	  				answer.slideDown(faq_speed);
		  			q.addClass('on');
  				
  				}
  				
  				
  				
  				
  			} else {
  				if (answer.is(':visible')) {
	  				jQuery(this).removeClass('on');
	  				answer.slideUp(faq_speed);
	  			} else {
	  				jQuery(this).addClass('on');
	  				answer.slideDown(faq_speed);
	  			}
  			}
  
  		});
  		

    });

  };
})( jQuery );
