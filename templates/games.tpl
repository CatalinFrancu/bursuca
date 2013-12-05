<h3>partide</h3>

<a id="gameFilterToggle" href="#">filtre</a>

<div id="gameFilters" hidden>
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

<script>
  $(gameFilterInit);
</script>
