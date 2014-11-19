{if $VIEWS}
    <div id="userviewstable" class="viewlist fullwidth listing">
    {foreach from=$VIEWS item=item name=view}
            <div class="{cycle values='r0,r1'} listrow">
                <h3 class="title"><a href="{$item.fullurl}">{$item.title}</a></h3>
                {if $item.tags}
                  <div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$item.owner tags=$item.tags}</div>
                {/if}
				 {if $view.artefacts}
					<div class="bloginfo"> <a href="{$view.artefacts[0]['bloglink']}">{str tag=workbook section=blocktype.groupviews}</a> - {str tag=posts section=blocktype.groupviews arg1=$view.artefacts[0]["postcount"]} - {str tag=latestpost section=blocktype.groupviews} {$view.artefacts[0]["latestpost"]}</div>
				 {/if}
            </div>
    {/foreach}
    </div>
{else}
    {str tag='noviewstosee' section='group'}
{/if}

