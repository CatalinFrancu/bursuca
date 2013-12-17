<h3>Turneul #{$tourney->id}</h3>

creat pe {$tourney->created|date_format:'d.m.Y H:i'}<br>
runde: {$tourney->numRounds}<br>
participanți: {$records|@count}<br>
mărime: {$tourney->gameSize} agenți la fiecare masă

<table class="mule">
  <tr>
    <th>loc</th>
    <th>utilizator</th>
    <th>agent</th>
    <th>ELO</th>
    <th>jocuri</th>
    <th>puncte</th>
  </tr>
  {foreach from=$records key=i item=rec}
    <tr>
      <td>{$i+1}</td>
      <td>{include file="bits/user.tpl" u=$rec.user}</td>
      <td>{include file="bits/agent.tpl" a=$rec.agent}</td>
      <td>{$rec.agent->elo}</td>
      <td>{$rec.played}</td>
      <td>{$rec.score}</td>
    </tr>
  {/foreach}
</table>

<form method="post">
  <input type="hidden" id="tourneyId" name="id" value="{$tourney->id}">
  {if ($user->id == $tourney->userId) && ($maxScheduledRound < $tourney->numRounds)}
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
