
(function($){
 
    $.fn.extend({ 
         
        gnModal: function(options) {
 
            var defaults = { top: 150 }
              
            options =  $.extend(defaults, options);
 
            return this.each(function() {
               
                $(this).click(function(e) {
              
					var modal_id = $(this).attr("modal_id");
					var modal_width = $(modal_id).outerWidth();
					var gn_overlay = "#gn_overlay";
					var closeButton = ".gn_close_modal";

					$(gn_overlay).css({ 'display' : 'block', opacity : 0 });

					$(gn_overlay).fadeTo(200, 0.5);

					$(modal_id).css({ 
					
						'display' : 'block',
						'opacity' : 0,
						'left' : 50 + '%',
						'margin-left' : -(modal_width/2) + "px",
						'top' : options.top + "px"
					
					});

					$(modal_id).fadeTo(200,1);

					$(closeButton).click(function() { gn_close_modal(modal_id); });
					
					$(gn_overlay).click(function() { gn_close_modal(modal_id); });

					e.preventDefault();
                		
              	});
             
            });

			function gn_close_modal(modal_id){

        		$(gn_overlay).fadeOut(200);

        		$(modal_id).css({ 'display' : 'none' });
			
			}
    
        }
    });
     
})(jQuery);

jQuery(document).ready(function(){

	if( jQuery('.gn_show_authorization_modal').length >= 1 ){ jQuery(".gn_show_authorization_modal").gnModal(); }

});

