var cform = {
	submit: function() {
		jQuery('#rm-form').submit();
		return false;
	},
	reset: function() {
		jQuery('#rm-form').get(0).reset();
	},
	callback: function(res) {
		if (res.result) {
			this.show('success');
		} else {
			this.show('error');
		}
	},
	show: function(type) {
		var form = jQuery('#rm-form');
		switch(type) {
			case 'form':
				form.find('.form-body').show();
				form.find('.form-success').hide();
				form.find('.form-error').hide();
				break;
			case 'success':
				form.find('.form-body').hide();
				form.find('.form-success').show();
				form.find('.form-error').hide();
				break;
			case 'error':
				form.find('.form-body').hide();
				form.find('.form-success').hide();
				form.find('.form-error').show();
				break;
		}
	}
};

jQuery(document).ready(function() {
	if (jQuery.prototype.validate != undefined) {
		jQuery('#rm-form').validate({
			errorClass: "rm-error",
			submitHandler: function(form) {
				var f = jQuery(form);
				var data = f.serialize();
				jQuery.post( "/api/rm.form.process?format=json", data, function( res ) {
				   cform.callback(jQuery.parseJSON(res));
				});
				
			}
		});
	}
	
});