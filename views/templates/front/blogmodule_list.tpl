<!-- @TODO : récuperer le bon lien réécrit -->
{capture name=path}<a href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}">{l s='Blog'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Derniers articles'}</span>{/capture}


<!-- HOOK INCLUDE -->
{hook h='LeftColumnBlogFrontController'}
<!-- COLUMN RIGHT BLOG MODULE--> 
<div class ="col-md-10">
	<div class="col-md-12">
		<h3>
			{if empty($displayTitleCategory)}
				{l s='Blog : Liste des articles' mod='blogmodule'}
			{else}
				{l s='Catégorie :' mod='blogmodule'} {$displayTitleCategory['name']} 
			{/if}
		</h3>
	</div><br/>
	<div id="articles_list">
		{include file='./blogmodule_list_articles.tpl'}
	</div>
	<!-- @TODO : pagination article -->
	<nav>

	<ul class="pagination">
	    {for $i=1 to $nbr_page}
	    <li><a href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}/page/{$i}">{$i}</a></li>
	    {/for}
	</ul>
	</nav>

	<input type="hidden" id="blog_link" value='{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}'>
</div>