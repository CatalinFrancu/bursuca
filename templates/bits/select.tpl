<select name="{$name}">
  {section name=s start=$min loop=$max+1}
    {$i=$smarty.section.s.index}
    <option value="{$i}" {if $i == $selected}selected{/if}>
      {$i}
    </option>
  {/section}
</select>
