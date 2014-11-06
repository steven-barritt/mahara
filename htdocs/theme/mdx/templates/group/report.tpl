{include file="header.tpl"}

{if !$sharedviews && !$groupviews}
<p>{str tag=youhaventcreatedanyviewsyet section=view}</p>
{else}

<table class="fullwidth groupreport" id="sharedviewsreport">
  <thead>
    <tr>
      <th class="sv {if $sort == title && $direction == asc}asc{elseif $sort == title}sorted{/if}">
        <a href="{$baseurl}&sort=title{if $sort == title && $direction == asc}&direction=desc{/if}">{str tag=viewssharedtogroup section=view}</a>
      </th>
      <th class="sb {if $sort == sharedby && $direction == asc}asc{elseif $sort == sharedby}sorted{/if}">
        <a href="{$baseurl}&sort=sharedby{if $sort == sharedby && $direction == asc}&direction=desc{/if}">{str tag=sharedby section=view}</a>
      </th>
      <th class="sd {if $sort == submittedtime && $direction == asc}asc{elseif $sort == submittedtime}sorted{/if}">
        <a href="{$baseurl}&sort=submittedtime{if $sort == submittedtime && $direction == asc}&direction=desc{/if}">{str tag=submitteddate section=group}</a>
      </th>
      <th class="pc {if $sort == postcount && $direction == asc}asc{elseif $sort == postcount}sorted{/if}">
        <a href="{$baseurl}&sort=postcount{if $sort == postcount && $direction == asc}&direction=desc{/if}">{str tag=postcount section=group}</a>
      </th>
      <th class="sg {if $sort == selfgrade && $direction == asc}asc{elseif $sort == selfgrade}sorted{/if}">
        <a href="{$baseurl}&sort=selfgrade{if $sort == selfgrade && $direction == asc}&direction=desc{/if}">{str tag=selfgrade section=group}</a>
      </th>
      <th class="pg {if $sort == peergrade && $direction == asc}asc{elseif $sort == peergrade}sorted{/if}">
        <a href="{$baseurl}&sort=peergrade{if $sort == peergrade && $direction == asc}&direction=desc{/if}">{str tag=peergrade section=group}</a>
      </th>
      <th class="tg {if $sort == tutorgrade && $direction == asc}asc{elseif $sort == tutorgrade}sorted{/if}">
        <a href="{$baseurl}&sort=tutorgrade{if $sort == tutorgrade && $direction == asc}&direction=desc{/if}">{str tag=tutorgrade section=group}</a>
      </th>
    </tr>
  </thead>
  <tbody>
{if $sharedviews}
{foreach from=$sharedviews item=view}
    <tr class="{cycle values='r0,r1'}">
      <td class="sv"><h3 class="title"><a href="{$view.baseurl}">{$view.title}</a></h3></td>
      <td class="sb"><label class="hidden">{str tag=sharedby section=view}: </label>
{if $view.owner}
        <a href="{$WWWROOT}user/view.php?id={$view.owner}">{$view.user->id|display_name:null:true|escape}</a>
{elseif $view.group}
        <a href="{$WWWROOT}group/view.php?id={$view.group}">{$view.groupname|escape}</a>
{elseif $view.institution}
        <a href="{$WWWROOT}institution/view.php?id={$view.institution}">{$view.institution|escape}</a>
{/if}
      </td>
      <td class="sd"><label class="hidden">{str tag=submitteddate section=group}: </label>
        <ul>
        	{$view.submittedtime|format_date}
        </ul>
      </td>
      <td class="pc"><label class="hidden">{str tag=postcount section=group}: </label>
        <ul>
             {if $view.artefacts}
     	<div class="bloginfo"> {str tag=posts section=blocktype.groupviews arg1=$view.postcount}</div>
     {/if}

        </ul>
      </td>
      <td class="sg"><label class="hidden">{str tag=selfgrade section=group}: </label>
        <ul>
        	{$view.selfgrade}
        </ul>
      </td>
      <td class="sg"><label class="hidden">{str tag=peergrade section=group}: </label>
        <ul>
        	{$view.peergrade}
        </ul>
      </td>
      <td class="sg {if $view.publishedgrade}published{else}unpublished{/if}"><label class="hidden">{str tag=tutorgrade section=group}: </label>
        <ul>
        	{$view.tutorgrade}
        </ul>
      </td>
    </tr>
{/foreach}
{elseif $svcount > 0}
    <tr class="{cycle values='r0,r1'}"><td colspan="4" class="message">{str tag=groupsharedviewsscrolled section=group}</td></tr>
{else}
    <tr class="{cycle values='r0,r1'}"><td colspan="4" class="message">{str tag=noviewssharedwithgroupyet section=group}</td></tr>
{/if}
  </tbody>
