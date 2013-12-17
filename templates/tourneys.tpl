<h3>turnee</h3>

<table id="tourneysGrid"></table>
<div id="pager"></div>

<h3>creează un turneu</h3>

<form method="post">
  <table class="form">
    <tr>
      <td>participanți:</td>
      <td>
        <input type="text" id="tourneyPlayers" name="tourneyPlayers" value="{$tourneyPlayers}"><br>
        <a id="addLastVersionLink" href="#">adaugă ultima versiune de agent a fiecărui jucător</a>
      </td>
    </tr>

    <tr>
      <td>runde:</td>
      <td><input type="text" name="numRounds" size="3" value="{$numRounds}"></td>
    </tr>

    <tr>
      <td>mărimea mesei:</td>
      <td>{include file="bits/select.tpl" name="gameSize" min=2 max=6 selected=$gameSize} agenți la fiecare masă</td>
    </tr>

    <tr>
      <td colspan="2">
        <input type="submit" name="submitButton" value="creează turneul">
      </td>
    </tr>

  </table>
</form>

<script>        
  $(tourneysPageInit);
  $(newTourneyInit);
</script>
