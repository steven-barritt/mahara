    <div class="sidebar-header">
    </div>
    <div class="sidebar-content">
{if $sbdata.mnetloggedinfrom}        <p>{$sbdata.mnetloggedinfrom|clean_html|safe}</p>
{/if}
        <ul>
{if $sbdata.invitedgroups}
            <li id="invitedgroups"><a href="{$WWWROOT}group/mygroups.php?filter=invite" class="btn-group">
                <span id="invitedgroupscount">{$sbdata.invitedgroups}</span>
                <span id="invitedgroupsmessage">{$sbdata.invitedgroupsmessage}</span>
            </a></li>
{/if}
{if $sbdata.pendingfriends}
            <li id="pendingfriends"><a href="{$WWWROOT}user/myfriends.php?filter=pending" class="btn-friend">
                <span id="pendingfriendscount">{$sbdata.pendingfriends}</span>
                <span id="pendingfriendsmessage">{$sbdata.pendingfriendsmessage}</span>
            </a></li>
{/if}
{if $sbdata.groups}
            <li id="groups"><a href="{$WWWROOT}group/mygroups.php">{str tag="mygroups"}:</a>
                <ul>
{foreach from=$sbdata.groups item=group}
                    <li><a href="{group_homepage_url($group)}">{$group->name}</a>{if $group->role == 'admin'} ({str tag=Admin section=group}){/if}</li>
{/foreach}
                </ul>
                <span class="tiny">{$sbdata.grouplimitstr}</span>
                </li>
{/if}
{if $sbdata.views}
            <li id="views"><a href="{$WWWROOT}view/">{str tag="views"}:</a>
                <ul>
{foreach from=$sbdata.views item=view}
                    <li><a href="{$view->fullurl}">{$view->title}</a></li>
{/foreach}
                </ul>
            </li>
{/if}
{if $sbdata.artefacts}
            <li class="artefacts">
                {str tag="Artefacts"}:
                <ul>
{foreach from=$sbdata.artefacts item=artefact}
{if $artefact->artefacttype == 'blog'}
                    <li><a href="{$WWWROOT}artefact/blog/view/index.php?id={$artefact->id}">{$artefact->title}</a></li>
{elseif $artefact->artefacttype == 'blogpost'}
                    <li><a href="{$WWWROOT}artefact/blog/view/index.php?id={$artefact->blogid}">{$artefact->title}</a></li>
{elseif $artefact->artefacttype == 'file' || $artefact->artefacttype == 'image' || $artefact->artefacttype == 'profileicon' || $artefact->artefacttype == 'archive'}
                    <li><a href="{$WWWROOT}artefact/file/download.php?file={$artefact->id}">{$artefact->title}</a></li>
{elseif $artefact->artefacttype == 'folder'}
                    <li><a href="{$WWWROOT}artefact/file/index.php?folder={$artefact->id}">{$artefact->title}</a></li>
{/if}
{/foreach}
                </ul>
            </li>
{/if}
        </ul>
{if $sbdata.peer}                <div id="sbdatapeer"><a href="{$sbdata.peer.wwwroot}">{$sbdata.peer.name}</a></div>
{/if}
{if $USERMASQUERADING}        <div id="changeuser">{$becomeyouagain|safe}</div>
{/if}
    </div>
