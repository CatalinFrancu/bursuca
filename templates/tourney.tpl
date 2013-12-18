<div class="pageHeader">Turneul #{$tourney->id}</div>
<div class="pageDetails">
  <ul>
    <li class="right created" title="data creării">{$tourney->created|date_format:'%e %B %Y %H:%M'}</li>
    <li class="right creator" title="creator">{include file="bits/user.tpl" u=$creator}</li>
    <li class="tourneyStatus tourneyStatus{$tourney->getStatus()}" title="stare">{$tourney->getStatusName()}</li>
    <li><b>{$records|@count}</b> participanți</li>
    <li><b>{$tourney->numRounds}</b> runde</li>
    <li><b>{$tourney->gameSize}</b> agenți la masă</li>
  </ul>
</div>

<h3>Agenți</h3>

<table class="mule">
  <tr>
    <th>loc</th>
    <th>utilizator</th>
    <th>ELO</th>
    <th>agent</th>
    <th>jocuri</th>
    <th>puncte</th>
  </tr>
  {foreach from=$records key=i item=rec}
    <tr>
      <td>{$i+1}</td>
      <td>{include file="bits/user.tpl" u=$rec.user}</td>
      <td>{$rec.user->elo}</td>
      <td>{include file="bits/agent.tpl" a=$rec.agent}</td>
      <td>{$rec.played}</td>
      <td>{$rec.score}</td>
    </tr>
  {/foreach}
</table>

<form method="post">
  <input type="hidden" id="tourneyId" name="id" value="{$tourney->id}">
  {if $user && ($user->id == $tourney->userId) && ($maxScheduledRound < $tourney->numRounds)}
    <input type="submit" name="scheduleNextRound" value="creează runda {$maxScheduledRound+1} / {$tourney->numRounds}">
    <input type="submit" name="scheduleAllRounds" value="creează toate rundele rămase">
  {/if}
</form>

<h3>Partide</h3>

<table id="gameListingGrid"></table>
<div id="pager"></div>

<script>
  $(tourneyGamesInit);
</script>
