jQuery(function($){
	  $('#userank_user_date_filter').change(function() {
        $.ajax({
            url: "/wp-admin/admin-ajax.php",
            type: 'POST',
            data: {
                action: 'ajaxFilterUserRanking',
                t: this.value,
                n: $('#userank_query_limit').val(),
            },
            success: function(data) {
                $('#userank-user-ranking').html(data);
            }
        });
    });
	$(document).ready(function() {
		var toreplace = $('#comments').html();
		var textArea = document.createElement('textarea');
		textArea.innerHTML = toreplace;
			
		$('#comments').html(textArea.value);
	});	
});