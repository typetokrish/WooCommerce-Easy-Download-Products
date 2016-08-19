
/**
 * Plugin Name: WooCommerce - TK Easy Download Products
 * Plugin URI: https://github.com/typetokrish/WooCommerce-Easy-Downlod-Products
 * Author: Kiran Krishnan
 * Author URI: https://github.com/typetokrish/
 * Version: 1.0.0
 * License: GPL2
 * 
 * Text Domain: tk-easy-downloads
 * Domain Path: /languages
 *
 * @package         Easy-Download-For-Products
 * @author          Kiran Krishnan
 * @copyright       Copyright (c) 2016
 *
 */

//Display the download popup//

function tk_ed_show_popup(id){	
	if(jQuery('#tk_ed_popup').parent().length!=0){
		alert("Exists ex");
		var dynamicDialog=jQuery('#tk_ed_popup');
	}else{
		var dynamicDialog = jQuery('<div id="tk_ed_popup">--Loading--</div>');
	}
	jQuery.ajax({
		url: tk_ed_ajax.ajaxurl,
		type:'POST',
		data: 
		{
			'action': 'tk_ed_display_download_display',
			'data':   'foobarid',
			'id':id
		}, 
		dataType: 'html',
		success:function(response){
			
			dynamicDialog.html(response);
			jQuery('#tk_ed_button').click=function(){				
				tk_ed_validate_and_save();
			}
		}
	});
			
	dynamicDialog.dialog({
                    resizable: false,
                    modal: true,
                    show: 'clip',     
					minHeight: 300,
					minWidth:600             
    });
	
}
//Validate the email//

function tk_ed_validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
//Processs the download request form through ajax//

function tk_ed_validate_and_save(){	
	var name	=	jQuery('#tk_ed_name').val();
	var email	=	jQuery('#tk_ed_email').val();
	var id		=	jQuery('#tk_ed_hidden').val();
	var error=0;
	if(name.trim()==''){
		jQuery('#tk_ed_name').addClass('tk_ed_form_error');
		error++;
	}else{
		jQuery('#tk_ed_name').removeClass('tk_ed_form_error');
	}
	
	if(email.trim()==''){
		jQuery('#tk_ed_email').addClass('tk_ed_form_error');
		error++;
	}else{
		jQuery('#tk_ed_email').removeClass('tk_ed_form_error');
	}
	if(tk_ed_validateEmail(email)==false){
		jQuery('#tk_ed_email').addClass('tk_ed_form_error');
		error++;
	}else{
		jQuery('#tk_ed_email').removeClass('tk_ed_form_error');
	}
	
	if(error==0){
		jQuery('#tk_ed_frm_error').html('..Processing..');
		jQuery.ajax({
			url: tk_ed_ajax.ajaxurl,
			type:'POST',
			data: 
			{
				'action': 'tk_ed_submit_form',
				'email':  email,
				'name': name,
				'product': id
			}, 
			dataType: 'html',
			success:function(response){
				var json=jQuery.parseJSON(response);
				if(json.error==0){
					jQuery('#tk_ed_dwnsummary').css('display','block');
					jQuery('#tk_ed_dwnform').css('display','none');
					jQuery('#tk_ed_name').val('');
					jQuery('#tk_ed_email').val('');
					jQuery('#tk_ed_dwnsummary').html(json.message);
					jQuery('#tk_ed_frm_error').html('');
				}else{
					jQuery('#tk_ed_dwnsummary').css('display','none');
					jQuery('#tk_ed_dwnform').css('display','block');					
					jQuery('#tk_ed_frm_error').html(json.message);
				}
				
			}
		});
	}
	
}

