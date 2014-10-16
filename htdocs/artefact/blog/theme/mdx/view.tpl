{include file="header.tpl"}
			<div id="myblogs rel">

                <div class="rbuttons">
                {if !$blog->get('locked')}
                <a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=1"><img src="{theme_url filename='images/photo.png'}" width="30px"/></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=2"><img src="{theme_url filename='images/text.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=3"><img src="{theme_url filename='images/link.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=0"><img src="{theme_url filename='images/html.png'}" width="30px" /></a>
					{if !$limitedediting}
                    <a class="btn settings" href="{$WWWROOT}artefact/blog/settings/index.php?id={$blog->get('id')}">{str section="artefact.blog" tag="settings"}</a>
                    {/if}
                {/if}
				</div>

                <div id="blogdescription">{clean_html($blog->get('description'))|safe}
                {if $blog->get('tags')}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$blog->get('owner') tags=$blog->get('tags')}</p>{/if}</div>

                {if $posts}
				<div id="postlist" class="postlist">
                    {$posts.tablerows|safe}
				</div>
                <div id="blogpost_page_container" class="hidden">{$posts.pagination|safe}</div>
<script>
addLoadEvent(function() {literal}{{/literal}
    {$posts.pagination_js|safe}
    removeElementClass('blogpost_page_container', 'hidden');
{literal}}{/literal});
</script>
                {else}
                <div class="message">
                  {str tag=nopostsyet section=artefact.blog} {if !$blog->get('locked')}<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}">{str tag=addone section=mahara}</a>{/if}
                </div>
                {/if}

                {if $enablemultipleblogstext}
                <p class="s dull center">{str tag=enablemultipleblogstext section=artefact.blog arg1=$WWWROOT}</p>
                {/if}
                {if $hiddenblogsnotification}
                <p class="s dull center">{str tag=hiddenblogsnotification section=artefact.blog arg1=$WWWROOT}</p>
                {/if}
            </div>
{include file="footer.tpl"}

