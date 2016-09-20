<div class="project-info">
		{if $group->leveldesc}<p> {$group->leveldesc}</p>{/if}
		{if $group->moduledesc}<p>{str tag=groupmodule section=blocktype.projectinfo} {$group->moduledesc}</p>{/if}
		{if count($group->admins) > 0}
			<p>{str tag=groupadmins section=blocktype.projectinfo} {foreach name=admins from=$group->admins item=user}
			<a href="{profile_url($user)}">{$user|display_name}</a>{if !$.foreach.admins.last}, {/if}
			{/foreach}</p>
		{/if}
		{if count($group->tutors) > 0}
			<p>{str tag=grouptutors section=blocktype.projectinfo} {foreach name=tutors from=$group->tutors item=user}
			<a href="{profile_url($user)}">{$user|display_name}</a>{if !$.foreach.admins.last}, {/if}
			{/foreach}</p>
		{/if}
		{if count($group->ta) > 0}
			<p>{str tag=groupta section=blocktype.projectinfo} {foreach name=tutors from=$group->ta item=user}
			<a href="{profile_url($user)}">{$user|display_name}</a>{if !$.foreach.admins.last}, {/if}
			{/foreach}</p>
		{/if}
			<p>
	{$text|clean_html|safe}</p>

</div>
