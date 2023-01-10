
function add_sort_capability_to_tables($) {
  console.log("HI THERE!");
  var headers = document.getElementsByTagName("th");
  for (var i=0; i<headers.length; ++i) {
    headers[i].onclick = function() {
      console.log(this.innerHTML);
      var col_index = Array.prototype.indexOf.call(this.parentElement.children, this);
      console.log(col_index);
      var table = this.closest('table');
      console.log(table);
      var sorted = false;
      while(!sorted) {
        sorted = true;
        for (var row_index=1; row_index < table.rows.length - 1; row_index++) {
          var row0 = table.rows[row_index];
          var row1 = table.rows[row_index+1];
          var text0 = row0.getElementsByTagName("td")[col_index];
          var text1 = row1.getElementsByTagName("td")[col_index];
          if (text0.innerHTML.toLowerCase() > text1.innerHTML.toLowerCase()) {
	    sorted = false;
            row0.parentNode.insertBefore(row1, row0);
          }
        }
      }
    }
  }
}


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

	      add_sort_capability_to_tables($);
	} );

})( jQuery );
