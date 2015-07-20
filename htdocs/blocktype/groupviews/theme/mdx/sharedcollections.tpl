{foreach from=$items item=collection}
    <div class="{cycle values='r0,r1'} listrow">
        <h4 class="title"><a href="{$collection.fullurl}">{$collection.name}</a>
        </h4>
        <div class="detail">{$collection.description|str_shorten_html:100:true|strip_tags|safe}</div>
     {if $collection.views['count'] > 1}
     {foreach from=$collection.views['views'] item=view}

	    <div class="{cycle values='r0,r1'} listrow ">
	    	<div class="page_title"><a href="{$view->fullurl}">{$view->title}</a></div>
	    </div>
	    
	    {/foreach}
	    {/if}
     {if $collection.tags}
        <div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$collection.owner tags=$collection.tags}</div>
     {/if}
    </div>
{/foreach}
