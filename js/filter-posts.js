
jQuery(document).ready(function(e) {
    jQuery("#selecttaxterm").change(function () {
        var x = jQuery('select option:selected').attr('value');
        jQuery.ajax({
			type: "POST",
			data: {
				id:     x,
				action: 'myajax-submit',
				nonce : ajaxdata.nonce
			},
			url: ajaxdata.url,
			dataType: 'text',
			success: function(res){
			    //alert(res);
			    var j = jQuery(".entry-content");
			    jQuery(j).hide();
			    j.html(res);
			    jQuery(j).show('slow');
			  },
			error: function(){
				alert('Error!');
			}
		});
    })
});