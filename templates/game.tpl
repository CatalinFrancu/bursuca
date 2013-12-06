<h3>Partida #{$game->id}</h3>

<table id="gameBoard">
  {foreach from=$playerRecords item=rec}
    <tr>
      <td class="shrink">{$rec.user->username}</td>
      <td class="agentName">v{$rec.agent->version} ({$rec.agent->name})</td>
      <td class="shrink">10 / 100</td>
    </tr>
  {/foreach}
</table>
