<h3>agenții mei</h3>

<table>
  {foreach from=$agents item=a}
    <tr>
      <td>{$a->name}</td>
    </tr>
  {/foreach}
</table>

<h3>adaugă un agent</h3>
<form method="post" enctype="multipart/form-data">
  Alege un fișier:
  <input type="file" name="file">
  <input type="text" name="name" maxlength="50" size="30" placeholder="nume (obligatoriu)">
  <input type="submit" name="submitButton" value="adaugă">
</form>

