{if $LOGGEDIN && $dashboardview}
<div id="quicklinks" class="quicklinks">
<table class="fullwidth">
<tr>
<td>
<a href="{$WWWROOT}artefact/blog/post.php?blog=0&type=1"><img src="{theme_url filename='images/photo.png'}"/></a>
</td>
<td>
<a href="{$WWWROOT}artefact/blog/post.php?blog=0&type=2"><img src="{theme_url filename='images/text.png'}"/></a>
</td>
<td>
<a href="{$WWWROOT}artefact/blog/post.php?blog=0&type=3"><img src="{theme_url filename='images/link.png'}"/></a>
</td>
<td>
<a href="{$WWWROOT}artefact/blog/post.php?blog=0&type=0"><img src="{theme_url filename='images/html.png'}"/></a>
</td>
</tr>
</table>
</div>
{/if}

