{capture name=path}<a href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}">{l s='Blog'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{$article['title']}</span>{/capture}

{hook h='LeftColumnBlogFrontController'}

<article class="col-md-10">
	<h3>{$article['title']}</h3>	

	<p class='border-article'>
		{l s='Auteur' mod='blogmodule'} : {$article['firstname']} {$article['lastname']} | {l s='Publié dans :' mod='blogmodule'}  {if !empty($article['category'])}<a href='{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}/category/{$article['category']['id_article_category']}'>{$article['category']['name']}</a>
		{else}{l s="Non classé"}{/if} {l s="le"} <b>{$article['date_add']|date_format:'%d/%m/%Y'}</b>
	</p>
	{$article['content']}		<div style="clear: both;"></div>
<br/>

	<section>
		{hook h='CommentsArticleFrontController'}
	</section>

	<!-- RETOURNER A LA LISTE DES ARTICLES -->
	<a class="btn btn-default button button-small" href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}" title="Retour à l'accueil " rel="nofollow"><span><i class="icon-chevron-left"></i>{l s='Revenir à la liste des articles' mod='blogmodule'} </span></a>

</article>