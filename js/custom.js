(function ( $ ) {
	'use strict';

	$( function () {
		// Document ready!
		$('table').addClass('table table-sm table-bordered').wrap('<div class="table-responsive"></div>');
		
		if($('body').hasClass('tml-action-login')) {
			$('.tml-button').addClass('btn btn-dark').css('min-width', '118px');
		}

		// $('.grantslist > li').hide();
                // $('.grantslist .current').show();

		if($('.grantsform').length) {
		  $('.grantsform select').on('change', function(e) {
			  var cl = [];
		    $('.grantsform select option:selected').each(function(i,e) {
		      var el = $(e);
		      if(el.index() > 0) {
			      cl.push($(e).val());    
		      }
		    });
		    if(cl.length) {
		      $('.grantslist > li').hide();
		      $('.grantslist .' + cl.join('.')).show();
		    } else {
		      $('.grantslist > li').show();
		    }
		  });
		}
	} );

})( jQuery );
