{include file="header.tpl"}
{if $GROUP}<h2>{str tag=groupviews section=view}</h2>
{/if}
{if !$limitedediting}
            <div class="rbuttons{if $GROUP} pagetabs{/if}">
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
{/if}
{if $institution}                {$institutionselector|safe}{/if}
            <div class="grouppageswrap">
{$searchform|safe}

{if $query}
                <h2 id="searchresultsheading" class="accessible-hidden">{str tag=Results}</h2>
{/if}

{if $views}
                <div id="myviews" class="listing pagecontent">
{foreach from=$views item=view}
                    <div class="listrow {cycle values='r0,r1'}">
                        <h3 class="title">
                        {if $view.issitetemplate}
                            {$view.displaytitle}
                        {else}
							{if !$view.submittedto && (!$view.locked || $editlocked) && ($view.type != 'profile' && $view.type != 'dashboard')}								
    						<a href="{$WWWROOT}view/view.php?id={$view.id}">{$view.displaytitle}</a>
                            {else}
                            <a href="{$view.fullurl}">{$view.displaytitle}</a>
                            {/if}
                        {/if}
                        </h3>
                        <div class="fr btns2">
{if !$view.submittedto && (!$view.locked || $editlocked) && !$limitedediting}
                            <a class="btn-big-edit" href="{$WWWROOT}view/blocks.php?id={$view.id}&{$querystring}" title="{str tag ="editcontentandlayout" section="view"}"></a>
{/if}
{if !$view.submittedto && $view.removable && (!$view.locked || $editlocked ) && !$limitedediting}
                            <a class="btn-big-del" href="{$WWWROOT}view/delete.php?id={$view.id}&{$querystring}" title="{str tag=deletethisview section=view}"></a>
{/if}
                        {if !$view.removable && !$limitedediting}
						<a class="btn-big-copy" href="{$WWWROOT}view/reset.php?id={$view.id}&{$querystring}" title="{str tag ="resetlayout" section="view"}"></a>
                        {/if}

                        </div>{* rbuttons *}
{if $view.submittedto}
                        <div class="detail submitted-viewitem">{$view.submittedto|clean_html|safe}</div>
{elseif $view.type == 'profile'}
                        <div class="detail">{str tag=profiledescription}</div>
{elseif $view.type == 'dashboard'}
                        <div class="detail">{str tag=dashboarddescription}</div>
{elseif $view.type == 'grouphomepage'}
                        <div class="detail">{str tag=grouphomepagedescription section=view}</div>
{elseif $view.description}
                        <div class="detail">{$view.description|str_shorten_html:110:true|strip_tags|safe}</div>
{/if}
                        <div class="cb"></div>
                    </div>
{/foreach}
                </div>
{$pagination|safe}
            </div>
{else}
            <div class="message">{if $GROUP}{str tag="noviewstosee" section="group"}{elseif $institution}{str tag="noviews" section="view"}{else}{str tag="youhavenoviews" section="view"}{/if}</div>
            </div>
{/if}
{include file="footer.tpl"}
