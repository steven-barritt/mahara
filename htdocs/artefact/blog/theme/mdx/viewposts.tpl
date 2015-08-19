{foreach from=$posts item=post}
  <div class="viewpost">
	  <div class="post_inner">
		<div class="shortpost  {if !$post->images && !$post->shortdesc}hidden{/if}">
			<h3><a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$options.viewid}" target="_blank">{$post->title}</a></h3>
			<div class="postdescription">
				<!-- just one image then just show that as is-->
					{if $post->imagecount != 0}
					<div class="thumb_container">
						{if $post->imagecount == 1}
							<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"><img class="{$post->images[0].class}" src="{$post->images[0].src}" alt="{$post->images[0].alt}"/></a>
						<!-- if there is two show them as two square thumbs -->
						{elseif $post->imagecount == 2}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>

						<!-- if there is three show them as one big image and two square thumbs -->
						{elseif $post->imagecount == 3}
							<div class="thumbrow_1">
								<div class="thumb">
						<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"><img class="{$post->images[0].class}" src="{$post->images[0].src}" alt="{$post->images[0].alt}"/></a>
								</div>
							</div>
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>
						<!-- if there is four show them as two square thumbs x2 -->
						{elseif $post->imagecount == 4}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[3].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>
						<!-- show first 2 as square thumbs then rows of three -->
						{else}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>
							<div class="thumbrow_3">
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[3].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[4].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
									{if $post->imagecount > 5}
									<div class="countthumb">
																		<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}">+{$post->imagecount - 5}</a>
									</div>
									{/if}
								</div>
							</div>
		
						{/if}
						</div>
					{/if}
					{$post->shortdesc|clean_html|safe}

			{if $post->tags}
			<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$post->owner tags=$post->tags}</p>
			{/if}
			</div>

			<div class="postdetails">{$post->postedby}
				{if $options.viewid && $post->allowcomments} | <a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$options.viewid}" target="_blank">{str tag=Comments section=artefact.comment} ({$post->commentcount})</a>{/if}
			</div>
		</div>
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
		{if $post->imagecount > 1 || (count_characters($post->shortdesc,true) > 380)}
		<div class="expand btn">Expand</div>
		<div class="expand btn hidden">Shrink</div>
		{/if}
	</div>
  </div>
{/foreach}