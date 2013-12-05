<h3>Agent: {$agent->name} (v{$agent->version})</h3>

lungime: {$sourceCode|strlen|number_format:0:',':'.'} octeți |
adăugat pe {$agent->created|date_format:'d.m.Y H:i'}

<pre data-language="C">
{$sourceCode}
</pre>
