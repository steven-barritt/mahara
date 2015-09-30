{include file="header.tpl"}
{if $timeleft}<div class="fr timeleftnotice">{str tag="timeleftnotice" section="interaction.forum" args=$timeleft}</div>{/if}
{if $eventid}
<div id="schedulebtns"><a href="{$WWWROOT}interaction/schedule/deleteevent.php?id={$eventid}&amp;returnto={$returnto}&view={$view}&month={$month}&year={$year}" class="btn" title="{str tag=delete}">
					{str tag=deleteevent section=interaction.schedule}
				</a></div><h2>{$subheading}</h2>
{/if}
{$editform|safe}
{include file="footer.tpl"}
