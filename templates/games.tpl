<h3>partide</h3>

<ul class="gameMenu">
  <li><a id="gameFilterToggle" href="#">filtre</a></li>
  <li><a id="newGameToggle" href="#">creează o partidă nouă</a></li>
</ul>

<div id="gameFilters" class="gameBox" hidden>
  <form>
    arată partidele jucătorului: <input id="userFilter" type="text" name="userId" value="{$userId}">
    <button type="submit">aplică</button>
  </form>

  <form>
    arată partidele agentului: <input id="agentFilter" type="text" name="agentId" value="{$agentId}">
    <button type="submit">aplică</button>
  </form>

  <a href="?all=1">arată toate partidele</a>
</div>

<div id="newGame" class="gameBox" hidden>
  <form>
    Alege 2-6 agenți în ordinea dorită. Trebuie ca măcar unul dintre ei să îți aparțină.<br><br>
    <input type="text" name="newGamePlayers[]" value=""><br>
    <input type="text" name="newGamePlayers[]" value=""><br>
    <input type="text" name="newGamePlayers[]" value=""><br>
    <input type="text" name="newGamePlayers[]" value=""><br>
    <input type="text" name="newGamePlayers[]" value=""><br>
    <input type="text" name="newGamePlayers[]" value=""><br/>
    <button type="submit">creează partida</button>
  </form>
</div>

<table class="gameList">
  <tr>
   <th>ID</th>
   <th>jucători</th>
   <th>creată la</th>
   <th>stare</th>
  </tr>
  {foreach from=$gameRecords item=rec}
    <tr>
      <td>{$rec.game->id}</td>
      <td>
        {foreach from=$rec.users key=i item=u}
          {$u->username} v{$rec.agents[$i]->version} ({$rec.agents[$i]->name})<br/>
        {/foreach}
      </td>
      <td>{$rec.game->created|date_format:'d.m.Y H:i'}</td>
      <td>{$rec.game->getStatusName()}</td>
    </tr>
  {/foreach}
</table>

<script>
  $(gameFilterInit);
  $(newGameInit);
</script>
