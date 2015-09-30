	<table id="yearplanlist" class="fullwidth nohead">
		<thead>
			<tr>
				<td></td>
				<td>{str tag=monday section=interaction.schedule}</td>
				<td>{str tag=tuesday section=interaction.schedule}</td>
				<td>{str tag=wednesday section=interaction.schedule}</td>
				<td>{str tag=thursday section=interaction.schedule}</td>
				<td>{str tag=friday section=interaction.schedule}</td>
				<td class="noprint weekend">{str tag=saturday section=interaction.schedule}</td>
				<td class="noprint weekend">{str tag=sunday section=interaction.schedule}</td>
			</tr>
		</thead>
		{for i 1 52}
			<tr>
				<td><span class="weekno">Week: {$i}<br>{$weeksanddays[$i][1].date|format_date:'strftimedatevshort'}</span></td>
				{for j 1 5}
				<td class="weekday">
				<div  class="yearplanday{if $admin} editable{/if}">
					{if $admin}
					<a href="{$WWWROOT}interaction/schedule/editevent.php?schedule={$schedule->id}&view={$view}&returnto={$groupid}&day={$weeksanddays[$i][$j].date|format_date:'strftimenday'}&month={$weeksanddays[$i][$j].date|format_date:'strftimenmonth'}&year={$weeksanddays[$i][$j].date|format_date:'strftimenyear'}"></a>
					{/if}
				<div class="daynumber">{$weeksanddays[$i][$j].date|format_date:'strftimedatevshort'}</div>
				<ul>
					{foreach $weeksanddays[$i][$j].events as event}
					<li><a style="color:{$event->color}" href="{$WWWROOT}interaction/schedule/{if $admin}editevent.php?id={else}view.php?event={/if}{$event->id}&view={$view}&returnto={$groupid}" title="{$event->startdate|format_date:'strftimetime'}&#13;{$event->scheduletitle}&#13;{str tag=where section=interaction.schedule}{$event->location}">{$event->title}</a></li>	
					{/foreach}
				</ul>
				</div>
				</td>
				{/for}
				<td class="weekend">
				<div  class="yearplanday">
				<div class="daynumber">{$weeksanddays[$i][6].date|format_date:'strftimedatevshort'}</div>
				<ul>
					{foreach $weeksanddays[$i][6].events as event}
					<li style="color:{$event->color}">{$event->title}</li>	
					{/foreach}
				</ul>
				</div>
				</td>
				<td class="weekend">
				<div  class="yearplanday">
				<div class="daynumber">{$weeksanddays[$i][7].date|format_date:'strftimedatevshort'}</div>
				<ul>
					{foreach $weeksanddays[$i][7].events as event}
					<li style="color:{$event->color}">{$event->title}</li>	
					{/foreach}
				</ul>
				</div>
				</td>
				
			</tr>
		{/for}
	</table>