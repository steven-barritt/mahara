{if !$blog->get('locked') && $isowner}
                <a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=1"><img src="{theme_url filename='images/photo.png'}" width="30px"/></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=2"><img src="{theme_url filename='images/text.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=3"><img src="{theme_url filename='images/link.png'}" width="30px" /></a>
<a href="{$WWWROOT}artefact/blog/post.php?blog={$blog->get('id')}&type=0"><img src="{theme_url filename='images/html.png'}" width="30px" /></a>
{/if}
{if !$options.hidetitle}
<h2>{$artefacttitle|safe}</h2>
{/if}

<div id="blogdescription">{$description|clean_html|safe}
{if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
</div>
{if $blog->count_published_posts() > 0}
<div class="message">
	{$blog->count_published_posts()} {str tag=posts section=artefact.blog} |
	<a href="" class="loadall hidden">{str tag=loadall section=artefact.blog}</a> | 
	<a href="" class="order hidden">{str tag=reverseorder section=artefact.blog}</a>
</div>
{/if}
<div class="block_instance_blog">
	<div id="postlist{if $blockid}_{$blockid}{/if}" class="postlist{if $flowview}_flow{/if}">
		<div class="grid-sizer"></div>
	  {$posts.tablerows|safe}
	</div>
</div>
	<div class="message hidden" id="loading">{str tag=loading section=artefact.blog}</div>
	<div class="message hidden" id="loaded">{str tag=loaded section=artefact.blog arg1=$blog->count_published_posts()}</div>

{if $posts.pagination}
	<div id="blogpost_page_container{if $blockid}_{$blockid}{/if}" class="hidden center">{$posts.pagination|safe}</div>
{/if}
{if $license}
  <div class="bloglicense">
    {$license|safe}
  </div>
{/if}
{if $posts.pagination_js}
<script>

addLoadEvent(function() {literal}{{/literal}
    {$posts.pagination_js|safe}
    removeElementClass('blogpost_page_container{if $blockid}_{$blockid}{/if}', 'hidden');
{literal}}{/literal});
</script>
{/if}
