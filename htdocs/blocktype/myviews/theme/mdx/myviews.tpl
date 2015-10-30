{if $VIEWS}
    <div id="userviewstable" class="viewlist fullwidth listing">
    {foreach from=$VIEWS item=item name=view}
            <div class="{cycle values='r0,r1'} {if $item.submittedtime}viewsubmitted{elseif $item.duedate}viewlate{/if} listrow">
            	<div class="viewcontainer">
                <h3 class="title"><a href="{$item.fullurl}">{$item.title}</a></h3>
                <span class="submitted">{if $item.submittedtime}
                	<strong>{str tag=viewsubmittedon section=view arg1=$item.submittedtime|format_date:'strftimerecentyear'}</strong>
                {if $item.submittedtime && ($isstaff || $owner) && $item.visible}<div class="gradecontainer">{if $item.published}{str tag=grade section=blocktype.myviews}<div class="grade">{$item.grade}</div>{else}{str tag=notgraded section=blocktype.myviews}{/if}</div>{/if}
                {elseif $item.duedate}
                	<strong>{str tag=viewislate section=blocktype.myviews arg1=$item.duedate|format_date:'strftimerecentyear'}</strong>
                {/if}
                </span>
                {if $item.tags}
                  <div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$item.owner tags=$item.tags}</div>
                {/if}
				 {if $item.artefacts}
					<div class="bloginfo"> <a href="{$item.artefacts[0]['bloglink']}">{str tag=workbook section=blocktype.groupviews}</a>{if $item.artefacts[0]["postcount"] != null} - {str tag=posts section=blocktype.groupviews arg1=$item.artefacts[0]["postcount"]} - {str tag=latestpost section=blocktype.groupviews} {$item.artefacts[0]["latestpost"]}{else} - {str tag=posts section=blocktype.groupviews arg1=0}{/if}</div>
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

