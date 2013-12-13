<h3>Agent: v{$agent->version} ({$agent->name})</h3>

utilizator: {include file="bits/user.tpl" u=$owner}<br>
ELO: {$agent->elo}<br>
adăugat pe {$agent->created|date_format:'d.m.Y H:i'}<br>

{if !$agent->rated}
  <br>Acest agent nu participă la rating.<br>
{/if}

{if $showSourceCode}
  <h3>Cod-sursă</h3>

  {strip}
    <pre data-language="C">{$sourceCode}</pre>
  {/strip}
{/if}
