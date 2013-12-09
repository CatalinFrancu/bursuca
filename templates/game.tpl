<h3>Partida #{$game->id}</h3>

<h4>Clasamentul final</h4>

<table>
  <tr>
    <th>loc</th>
    <th>utilizator</th>
    <th>agent</th>
    <th>motiv / exit code</th>
    <th>descarcă</th>
  </tr>
  {foreach from=$ranks key=i item=rank}
    {$rec=$playerRecords[$rank]}
    <tr>
      <td>{$i+1}</td>
      <td>{$rec.user->username}</td>
      <td>v{$rec.agent->version} ({$rec.agent->name})</td>
      <td>{$rec.player->getKillReason()} ({$rec.player->exitCode|default:0})</td>
      <td><a href="commands?gameId={$game->id}&agentId={$rec.agent->id}">fișierul de comenzi</a></td>
    </tr>
  {/foreach}
</table>

<h4>Reluare</h4>

<table id="gameBoard">
  <tr>
    <th rowspan="2">utilizator</th>
    <th rowspan="2">agent</th>
    <th class="companyName center">C1</th>
    <th class="companyName center">C2</th>
    <th class="companyName center">C3</th>
    <th class="companyName center">C4</th>
    <th class="companyName center">C5</th>
    <th class="companyName center">C6</th>
    <th rowspan="2" class="center">cash</th>    
    <th rowspan="2" class="center">total</th>    
  </tr>
  <tr>
    {foreach from=$game->getStartingPrices() key=i item=p}
      <th id="stockPrice_{$i+1}" class="dollars center">{$p}</th>
    {/foreach}
  </tr>
  {foreach from=$playerRecords key=i item=rec}
    <tr class="playerRow">
      <td id="username_{$i}">{$rec.user->username}</td>
      <td id="agentName_{$i}">v{$rec.agent->version} ({$rec.agent->name})</td>
      {section name="company" start=1 loop=7}
        <td id="stock_{$i}_{$smarty.section.company.index}" class="center">0</td>
      {/section}
      <td id="cash_{$i}" class="cash dollars expand center">10</td>
      <td id="total_{$i}" class="total dollars expand center">10</td>
    </tr>
  {/foreach}
  <tr>
    <td class="controlBar" colspan="10">
      <a id="controlFirst" class="controlLink" href="#" hidden>prima</a>
      <a id="controlPrev" class="controlLink" href="#" hidden>înapoi</a>
      <a id="controlNext" class="controlLink" href="#">înainte</a>
      <a id="controlLast" class="controlLink" href="#">ultima</a>
    </td>
  </tr>
</table>

<div id="moveInfo">
  <div id="die1" class="die"></div>
  <div id="die2" class="die"></div>
  <div id="moveText">
  </div>
</div>
<div style="clear: both;"></div>

<ul id="moves" hidden>
  {foreach from=$moves item=m}
    <li id="move_{$m->number-1}" data-action="{$m->action}" data-arg="{$m->arg}" data-company="{$m->company}"></li>
  {/foreach}
</table>

<script>
  $(replayInit);
</script>
