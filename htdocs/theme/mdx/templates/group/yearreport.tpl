{include file="header.tpl"}
<div>
<a href="#" class="export">Export Table data into Excel</a>
</div>
<table class="fullwidth groupreport" id="assessmentreport">
  <thead>
  	{if $modulegroups}
  		<tr>
    		<th class="reportUser" colspan="8"></th>
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
			<th class="reportLastRow" style="height:{$columnheight}"><div class="verticalText"><span>Present</span>
				</div></th>
			<th class="reportLastRow" style="height:{$columnheight}"><div class="verticalText"><span>Late</span>
				</div></th>
			<th class="reportLastRow" style="height:{$columnheight}"><div class="verticalText"><span>Absent</span>
				</div></th>
			<th class="reportLastRow" style="height:{$columnheight}"><div class="verticalText"><span>Excused</span>
				</div></th>
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
	    	<td class="{if ($user.percentages[1]->percentage > 25) && ($user.percentages.total > 0)}late{/if} {if (($user.percentages[0]->percentage + $user.percentages[1]->percentage) <= 70) && ($user.percentages.total > 0)}absent{/if} ">
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" style="max-width:40px;max-height:40px;" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
	    	<td class="percent">{$user.percentages[0]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[1]->percentage > 25) && ($user.percentages.total > 0)}late{/if}">{$user.percentages[1]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[2]->percentage > 30) && ($user.percentages.total > 0)}absent{/if}">{$user.percentages[2]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[3]->percentage > 25) && ($user.percentages.total > 0)}excused{/if}">{$user.percentages[3]->percentage}%</td>
	    	{for i 1 $colcount}
	    		<td class="gradecolumn {if $user[$i][2]}absent{/if}">{if $user[$i][2]}20{elseif $user[$i][1] == 0}-{else}{$user[$i][1]}{/if}</td>
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
