{include file="header.tpl"}
<div id="scheduleoptions">
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=0" class="btn newschedule {if $view == 0}selected{/if}">{str tag="scheduleview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=1" class="btn newschedule {if $view == 1}selected{/if}">{str tag="yearplannerview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=2" class="btn newschedule {if $view == 2}selected{/if}">{str tag="calendarview" section=interaction.schedule}</a>
</div>
{if $admin}
<div id="schedulebtns">
{if !$schedule}
<a href="{$WWWROOT}interaction/edit.php?group={$groupid}&amp;plugin=schedule&amp;returnto=index&view={$view}" class="btn newschedule">{str tag="newschedule" section=interaction.schedule}</a>
{else}
<a href="{$WWWROOT}interaction/schedule/editevent.php?schedule={$schedule->id}&view={$view}" class="btn newschedule">{str tag="newevent" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/edit.php?id={$schedule->id}&amp;group={$groupid}&amp;plugin=schedule&amp;returnto=index&view={$view}" class="btn newschedule">{str tag="schedulesettings" section=interaction.schedule}</a>
{/if}
</div>
{/if}
{if $schedule}
	<div class="color_swatch" style="background-color:{$schedule->color}"></div>
	<h2 class="schedule_title">{$schedule->title}</h2>
	<div id="viewschedule">
		{$table|safe}
	</div>
{else}
	<h2>{str tag=name section=interaction.schedule}</h2>

	<div class="message">{str tag=noschedules section=interaction.schedule}</div>
{/if}
<div class="schedulemods">
	<strong>{str tag="groupadminlist" section="interaction.schedule"}</strong>
	{foreach from=$groupadmins item=groupadmin}
    <span class="inlinelist">
        <a href="{profile_url($groupadmin)}" class="groupadmin"><img src="{profile_icon_url user=$groupadmin maxheight=20 maxwidth=20}" alt="{str tag=profileimagetext arg1=$groupadmin|display_default_name}"> {$groupadmin|display_name}</a>
    </span>
    {/foreach}
</div>
{include file="footer.tpl"}
