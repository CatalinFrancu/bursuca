<h3>Utilizator: {$displayUser->username}</h3>

<h3>Agenți</h3>

<table class="mule">
  <tr>
    <th>versiune</th>
    <th>ELO</th>
  </tr>
  {foreach from=$agents item=a}
    <tr>
      <td>{include file="bits/agent.tpl"}</td>
      <td>{$a->elo}</td>
    </tr>
  {/foreach}
</table>
