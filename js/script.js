var is_stop = false;


jQuery(document).ready(function(){
    
     jQuery('.wc_bpd_delete_btn').click(function(){
         jQuery( "div.error_div" ).hide();
         jQuery(this).attr('disabled','disabled');
         jQuery('div#message_update').fadeIn('slow');
         is_stop = false;
         var data = jQuery('form#wc_bpd_form').serialize();
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                timeout: 1000*60,
                success: function(responses) {
                    var final = JSON.parse(responses);
                    var text = '';
                    var string = 'Total : ' + final['total'] + ' | Success : ' + final['success'] + ' | Error : ' + final['error'];
                    jQuery('div#message_update p.data').html(string);        

                    if(final['error_ids'] != ''){
                    for (i = 0; i < final['error_ids'].length; i++) {
                        text += "<li>" + final['error_ids'][i] + "</li>";
                    }
                     jQuery( "div.error_div ul" ).html(text);
                    jQuery( "div.error_div" ).fadeIn();
                    }
                    
                   

                    jQuery( "div.error_div ul" ).html(text);                    
                    jQuery('div#message_update .spinner').remove();
                    jQuery('.wc_bpd_delete_btn').removeAttr('disabled');
                } 
            }); 
         start_check();
     });
});

function start_check(){
    setTimeout(function(){check_status();},100);
}


function check_status(){
    var data = {
			'action': 'wc_bpd_status'
		};
    jQuery.post(ajaxurl, data, function(response) {
        var post_data = JSON.parse(response);
        var text = '';
        var string = 'Total : ' + post_data['total'] + ' | Success : ' + post_data['success'] + ' | Error : ' + post_data['error'];
        jQuery('div#message_update p.data').html(string);    
        
        if(post_data['error_ids'] != ''){
        for (i = 0; i < post_data['error_ids'].length; i++) {
            text += "<li>" + post_data['error_ids'][i] + "</li>";
        }
        jQuery( "div.error_div ul" ).html(text);
        jQuery( "div.error_div" ).fadeIn();
        
        }
        
        
        if(post_data['status'] == 'stop'){
            
            return true;        
        }                         
        check_status();
        
        
    });
}