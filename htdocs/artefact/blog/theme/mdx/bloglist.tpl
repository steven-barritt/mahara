{foreach from=$blogs->data item=blog}
    <tr class="{cycle name=rows values='r0,r1'}">
        <td colspan="2">
            <div class="fr">
                <span class="entries"><a href="{$WWWROOT}artefact/blog/view/index.php?id={$blog->id}">{str tag=nposts section=artefact.blog arg1=$blog->postcount}</a></span>
                <span class="newentry">
                    {if !$blog->locked}
                <a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=1"><img src="{theme_url filename='images/photo.png'}" width="30px"/></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=2"><img src="{theme_url filename='images/text.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=3"><img src="{theme_url filename='images/link.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->id}&type=0"><img src="{theme_url filename='images/html.png'}" width="30px" /></a>
					{/if}
                </span>
                <span class="btns2">
                    {if $blog->locked}
                        <span class="s dull">{str tag=submittedforassessment section=view}</span>
                    {elseif  $limitedediting}
                    {else}
                        <a href="{$WWWROOT}artefact/blog/settings/index.php?id={$blog->id}" title="{str tag=settings}"><img src="{theme_url filename='images/manage.gif'}" alt="{str tag=settings}"></a>
                            {$blog->deleteform|safe}
                    {/if}
                </span>
            </div>
            <h4><a href="{$WWWROOT}artefact/blog/view/index.php?id={$blog->id}">{$blog->title}</a></h4>
            <div id="blogdesc">{$blog->description|clean_html|safe}</div>
        </td>
    </tr>
{/foreach}

