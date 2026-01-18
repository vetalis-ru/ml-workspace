<script>
  export let hex
  const values = [
    '#fdba74', '#fb923c', '#f97316', '#fde047', '#facc15', '#bef264', '#4ade80',
    '#22c55e', '#16a34a', '#60a5fa', '#3b82f6', '#1d4ed8', '#c084fc', '#a855f7',
    '#7e22ce', '#f472b6', '#ec4899', '#db2777', '#f87171', '#ef4444', '#dc2626',
    '#d1d5db', '#9ca3af', '#374151', '#337277', '#f3f4f6', '#ffffff', '#000000'
  ]

  function changeValue (value) {
    hex = value
  }
</script>

<div class="container">
    {#each values as value, index (index)}
        <button class:active={hex === value}
                style="background: {value};"
                on:click={() => { changeValue(value) }}
                class="color-block">
        </button>
    {/each}
</div>

<style>
    .color-block {
        border: none;
        box-shadow: 0 0 1px inset;
        margin-top: 3px;
        margin-right: 3px;
        border-radius: .2rem;
        width: 24px;
        height: 24px;
        line-height: 0;
        font-size: 0;
        cursor: pointer;
    }

    .active {
        box-shadow: inset 0 0 0 1px #FFF, 0 0 3px 1px rgba(0, 0, 0, 0.25);
    }

</style>
