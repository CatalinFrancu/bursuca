<!DOCTYPE HTML>
<html>
  <head>
    <title>{$pageTitle|ucfirst} | Bursuca</title>
    <meta charset="utf-8">
    {foreach from=$cssFiles item=cssFile}
      <link type="text/css" href="{$wwwRoot}css/{$cssFile}" rel="stylesheet"/>
    {/foreach}
    {foreach from=$jsFiles item=jsFile}
      <script src="{$wwwRoot}js/{$jsFile}"></script>
    {/foreach}
  </head>

  <body>
    <div class="title">Bursuca</div>

    <div class="menu">
      <ul>
        <li><a href="{$wwwRoot}">{"home"|_}</a></li>
        {if $user}
          <li><a href="{$wwwRoot}games">partide</a></li>
          <li><a href="{$wwwRoot}agents">agenți</a></li>
          <li class="right"><a href="{$wwwRoot}auth/logout">{"logout"|_}</a></li>
          <li class="right"><a href="{$wwwRoot}auth/account">{"my account"|_}</a></li>
          <li class="userName right">{$user->getDisplayName()}</li>
        {else}
          <li class="right"><a id="openidLink" href="{$wwwRoot}auth/login">{"OpenID login"|_}</a></li>
        {/if}
      </ul>
    </div>

    {if $flashMessage}
      <div class="flashMessage {$flashMessageType}Type">{$flashMessage}</div>
    {/if}

    <div id="template">
      {include file=$templateName}
    </div>

    <footer>
      <div id="license">
        Bursuca este <a href="https://github.com/CatalinFrancu/bursuca">disponibil</a> sub
        licența <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License</a>.
      </div>
    </footer>
  </body>

</html>
