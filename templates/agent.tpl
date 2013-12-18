<div class="pageHeader">Agent: v{$agent->version} ({$agent->name})</div>
<div class="pageDetails">
  <ul>
    <li class="right created" title="data creării">{$agent->created|date_format:'%e %B %Y %H:%M'}</li>
    <li class="creator" title="creator">
      {include file="bits/user.tpl" u=$owner}
      (ELO {$owner->elo})
    </li>
    {if !$agent->rated}
      <li class="unrated">nu participă la rating</li>
    {/if}
  </ul>
</div>

{if $showSourceCode}
  <h3>Cod-sursă</h3>

  {strip}
    <pre data-language="C">{$sourceCode}</pre>
  {/strip}
{/if}
