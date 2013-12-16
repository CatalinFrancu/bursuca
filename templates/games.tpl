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

  <div id="all" hidden>{$all}</div>
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

<table id="gameListingGrid"></table>
<div id="pager"></div>

<script>
  $(gameFilterInit);
  $(newGameInit);
  $(gamesPageInit);
</script>
