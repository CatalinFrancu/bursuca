<div class="pageHeader">Utilizator: {$displayUser->username}</div>
<div class="pageDetails">
  <ul>
    <li>ELO: <b>{$displayUser->elo}</b></li>
    <li><b>{$numGames}</b> partide jucate</li>
  </ul>
</div>

<h3>Agenți</h3>

<table class="mule">
  <tr>
    <th>versiune</th>
  </tr>
  {foreach from=$agents item=a}
    <tr>
      <td>{include file="bits/agent.tpl"}</td>
    </tr>
  {/foreach}
</table>
