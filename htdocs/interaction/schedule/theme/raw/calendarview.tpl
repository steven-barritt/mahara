	{assign '' currmonth}
	<table id="calendarlist" class="fullwidth nohead">
		<thead>
			<tr>
			<td colspan="7">
			<div class="btns2 cal_forward_back">
					<a href="{$WWWROOT}interaction/schedule/{if $groupid}index.php?group={$groupid}&amp;{else}schedule.php?{/if}view=2&month={$month-1}&year={$year}" class="btn" title="{str tag=prevmonth section=interaction.schedule}">
					<</a>
					<a href="{$WWWROOT}interaction/schedule/{if $groupid}index.php?group={$groupid}&amp;{else}schedule.php?{/if}view=2&month={$month+1}&year={$year}" class="btn" title="{str tag=prevmonth section=interaction.schedule}">
					></a>
				</div>
				<h2 class="month_title">{$weeksanddays[2][4].date|format_date:'strftimemonthyear'}</h2>			
			</td>
			</tr>
			<tr>
				<td>{str tag=monday section=interaction.schedule}</td>
				<td>{str tag=tuesday section=interaction.schedule}</td>
				<td>{str tag=wednesday section=interaction.schedule}</td>
				<td>{str tag=thursday section=interaction.schedule}</td>
				<td>{str tag=friday section=interaction.schedule}</td>
				<td class="noprint">{str tag=saturday section=interaction.schedule}</td>
				<td class="noprint">{str tag=sunday section=interaction.schedule}</td>
			</tr>
		</thead>
		{for i 1 6}
			{if $weeksanddays[$i][1].date|format_date:'strftimenmonth' == $weeksanddays[2][4].date|format_date:'strftimenmonth' || $i == 1}
			<tr>
				{for j 1 7}
				<td class="{if $j >5 }weekend{else}weekday{/if}{if $weeksanddays[$i][$j].date|format_date:'strftimenmonth' != $weeksanddays[2][4].date|format_date:'strftimenmonth'} greyed{/if}">
				<div  class="yearplanday">
				<div class="daynumber">{if $weeksanddays[$i][$j].date|format_date:'strftimenmonth' != $currmonth}{$weeksanddays[$i][$j].date|format_date:'strftimedatevshort'}{else}{$weeksanddays[$i][$j].date|format_date:'strftimenday'}{/if}</div>
				<ul>
					{foreach $weeksanddays[$i][$j].events as event}
					<li><a style="color:{$event->color}" href="{$WWWROOT}interaction/schedule/{if $admin}editevent{else}view{/if}.php?id={$event->id}&view={$view}" title="{$event->startdate|format_date:'strftimetime'}&#13;{$event->scheduletitle}&#13;{str tag=where section=interaction.schedule}{$event->location}&#13;{$event->startdate|format_date:'strftimedatevshort'}">{$event->title}</a></li>	
					{/foreach}
				</ul>
				</div>
				</td>
					{assign $weeksanddays[$i][$j].date|format_date:'strftimenmonth' currmonth}
				{/for}
				
			</tr>
			{/if}
		{/for}
	</table>