{foreach from=$posts item=post}
  <div class="viewpost">
	  <div class="post_inner ">
		<div class="longpost {if $post->images || $post->shortdesc}hidden{/if}">
			<h3><a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$options.viewid}" target="_blank">{$post->title}</a></h3>
			<div class="postdescription">
			{$post->description|clean_html|safe}
			{if $post->tags}
			<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$post->owner tags=$post->tags}</p>
			{/if}
			</div>
			{if $post->files}
			<table class="cb attachments fullwidth hidden">
			  <tbody>
				<tr><th>{str tag=attachedfiles section=artefact.blog}:</th></tr>
				{foreach from=$post->files item=file}
				<tr class="{cycle values='r0,r1'}">
				  <td>
					<a href="{$WWWROOT}artefact/artefact.php?artefact={$file->attachment}&view={$options.viewid}">{$file->title}</a>
					({$file->size|display_size})
					- <strong><a href="{$WWWROOT}artefact/file/download.php?file={$file->attachment}&view={$options.viewid}">{str tag=Download section=artefact.file}</a></strong>
				  </td>
				</tr>
				{/foreach}
			  </tbody>
			</table>
			{/if}
			<div class="postdetails">{$post->postedby}
				{if $options.viewid && $post->allowcomments} | <a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$options.viewid}" target="_blank">{str tag=Comments section=artefact.comment} ({$post->commentcount})</a>{/if}
			</div>
		</div>
	</div>
  </div>
{/foreach}