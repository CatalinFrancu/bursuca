<h3>Bun venit la Bursuca</h3>

<p>Bursuca este un joc care simulează o bursă de acțiuni. Acest site organizează competiții între programe de jucat Bursuca.</p>

<p>Puteți citi <a href="manual">documentația</a>.</p>

<h3>Cei mai buni agenți</h3>

<table>
  <tr>
    <th>utilizator</th>
    <th>agent</th>
    <th>ELO</th>
  </tr>
  {foreach from=$agents key=i item=a}
    <tr>
      <td>{include file="bits/user.tpl" u=$users[$i]}</td>
      <td>{include file="bits/agent.tpl"}</td>
      <td>{$a->elo}</td>
    </tr>
  {/foreach}
</table>
