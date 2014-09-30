{if $posts}
<div id="postlist">
    <ul class="recentblogpost">
    {foreach from=$posts item=post}
        <li>

            <div class="friendcell">
                    <a href="{profile_url($post->user)}">
                       <img src="{profile_icon_url user=$post->user maxwidth=40 maxheight=40}" alt="">
                    </a>
                    <div><a href="{profile_url($post->user)}">{$post->user|display_default_name|escape}</a></div>
                </div>
            	<div class="newsfeedpost">
                    Posted {str tag='postedin' section='blocktype.blog/recentposts'} - 
                    <a href="{$WWWROOT}artefact/artefact.php?artefact={$post->parent}&amp;view={$post->view}">{$post->parenttitle}</a>
                    
                    
                    <br />
                    <h2><a href="{$WWWROOT}artefact/artefact.php?artefact={$post->id}&amp;view={$post->view}">{$post->title}</a></h2>
                    <br />
                    <br style="clear:both"/>
                    <div class="postdescription">{clean_html($post->description)|safe}</div>    

					<div class="postdetails"><span class="description">{$post->displaydate}</span>
						{if isset($post->commentcount) } | <a href="{$post->artefacturl}">{str tag=Comments section=artefact.comment} ({$post->commentcount})</a>{/if}

  					</div>
<!--                    <div class=
                        {if $feedback->count || $enablecomments}
                            <table id="feedbacktable" class="fullwidth table">
                              <thead><tr><th>{str tag="feedback" section="artefact.comment"}</th></tr></thead>
                              <tbody>
                                {$feedback->tablerows|safe}
                              </tbody>
                            </table>
                            {$feedback->pagination|safe}
                        {/if}
                        <div id="viewmenu">
                            {include file="view/viewmenu.tpl"}
                        </div>
                        <div>{$addfeedbackform|safe}</div>
                        <div>{$objectionform|safe}</div>
-->
               </div>
    	</li>
    <br style="clear:both" />
    <hr />
    
    {/foreach}
    </ul>
</div>
{else}
  {str tag=noviews section=view}
{/if}
