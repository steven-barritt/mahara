{foreach from=$blogs->groupdata item=blog}
    <tr class="{cycle name=rows values='r0,r1'}">
        <td>
            <h4><a href="{$WWWROOT}artefact/blog/view/index.php?id={$blog->id}">{$blog->title}</a> - <a href="{$WWWROOT}group/view.php?id={$blog->groupid}">{$blog->groupname}</a></h4>
            <div id="blogdesc">{$blog->description|clean_html|safe}</div>
        </td>
        <td class="right">
                <span class="entries"><a href="{$WWWROOT}artefact/blog/view/index.php?id={$blog->id}">{str tag=nposts section=artefact.blog arg1=$blog->postcount}</a></span>
        </td>
        <td class="right" colspan="2">
            <div class="fr">
                <span class="newentry">
                    {if !$blog->locked}
                <a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=1"><img src="{theme_url filename='images/photo.png'}" width="30px"/></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=2"><img src="{theme_url filename='images/text.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=3"><img src="{theme_url filename='images/link.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=0"><img src="{theme_url filename='images/html.png'}" width="30px" /></a>
					{else}
                        <span class="s dull">{str tag=submittedforassessment section=view}</span>
					{/if}
                </span>
            </div>
        </td>
				{if !$limitedediting && !$blog->locked}
		        <td class="right">
                <span class="right btns2">
                        <a class="btn-big-settings" href="{$WWWROOT}artefact/blog/settings/index.php?id={$blog->id}" title="{str tag=settings}"></a>
                            {$blog->deleteform|safe}
                </span>
        </td>
                    {else}
		        <td class="right">
		        </td>
                    {/if}
    </tr>
{/foreach}

