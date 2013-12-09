{if $agents}
  <h3>agenții mei</h3>
{/if}

<table>
  <tr>
    <th>versiune</th>
    <th>nume</th>
    <th>acțiuni</th>
  </tr>
  {foreach from=$agents item=a}
    <tr>
      <td>v{$a->version}</td>
      <td>{$a->name}</td>
      <td><a href="agentSourceCode?id={$a->id}">cod-sursă</a></td>
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

