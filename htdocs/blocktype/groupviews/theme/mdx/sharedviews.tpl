{*
   I wanted to put author_link_index in templates/author.tpl, but its
   state is non-persistent. So until Dwoo gets smarter...
*}
{assign var='author_link_index' value=1}
{foreach from=$items item=view}
    <div class="{cycle values='r0,r1'} listrow">
    {if $view.template}
        <div class="s fr">{$view.form|safe}</div>
    {/if}
		<span class="owner">
		<div class="friendcell">
			<a href="{profile_url($view.user)}">
			   <img src="{profile_icon_url user=$view.user maxwidth=60 maxheight=60}" alt="{str tag=profileimagetext arg1=$view.user|display_default_name}" title="{$view.user|display_default_name|escape}">
			</a>
		</div>
		</span>
		<span class="sharedpage">
        <h4 class="title"><a href="{$view.fullurl}">{$view.title}</a>
        {if $view.sharedby}
            <span class="owner"> {str tag=by section=view}
                {if $view.group}
                    <a href="{group_homepage_url($view.groupdata)}">{$view.sharedby}</a>
                {elseif $view.owner}
                    {if $view.anonymous}
                        {if $view.staff_or_admin}
                            {assign var='realauthor' value=$view.sharedby}
                            {assign var='realauthorlink' value=profile_url($view.user)}
                        {/if}
                        {assign var='author' value=get_string('anonymoususer')}
                        {include file=author.tpl}
                        {if $view.staff_or_admin}
                            {assign var='author_link_index' value=`$author_link_index+1`}
                        {/if}
                    {else}
                        <a href="{profile_url($view.user)}">{$view.sharedby}</a>
                    {/if}
                {else}
                    {$view.sharedby}
                {/if}
            </span>
        {/if}
        </h4>
     {if $view.tags}
        <div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$view.owner tags=$view.tags}</div>
     {/if}
     {if $view.artefacts}
     	<div class="bloginfo"> <a href="{$view.artefacts[0]['bloglink']}">{str tag=workbook section=blocktype.groupviews}</a> - {str tag=posts section=blocktype.groupviews arg1=$view.artefacts[0]["postcount"]} - {str tag=latestpost section=blocktype.groupviews} {$view.artefacts[0]["latestpost"]}</div>
     {/if}
        </span>
    </div>
{/foreach}
