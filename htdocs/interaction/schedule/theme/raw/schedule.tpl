{include file="header.tpl"}
<div id="scheduleoptions">
<a href="{$WWWROOT}interaction/schedule/schedule.php?view=0" class="btn newschedule {if $view == 0}selected{/if}">{str tag="scheduleview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/schedule.php?view=1" class="btn newschedule {if $view == 1}selected{/if}">{str tag="yearplannerview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/schedule.php?view=2" class="btn newschedule {if $view == 2}selected{/if}">{str tag="calendarview" section=interaction.schedule}</a>
</div>
		<h2>{$heading}</h2>
	<div id="viewschedule">
	{if $view == 0}
<div class="">{str tag=showingfrom section=interaction.schedule}{$mindate|format_date:'strftimedayvshortyear'}  <a href="{$WWWROOT}interaction/schedule/schedule.php?limit=31&offset={$offset-7}">{str tag=showearlier section=interaction.schedule}</a></div>
	{/if}
	{$table|safe}
	{if $view == 0}
	<div class="">{str tag=showingto section=interaction.schedule}{$maxdate|format_date:'strftimedayvshortyear'}
	<a href="{$WWWROOT}interaction/schedule/schedule.php?limit={if $limit < 63}{$limit+14}&offset={$offset}{else}{$limit}&offset={$offset+7}{/if}">{str tag=showmore section=interaction.schedule}</a></div>
	{/if}
	</div>
{include file="footer.tpl"}
