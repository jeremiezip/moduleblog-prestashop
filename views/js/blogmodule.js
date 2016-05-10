$(document).ready(function()
{
	/* AJAX PAGINATION */
	$(".pagination li a").click(function(e)
	{
		e.preventDefault();
		valeur = this.text; 
		// Difficulte a recuperer l'URL, du coup j'utilise cette'ritournelle pour la récupérer dans un champs hidden mis dans le tpl !
		var blog_link = $('#blog_link').val();
		console.log(blog_link);   
		$.ajax({
		  type: 'GET',
		  url: blog_link ,
		headers: { "cache-control": "no-cache" },
		async: true,
		cache: false,
		  data: 'ajax=true&page='+valeur,
			  success: function(data) {
		    $('#articles_list').html(data); // update the DIV
		    console.log(data);
		  },
		  error: function(){
		  	console.log('error');
		  }

		});
	});

});
