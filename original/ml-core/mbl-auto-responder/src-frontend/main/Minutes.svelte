<script>
  export let value
</script>
<select bind:value={value} {...$$props}>
    <option value={0}>00</option>
    <option value={30}>30</option>
</select>