<div class="rbuttons">
{if $group->membershiptype == 'member'}
    <div class="member">
    {if $group->role == 'member' || $group->role == 'admin'}
        {str tag="youaregroup$group->role" section="group"}
	{elseif $group->role == 'tutor'}
    <div class="admincontrol">
        <a href="{$WWWROOT}artefact/multirecipientnotification/sendmessage.php?groupid={$group->id}&returnto=group" title="{str(tag=sendgroupmessage, section=group )}" class="btn">
            <span class="btn-message">{str(tag=sendgroupmessage, section=group )}</span>
            <span class="accessible-hidden">{str(tag=sendgroupmessage, section=group )}</span>
        </a>
        <a href="{$WWWROOT}view/blocks.php?id={$group->homepageid}" title="{str(tag=editpagespecific arg1=$group->name)|escape:html|safe}" class="btn">
            <span class="btn-edit">{str tag=editgrouppage section=group}</span>
            <span class="accessible-hidden">{str tag=editpagespecific arg1=$group->name}</span>
        </a>
    </div>
    {else}
        {str tag="youaregroup$group->role" section="grouptype.$group->grouptype"}
    {/if}
    </div>
    <div class="leavegroup">
    {if $group->canleave}
        <a href ="{$WWWROOT}group/leave.php?id={$group->id}&amp;returnto={$returnto}" class="btn"><span class="btn-leavegroup">{str tag="leavegroup" section="group"}</span></a>
    {/if}
    {if $group->invitefriends}
        <a href ="{$WWWROOT}group/inviteusers.php?id={$group->id}&friends=1" class="btn"><span class="btn-friend">{str tag="invitefriends" section="group"}</span></a>
    {elseif $group->suggestfriends && ($group->request || $group->jointype == 'open')}
        <a href ="{$WWWROOT}group/suggest.php?id={$group->id}" class="btn"><span class="btn-friend">{str tag="suggesttofriends" section="group"}</span></a>
    {/if}
    </div>
{elseif $group->membershiptype == 'admin'}
    <div class="admincontrol">
        <a href="{$WWWROOT}artefact/multirecipientnotification/sendmessage.php?groupid={$group->id}&returnto=group" title="{str(tag=sendgroupmessage, section=group )}" class="btn">
            <span class="btn-message">{str(tag=sendgroupmessage, section=group )}</span>
            <span class="accessible-hidden">{str(tag=sendgroupmessage, section=group )}</span>
        </a>
        {if $group->homepageid}
        <a href="{$WWWROOT}view/blocks.php?id={$group->homepageid}" title="{str(tag=editspecific arg1=$group->name)|escape:html|safe}" class="btn">
            <span class="btn-edit">{str tag=editgrouppage section=group}</span>
            <span class="accessible-hidden">{str tag=editspecific arg1=$group->name}</span>
        </a>
        {/if}
        <a href="{$WWWROOT}group/edit.php?id={$group->id}" title="{str(tag=editspecific arg1=$group->name)|escape:html|safe}" class="btn">
            <span class="btn-edit">{str tag=editgroup section=group}</span>
            <span class="accessible-hidden">{str tag=editspecific arg1=$group->name}</span>
        </a>
        <a href="{$WWWROOT}group/delete.php?id={$group->id}" title="{str(tag=deletespecific arg1=$group->name)|escape:html|safe}" class="btn">
            <span class="btn-del">{str tag=deletegroup1 section=group}</span>
            <span class="accessible-hidden">{str tag=deletespecific arg1=$group->name}</span>
        </a>
    </div>


    {if $group->requests}
        <div class="requestspending">
            <a href="{$WWWROOT}group/members.php?id={$group->id}&amp;membershiptype=request" class="btn"><span class="btn-pending">{str tag="membershiprequests" section="group"} ({$group->requests})</span></a>
        </div>
    {/if}
{elseif $group->membershiptype == 'invite'}
    <div class="invite">
    {if $group->role}
        {assign var=grouptype value=$group->grouptype}
        {assign var=grouprole value=$group->role}
        {str tag="grouphaveinvitewithrole" section="group"}: {str tag="$grouprole" section="grouptype.$grouptype"}
    {else}
        {str tag="grouphaveinvite" section="group"}
    {/if}
    {$group->invite|safe}
    </div>
{elseif $group->jointype == 'open'}
    <div class="jointhisgroup">{$group->groupjoin|safe}</div>
{elseif $group->membershiptype == 'request'}
    <div class="requestedtojoin">{str tag="requestedtojoin" section="group"}</div>
{elseif $group->request}
    <div class="requesttojoin"><a href="{$WWWROOT}group/requestjoin.php?id={$group->id}&amp;returnto={$returnto}" class="btn"><span class="btn-request">{str tag="requestjoingroup" section="group"}</span></a></div>
{elseif $group->jointype == 'controlled'}
    <div class="controlled">{str tag="membershipcontrolled" section="group"}</div>
{else}
    <div class="controlled">{str tag="membershipbyinvitationonly" section="group"}</div>
{/if}
</div>
