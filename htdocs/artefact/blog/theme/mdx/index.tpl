{include file="header.tpl"}
    <div class="pagecontent">
		<div class="blogheader">
				{if !$limitedediting}
				<div class="rbuttons">
					<a class="btn" href="{$WWWROOT}artefact/blog/new/index.php">{str section="artefact.blog" tag="addblog"}</a>
				</div>
				{/if}
			<h2>{str section="artefact.blog" tag="myblogs"}</h2>
		</div>
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
{if $blogs->groupdata}
		<div class="blogheader">
				{if !$limitedediting}
				<div class="rbuttons">
					<a class="btn" href="{$WWWROOT}artefact/blog/new/index.php?group=1">{str section="artefact.blog" tag="addblog"}</a>
				</div>
				{/if}
			<h2>{str section="artefact.blog" tag="groupblogs"}</h2>
		</div>

		<div id="myblogs rel">
           <table id="bloglist" class="tablerenderer fullwidth">
             <tbody>
              {$blogs->grouptablerows|safe}
             </tbody>
           </table>
        </div>
{/if}
    </div>
{include file="footer.tpl"}
