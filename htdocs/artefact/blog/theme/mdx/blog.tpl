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
<div class="block_instance_blog">
<table id="postlist{if $blockid}_{$blockid}{/if}" class="postlist">
  <tbody>
  {$posts.tablerows|safe}
  </tbody>
</table>
</div>
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