</table>

<table class="fullwidth groupreport" id="groupviewsreport">
  <thead>
    <tr>
      <th class="sv {if $sort == title && $direction == asc}asc{elseif $sort == title}sorted{/if}">
        <a href="{$baseurl}&sort=title{if $sort == title && $direction == asc}&direction=desc{/if}">{str tag=viewsownedbygroup section=view}</a>
      </th>
      <th class="mc {if $sort == mcomments && $direction == asc}asc{elseif $sort == mcomments}sorted{/if}">
        <a href="{$baseurl}&sort=mcomments{if $sort == mcomments && $direction == asc}&direction=desc{/if}">{str tag=membercommenters section=group}</a>
      </th>
      <th class="ec {if $sort == ecomments && $direction == asc}asc{elseif $sort == ecomments}sorted{/if}">
        <a href="{$baseurl}&sort=ecomment{if $sort == ecomments && $direction == asc}&direction=desc{/if}">{str tag=extcommenters section=group}</a>
      </th>
    </tr>
  </thead>
  <tbody>
{if $groupviews}
{foreach from=$groupviews item=view}
    <tr class="{cycle values='r0,r1'}">
      <td class="sv"><h3 class="title"><a href="{$view.fullurl}">{$view.title}</a></h3></td>
      <td class="mc"><label class="hidden">{str tag=membercommenters section=group}: </label>
        <ul>
{foreach from=$view.comments key=commenter item=info}
        {if $info.member}<li><a href="{$WWWROOT}user/view.php?id={$info.commenter}">{$info.commenter|display_name:null:true|escape}</a><span> ({$info.count})</span></li>{/if}
{/foreach}
        </ul>
{if $view.mcomments > 0}<div class="detail">{$view.mcomments} {str tag=comments section=artefact.comment}</div>{/if}
      </td>
      <td class="ec"><label class="hidden">{str tag=extcommenters section=group}: </label>
        <ul>
{foreach from=$view.comments key=commenter item=info}
        {if $info.commenter|is_string}
          <li>{$info.commenter}<span> ({$info.count})</span></li>
        {elseif ! $info.member}
          <li><a href="{$WWWROOT}user/view.php?id={$info.commenter}">{$info.commenter|display_name:null:true|escape}</a><span> ({$info.count})</span></li>
        {/if}
{/foreach}
        </ul>
{if $view.ecomments > 0}<div class="detail">{$view.ecomments} {str tag=comments section=artefact.comment}</div>{/if}
      </td>
    </tr>
{/foreach}
{elseif $gvcount > 0}
    <tr class="{cycle values='r0,r1'}"><td colspan="3" class="message">{str tag=groupcreatedviewsscrolled section=group}</td></tr>
{else}
    <tr class="{cycle values='r0,r1'}"><td colspan="3" class="message">{str tag=grouphasntcreatedanyviewsyet section=group}</td></tr>
{/if}
  </tbody>
</table>
{$pagination|safe}

{/if}
{include file="footer.tpl"}
