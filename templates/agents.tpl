{if $agents}
  <h3>agenții mei</h3>
{/if}

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

<h3>adaugă un agent</h3>
<form method="post" enctype="multipart/form-data">
  Alege un fișier:
  <input type="file" name="file">
  <input type="text" name="name" maxlength="30" size="30" placeholder="nume (obligatoriu)">
  <input type="submit" name="submitButton" value="adaugă">
</form>

