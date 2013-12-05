{if $agents}
  <h3>agenții mei</h3>
{/if}

{foreach from=$agents item=a}
  <div>
    v{$a->version}. {$a->name} <a class="sourceCodeLink" href="agentSourceCode?id={$a->id}">cod-sursă</a>
  </div>
{/foreach}

<h3>adaugă un agent</h3>
<form method="post" enctype="multipart/form-data">
  Alege un fișier:
  <input type="file" name="file">
  <input type="text" name="name" maxlength="50" size="30" placeholder="nume (obligatoriu)">
  <input type="submit" name="submitButton" value="adaugă">
</form>

