<script>
  export let value
  $: if (value < 1 || value > 7) {
    value = 1
  }
</script>
<select bind:value={value} {...$$props}>
    <option value={1}>Понедельник</option>
    <option value={2}>Вторник</option>
    <option value={3}>Среда</option>
    <option value={4}>Четверг</option>
    <option value={5}>Пятница</option>
    <option value={6}>Суббота</option>
    <option value={7}>Воскресенье</option>
</select>