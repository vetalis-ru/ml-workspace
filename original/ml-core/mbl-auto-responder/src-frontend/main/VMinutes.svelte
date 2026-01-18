<script>
  export let value
</script>
<select bind:value={value} {...$$props}>
    <option value={0}>0</option>
    <option value={30}>30</option>
</select>