{if $microheaders}
  {include file="viewmicroheader.tpl"}
{else}
  {include file="header.tpl"}
  <h1>{$viewtitle}</h1>
{/if}

{include file="view/editviewtabs.tpl" selected='content' new=$new}
<div class="subpage rel cl">

{if $columns}
<!--SB-->
	{if !limitedediting}
    {str tag="editblockspagedescription" section="view"}
    {/if}
    <form action="{$formurl}" method="post">
        <input type="submit" name="{$action_name}" id="action-dummy" class="hidden">
        <input type="hidden" id="viewid" name="id" value="{$view}">
        <input type="hidden" name="change" value="1">
        <input type="hidden" id="category" name="c" value="{$category}">
        <input type="hidden" name="sesskey" value="{$SESSKEY}">
        {if $new}<input type="hidden" name="new" value="1">{/if}
        <div id="page">
            <div id="top-pane">
<!--SB-->
	        	{if !$limitedediting}
                <div id="category-list">
                    {$category_list|safe}
                </div>
                <div id="blocktype-list">
                    {$blocktype_list|safe}
                </div>
                {/if}
            </div>
            <div id="middle-pane">
{if $viewthemes}
              <div id="themeselect">
                  <label for="viewtheme-select">{str tag=theme}: </label>
                  <select id="viewtheme-select" name="viewtheme">
{foreach from=$viewthemes key=themeid item=themename}
                      <option value="{$themeid}"{if $themeid == $viewtheme} selected="selected" style="font-weight: bold;"{/if}>{$themename}</option>
{/foreach}
                  </select>
              </div>
{/if}
              <div class="nojs-hidden-block" id="current_bt_description"></div>
              <div class="cb"></div>
            </div>

            <div id="bottom-pane">
                <div id="column-container">
                
                	<div id="blocksinstruction" class="center">
<!--SB-->
                    {if !limitedediting}
                	    {str tag='blocksintructionnoajax' section='view'}
                    {/if}
                	</div>
                        {$columns|safe}
                    <div class="cb"></div>
                </div>
            </div>
            <script type="text/javascript">
            {literal}
            insertSiblingNodesAfter('bottom-pane', DIV({'id': 'views-loading'}, IMG({'src': config.theme['images/loading.gif'], 'alt': ''}), ' ', get_string('loading')));
            {/literal}
            </script>
        </div>
    </form>

    <div id="view-wizard-controls" class="center">
        <form action="{$WWWROOT}{if $groupid}{if $viewtype == 'grouphomepage'}{$groupurl}{else}view/groupviews.php{/if}{elseif $institution}view/institutionviews.php{else}view/index.php{/if}" method="GET">
        {if $groupid}
            {if $viewtype == 'grouphomepage'}
            <input type="hidden" name="id" value="{$groupid}">
            {else}
            <input type="hidden" name="group" value="{$groupid}">
            {/if}
        {elseif $institution}
            <input type="hidden" name="institution" value="{$institution}">
        {/if}
            <input class="submit" type="submit" value="{str tag='done'}">
        </form>
    </div>

{elseif $block}
    <div class="blockconfig-background">
        <div class="blockconfig-container">
            {$block.html|safe}
        </div>
    </div>
    {if $block.javascript}<script type="text/javascript">{$block.javascript|safe}</script>{/if}
{/if}
</div>
  <div class="viewfooter">
    {if $tags}<div class="tags"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</div>{/if}
    {if $releaseform}<div class="releaseviewform">{$releaseform|safe}</div>{/if}
    {if $view_group_submission_form}<div class="submissionform">{$view_group_submission_form|safe}</div>{/if}
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
        {include file="view/viewmenu.tpl" enablecomments=$enablecomments}
    </div>
    {if $addfeedbackform}<div>{$addfeedbackform|safe}</div>{/if}
    {if $objectionform}<div>{$objectionform|safe}</div>{/if}
  </div>

{if $microheaders}{include file="microfooter.tpl"}{else}{include file="footer.tpl"}{/if}
