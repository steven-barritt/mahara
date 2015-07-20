{include file="header.tpl"}
			{if !$limitedediting}
            <div class="rbuttons">
                <a class="btn" href="{$WWWROOT}artefact/blog/new/index.php">{str section="artefact.blog" tag="addblog"}</a>
            </div>
            {/if}
            <div class="pagecontent">
		<div id="myblogs rel">
{if !$blogs->data}
           <div>{str tag=youhavenoblogs section=artefact.blog}</div>
{else}
		
           <table id="bloglist" class="tablerenderer fullwidth">
             <tbody>
              {$blogs->tablerows|safe}
             </tbody>
           </table>
           {$blogs->pagination|safe}
{/if}
                </div>
                </div>
{include file="footer.tpl"}
