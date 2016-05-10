<div class="col-md-2">
	<h3>{l s='Les catégories' mod='blogmodule'}</h3>
	{foreach from=$categories item=category}
	<a href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}/category/{$category.id_article_category}">{$category.name}</a><br/>
	{/foreach} <br/>
	<a href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}" style="color: #5EA03C;" />
		{l s='Tous les articles' mod='blogmodule'}
	</a>
</div>