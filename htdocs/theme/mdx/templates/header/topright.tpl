<div id="header-right">
        <div id="right-nav">
{if $LOGGEDIN}        	<div class="username"><a href="{profile_url($USER)}">{$USER->get('firstname')}</a></div>
        	<div class="icon">            <a href="{profile_url($USER)}">
               <img src="{profile_icon_url user=$USER maxwidth=100 maxheight=100}" style="max-width:45px;max-height:65px;" alt="{str tag=profileimagetext arg1=$USER|display_default_name}" title="{$USER|display_default_name|escape}">
            </a>
</div>
{/if}
            <ul>{strip}
{if $LOGGEDIN}      <li class="identity"><a href="{profile_url($USER)}">{$USER|display_default_name}</a></li>{/if}
{if $RIGHTNAV}
{foreach from=$RIGHTNAV item=item}
                <li class="{$item.path}{if $item.selected}{assign var=MAINNAVSELECTED value=$item} selected{/if}{if $item.class} {$item.class}{/if} bar-before">
                    <a {if $item.accesskey}accesskey="{$item.accesskey}" {/if}{if $item.aria}{foreach $item.aria key=key item=value}aria-{$key}="{$value}" {/foreach}{/if}href="{if $item.wwwroot}{$item.wwwroot}{else}{$WWWROOT}{/if}{$item.url}">
                        <img src="{$item.icon}" alt="{$item.alt}">{if $item.title}{$item.title}{/if}{if $item.icon}{if isset($item.count)}<span class="navcount{if $item.countclass} {$item.countclass}{/if}">({$item.count})</span>{/if}
                    </a>
                </li>
{/foreach}
                <li class="btn-logout bar-before"><a href="{$WWWROOT}?logout" accesskey="l">{str tag="logout"}</a></li>
{/if}
            {/strip}
{if !$nosearch && !$LOGGEDIN && $languageform}
                <li id="language">
                    {$languageform|safe}
                </li>
{/if}
{if !$LOGGEDIN && !$SIDEBARS && !$LOGINPAGE}        <li id="loginlink"><a href="{$WWWROOT}?login" accesskey="l">{str tag="login"}</a></li> {/if}
            </ul>
        </div>

</div>

