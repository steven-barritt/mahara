{if $VIEWS}
    <div id="userviewstable" class="viewlist fullwidth listing">
    {foreach from=$VIEWS item=item name=view}
            <div class="{cycle values='r0,r1'} {if $item.submittedtime}viewsubmitted{/if} listrow">
            	<div class="viewcontainer">
                <h3 class="title"><a href="{$item.fullurl}">{$item.title}</a></h3>
                <span class="submitted">{if $item.submittedtime}
                	<strong>{str tag=viewsubmittedon section=view arg1=$item.submittedtime|format_date:'strftimerecentyear'}</strong>
                {if $item.submittedtime && ($isstaff || $owner)}<div class="gradecontainer">{if $item.published}{str tag=grade section=blocktype.myviews}<div class="grade">{$item.grade}</div>{else}{str tag=notgraded section=blocktype.myviews}{/if}</div>{/if}
                {/if}
                </span>
                {if $item.tags}
                  <div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$item.owner tags=$item.tags}</div>
                {/if}
				 {if $item.artefacts}
					<div class="bloginfo"> <a href="{$item.artefacts[0]['bloglink']}">{str tag=workbook section=blocktype.groupviews}</a> - {str tag=posts section=blocktype.groupviews arg1=$item.artefacts[0]["postcount"]} - {str tag=latestpost section=blocktype.groupviews} {$item.artefacts[0]["latestpost"]}</div>
				 {else}
					<div class="bloginfo">&nbsp;</div>
				 {/if}
				 </div>
            </div>
    {/foreach}
    </div>
{else}
    {str tag='noviewstosee' section='group'}
{/if}

