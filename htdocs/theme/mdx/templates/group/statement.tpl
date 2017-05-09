{include file="header.tpl"}
  {if $statementdata}
    	{foreach from=$statementdata item=statement}
    	<h4>
    	{$statement->firstname} {$statement->lastname}
    	</h4>
    	<p>
    	{$statement->statement|safe}
    	</p>
    	<br/>
    	<br/>
    	{/foreach}
  {else}
  	bob
  {/if}

{$pagination|safe}


{include file="footer.tpl"}
