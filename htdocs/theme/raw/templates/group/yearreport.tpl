{include file="header.tpl"}

<table class="fullwidth groupreport" id="assessmentreport">
  <thead>
	{foreach from=$rows item=row name=rrow}
    	<tr>
    	{if $dwoo.foreach.rrow.last}
    		<th></th>
			<th class="reportUser"><a href="{$baseurl}&sort=firstname{if $sort == firstname && $direction == asc}&direction=desc{/if}">Firstname</a></th>
			<th class="reportUser"><a href="{$baseurl}&sort=lastname{if $sort == lastname && $direction == asc}&direction=desc{/if}">Lastname</a></th>
			<th class="reportUser"><a href="{$baseurl}&sort=syudentnumber{if $sort == studentnumber && $direction == asc}&direction=desc{/if}">Studentnumber</a></th>
		{else}
    		<th class="reportUser" colspan="4"></th>
    	{/if}
    	
    	{foreach from=$row item=col name=ccol}
      	
      	<th class="{cycle values='r0,r1'} {if $dwoo.foreach.rrow.last}reportLastRow{/if}" colspan="{$col.colspan}">
      		{if $dwoo.foreach.rrow.last}
      		<div class="verticalText"><span>
      		{/if}
      		<a href="{$baseurl}&sort={$dwoo.foreach.ccol.index+1}{if $sort == $dwoo.foreach.ccol.index+1 && $direction == asc}&direction=desc{/if}">{$col.name}</a>
      		
      		{if $dwoo.foreach.rrow.last}
      		</span></div>
      		{/if}
      	</th>
		{/foreach}
	{/foreach}
	<tr>
	</tr>
  </thead>
  <tbody>
  {if $userdata}
    	{foreach from=$userdata item=user}
	    <tr class="{cycle values='r0,r1'}">
	    	<td>
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
	    	{for i 1 $colcount}
	    		<td>{if $user[$i] == 0}-{else}{$user[$i]}{/if}</td>
	    	{/for}
	    </tr>    	
    	{/foreach}
  {else}
    <tr class="{cycle values='r0,r1'}"><td colspan="{$totalcolcount}" class="message">{str tag=noviewssharedwithgroupyet section=group}</td></tr>
  {/if}
  </tbody>
</table>

{$pagination|safe}


{include file="footer.tpl"}
