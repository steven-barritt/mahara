{include file="header.tpl"}

<table class="fullwidth groupreport" id="assessmentreport">
  <thead>
  	{if $modulegroups}
  		<tr>
    		<th class="reportUser" colspan="5"></th>
    		{foreach from=$modulegroups item=module}
    			<th class="{cycle values='r0,r1'}" colspan="{$module->colspan}">
    				{$module->name}
				</th>
    		{/foreach}
  		</tr>
  	{/if}
    	<tr>
    		<th></th>
			<th class="reportUser"><a href="{$baseurl}&sort=firstname{if $sort == firstname && $direction == asc}&direction=desc{/if}">Firstname</a></th>
			<th class="reportUser"><a href="{$baseurl}&sort=lastname{if $sort == lastname && $direction == asc}&direction=desc{/if}">Lastname</a></th>
			<th class="reportUser"><a href="{$baseurl}&sort=studentnumber{if $sort == studentnumber && $direction == asc}&direction=desc{/if}">Studentnumber</a></th>
			<th class="reportUser"><a href="{$baseurl}&sort=attendance{if $sort == attendance && $direction == asc}&direction=desc{/if}">{str tag=attendance section=group}</a></th>
			{foreach from=$assessmentgroups item=assessment}
				<th class="{cycle values='r0,r1'} reportLastRow">
					<div class="verticalText"><span>
					<a href="{$baseurl}&sort={$dwoo.foreach.index+1}{if $sort == $dwoo.foreach.index+1 && $direction == asc}&direction=desc{/if}">{$assessment->name}</a>
					</span></div>
				</th>
			{/foreach}
		</tr>
	</tr>
  </thead>
  <tbody>
  {if $userdata}
    	{foreach from=$userdata item=user}
	    <tr class="{cycle values='r0,r1'}">
	    	<td {if (($user.percentages[0]->percentage + $user.percentages[1]->percentage) <= 70) && ($user.percentages.total > 0)}class="alert"{/if}>
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
      <td {if (($user.percentages[0]->percentage + $user.percentages[1]->percentage) <= 70) && ($user.percentages.total > 0)}class="alert"{/if}><label class="hidden">{str tag=attendance section=group}: </label>
        	<a href="{$WWWROOT}interaction/schedule/viewattendance.php?group={$group->id}" title="{str tag=present section=interaction.schedule}: {$user.percentages[0]->percentage}%&#13;{str tag=late section=interaction.schedule}: {$user.percentages[1]->percentage}%&#13;{str tag=absent section=interaction.schedule}: {$user.percentages[2]->percentage}%&#13;{str tag=excused section=interaction.schedule}: {$user.percentages[3]->percentage}%">{$user.percentages[0]->percentage + $user.percentages[1]->percentage}</a>
      </td>
	    	{for i 1 $colcount}
	    		<td class="gradecolumn"><label class="hidden">{$assessmentgroups[$i-1]->name}: </label>{if $user[$i] == 0}-{else}{$user[$i]}{/if}</td>
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
