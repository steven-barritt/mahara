{include file="header.tpl"}
<div class="clearfix">
	<div class="content-wrapper ">
		<div class="column-one-third">
			<ul id="groupviews" class="fullwidth">
			{if $views}
				{foreach from=$views item=view}
					<li class=" {if $view.id == $currentviewid}selected{/if}">
							<h5><a href="{$WWWROOT}view/groupviews.php?group={$group->id}&currentview={$view.id}">{$view.name}</a></h5>
							{if $canedit}
						<div class="rbuttonssml">
						<a class="btn-big-edit btn" href="{$WWWROOT}view/blocks.php?id={$view.id}" title="{str(tag=editspecific arg1=$view.name)|escape:html|safe}">
							<span class="accessible-hidden">{str tag=editspecific arg1=$view.name}</span>
						</a>
						</div>
						{/if}
					</li>
				{/foreach}
				<div class="center">{$pagination|safe}</div>
			{else}
				<li><h5>{str tag="noviewstosee" section="group"}</h5></li>
			{/if}
			{foreach from=$collections item=collection}
				<li class=" listrow  {if $collection.id == $currentcollectionid}collectionselected{/if}">
					<h4><a class="collection" href="{$WWWROOT}view/groupviews.php?group={$group->id}&currentview={$collection.homepage}">{$collection.name}</a>
					</h4>
							{if $canedit}
						<div class="rbuttonssml">
						<a class="btn-big-edit btn" href="{$WWWROOT}collection/edit.php?id={$collection.id}&returnto=" title="{str(tag=editspecific arg1=$view.name)|escape:html|safe}">
							<span class="accessible-hidden">{str tag=editspecific arg1=$view.name}</span>
						</a>
						</div>
						{/if}
					<ul class="collectionviews hidden">
				 {foreach from=$collection['views'] item=view}

					<li class=" listrow  {if $view.id == $currentviewid}selected{/if}">
						<div class="page_title"><h5><a href="{$WWWROOT}view/groupviews.php?group={$group->id}&currentview={$view.id}">{$view.name}</a></h5>
							{if $canedit}
						<div class="rbuttonssml">
						<a class="btn-big-edit btn" href="{$WWWROOT}view/blocks.php?id={$view.id}" title="{str(tag=editspecific arg1=$view.name)|escape:html|safe}">
							<span class="accessible-hidden">{str tag=editspecific arg1=$view.name}</span>
						</a>
						</div>
						{/if}
						</div>

					</li>
					{/foreach}
					{if $canedit}
                {$collection.createviewform|safe}
					{/if}
					</ul>
				 {if $collection.tags}
					<div class="tags"><strong>{str tag=tags}:</strong> {list_tags owner=$collection.owner tags=$collection.tags}</div>
				 {/if}
				</li>
			{/foreach}
			</ul>
			{if $canedit}
				<a class="btn" href="{$WWWROOT}collection/edit.php?new=1&group={$group->id}">{str section=collection tag=newcollection}
					<span class="accessible-hidden"></span>
				</a>
            <div class="">
                {$createviewform|safe}
                <form method="post" action="{$WWWROOT}view/choosetemplate.php">
                    <input type="submit" class="submit" value="{str tag="copyaview" section="view"}">
{if $GROUP}
                    <input type="hidden" name="group" value="{$GROUP->id}" />
{elseif $institution}
                    <input type="hidden" name="institution" value="{$institution}">
{/if}
                </form>
            </div>
			<div class="templateviews"><h2>{str tag=templatepages section=view}</h2></div>
			<ul id="groupviews" class="fullwidth">
			{if $templates}
				{foreach from=$templates item=view}
					<li class=" {if $view.id == $currentviewid}selected{/if}">
							<h5><a href="{$WWWROOT}view/groupviews.php?group={$group->id}&currentview={$view.id}">{$view.name}</a></h5>
							{if $canedit}
						<div class="rbuttonssml">
						<a class="btn-big-edit btn" href="{$WWWROOT}view/blocks.php?id={$view.id}" title="{str(tag=editspecific arg1=$view.name)|escape:html|safe}">
							<span class="accessible-hidden">{str tag=editspecific arg1=$view.name}</span>
						</a>
						</div>
						{/if}
					</li>
				{/foreach}
				<div class="center">{$pagination|safe}</div>
			{else}
				<li><h5>{str tag="noviewstosee" section="group"}</h5></li>
			{/if}
	
			{/if}
			
		</div>
		<div class="column-two-thirds">
			{$viewcontent|safe}
		</div>
	</div>
</div>
{include file="footer.tpl"}
