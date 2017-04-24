{if $posts}
{foreach from=$posts item=post}
  <div class="newsfeed_flow">
          <li>
            <div class="friendcell">
                    <a href="{profile_url($post->user)}">
                       <img src="{profile_icon_url user=$post->user maxwidth=40 maxheight=40}" alt="">
                    </a>
                </div>

	  <div class="newsfeedpost  {if $post->sensitive}sensitive{/if}">
		<div class="shortpost  {if !$post->images && !$post->shortdesc}hidden{/if}">
		                    <div><a href="{profile_url($post->user)}">{$post->user|display_default_name|escape}</a></div>
                    Posted {str tag='postedin' section='blocktype.blog/recentposts'} - 
                    <a href="{$WWWROOT}artefact/artefact.php?artefact={$post->parent}&amp;view={$post->view}">{$post->parenttitle}</a>
                    
                    
                    <br />
		<div class="post_body">
			<h3><a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$post->view}" target="_blank">{$post->title}</a></h3>
			<div class="postdescription">
				<!-- just one image then just show that as is-->
					{if $post->imagecount != 0}
					<div class="thumb_container">
						{if $post->imagecount == 1}
							<div class="thumbrow_1">
								<div class="thumb"  style="background-image:url('{$post->images[0].src}')">
							<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>
						<!-- if there is two show them as two square thumbs -->
						{elseif $post->imagecount == 2}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>

						<!-- if there is three show them as one big image and two square thumbs -->
						{elseif $post->imagecount == 3}
							<div class="thumbrow_1">
								<div class="thumb"  style="background-image:url('{$post->images[0].src}')">
						<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$options.viewid}"></a>
								</div>
							</div>
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>
						<!-- if there is four show them as two square thumbs x2 -->
						{elseif $post->imagecount == 4}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[3].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>
						<!-- show first 2 as square thumbs then rows of three -->
						{else}
							<div class="thumbrow_2">
								<div class="thumb" style="background-image:url('{$post->images[0].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[1].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
							</div>
							<div class="thumbrow_3">
								<div class="thumb" style="background-image:url('{$post->images[2].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[3].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
								</div>
								<div class="thumb" style="background-image:url('{$post->images[4].src}')">
									<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}"></a>
									{if $post->imagecount > 5}
									<div class="countthumb">
																		<a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}">+{$post->imagecount - 5}</a>
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
		<div class="postdetails"><span class="description">{$post->displaydate}</span>
			{if isset($post->commentcount) } | <a href="{$post->artefacturl}">{str tag=Comments section=artefact.comment} ({$post->commentcount})</a>{/if}

		</div>
		</div>
		</div>
		<div class="longpost {if $post->images || $post->shortdesc}hidden{/if}">
			<h3><a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&view={$post->view}" target="_blank">{$post->title}</a></h3>
			<div class="postdescription">
			{$post->description|clean_html|safe}
			{if $post->tags}
			<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$post->owner tags=$post->tags}</p>
			{/if}
			</div>
		<div class="postdetails"><span class="description">{$post->displaydate}</span>
			{if isset($post->commentcount) } | <a href="{$post->artefacturl}">{str tag=Comments section=artefact.comment} ({$post->commentcount})</a>{/if}

		</div>
		</div>
		{if $post->imagecount > 1 || (count_characters($post->shortdesc,true) > 380)}
		<div class="expand btn">Expand</div>
		<div class="expand btn hidden">Shrink</div>
		{/if}

	</div>
  </div>
{/foreach}
{/if}