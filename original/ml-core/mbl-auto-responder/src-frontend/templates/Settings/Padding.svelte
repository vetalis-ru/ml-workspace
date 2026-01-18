<script>
  import Padding from '../Controls/Padding.svelte'

  export let id
  export let node
  export let store

  $: padding = {
    top: parseInt($store[id].tree[node].props.style['padding-top']),
    right: parseInt($store[id].tree[node].props.style['padding-right']),
    bottom: parseInt($store[id].tree[node].props.style['padding-bottom']),
    left: parseInt($store[id].tree[node].props.style['padding-left'])
  }

  function change (event) {
    const padding = event.detail
    $store[id].tree[node].props.style = {
      ...$store[id].tree[node].props.style,
      'padding-top': `${padding.top}px`,
      'padding-right': `${padding.right}px`,
      'padding-bottom': `${padding.bottom}px`,
      'padding-left': `${padding.left}px`
    }
  }
</script>
<Padding padding={padding} on:change={change} checked={true}/>