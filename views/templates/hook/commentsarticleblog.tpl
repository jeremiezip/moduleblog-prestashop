
<h4>{l s='Liste des commentaires :' mod='blogmodule'}</h4>

{if !empty($comments)}
	{foreach from=$comments key=k item=comment}
		<div class="comments">
			<div class="comments-title"> #{$k}/ {$comment['title']}</div>
			{l s='Publié le'} {$comment['date_add']|date_format:'%d/%m/%Y'} {l s='par' mod='blogmodule'} {$comment['firstname']} {$comment['lastname']}<br/>
			{$comment['content']}
		</div>
	{/foreach}
{else}
{l s='Pas de commentaires.' mod='blogmodule'}<br/><br/>
{/if}

{if !$logged}
	{l s='Veuillez vous identifiez pour pouvoir poster des commentaires.' mod='blogmodule'}
	<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Se connecter' mod='blogmodule'}">{l s='Se connecter' mod='blogmodule'}</a>
{else}
	{include file="$tpl_dir./errors.tpl"}

	<form action="" method="post">
		<label for="title">
			{l s='Titre de votre commentaire' mod='blogmodule'}
		</label><br>
		<input type="text" name="title" placeholder="{l s='Max. 100 caractères.' mod='blogmodule'}" maxlength="100"/><br/><br/>
		<label for="title">
			{l s='Votre commentaire' mod='blogmodule'}
		</label><br/>
		<textarea name="content" rows="4" cols="50" placeholder="{l s='Max 700 caractères' mod='blogmodule'}" maxlength="700"/></textarea>
		<br/>
		<br/>
		<input type="submit"/>
	</form>
{/if}
<br/>
<br/>