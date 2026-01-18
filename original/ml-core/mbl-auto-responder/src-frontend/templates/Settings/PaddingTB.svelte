<script>
  import PaddingTB from '../Controls/PaddingTB.svelte'

  export let id
  export let node
  export let store

  $: padding = {
    top: parseInt($store[id].tree[node].props.style['padding-top']),
    bottom: parseInt($store[id].tree[node].props.style['padding-bottom']),
  }

  function change (event) {
    const padding = event.detail
    $store[id].tree[node].props.style = {
      ...$store[id].tree[node].props.style,
      'padding-top': `${padding.top}px`,
      'padding-bottom': `${padding.bottom}px`,
    }
  }
</script>
<PaddingTB padding={padding} on:change={change} checked={true}/>