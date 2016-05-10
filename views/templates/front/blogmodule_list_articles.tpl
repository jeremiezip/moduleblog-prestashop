	{if !empty($articles)}
		{foreach from=$articles item=article}
			<div id="content-list" class="col-md-12">
				<h4>{$article['title']}</h4>
				<p class='border-article'>{l s='Auteur' mod='blogmodule'} : {$article['firstname']} {$article['lastname']} | {l s='Publié dans :' mod='blogmodule'}  {if !empty($article['category'])}<a href='{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}/category/{$article['category']['id_article_category']}'>{$article['category']['name']}</a>{else}{l s="Non classé"}{/if} {l s="le"} <b>{$article['date_add']|date_format:'%d/%m/%Y'}</b></p>
			
				<div id="content-list">{$article['content']|strip_tags|truncate:500:'..':true}</div>
			<br/>
			<a class="button-custom" href="{$link->getModuleLink('blogmodule', 'display')|escape:'html':'UTF-8'}/{$article['id_article']}-{$article['link_rewrite']}">{l s='Lire plus'}</a>
			</div><div class="clearfix"></div>
			<br/><br/>

		{/foreach}


	 {else}
		{l s='Aucun article publié' mod='blogmodule'}
	{/if}
