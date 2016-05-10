{extends file="helpers/form/form.tpl"}
{block name="input"}
	{if $input.name == "link_rewrite"}
		<script type="text/javascript">
		{if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		{else}
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		{/if}
		</script>
		{$smarty.block.parent}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="script"}
</script>
<script>
$(document).ready(function(){
	$(".copyNiceUrl").live('keyup change',function(e){
		if(!isArrowKey(e))
			return copyNiceUrl();
	});
});
function copyNiceUrl()
{
	$('#link_rewrite_' + id_language).val(str2url($('#name_' + id_language).val().replace(/^[0-9]+\./, ''), 'UTF-8'));
}	
</script>
{/block}