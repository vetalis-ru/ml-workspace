<script>
  import Borders from '../Controls/Borders.svelte'

  export let id
  export let node
  export let store
  $: border = {
    left: {
      'border-color': $store[id].tree[node].props.style['border-left-color'],
      'border-width': parseInt($store[id].tree[node].props.style['border-left-width']),
      'border-style': $store[id].tree[node].props.style['border-left-style']
    },
    right: {
      'border-color': $store[id].tree[node].props.style['border-right-color'],
      'border-width': parseInt($store[id].tree[node].props.style['border-right-width']),
      'border-style': $store[id].tree[node].props.style['border-right-style']
    },
    top: {
      'border-color': $store[id].tree[node].props.style['border-top-color'],
      'border-width': parseInt($store[id].tree[node].props.style['border-top-width']),
      'border-style': $store[id].tree[node].props.style['border-top-style']
    },
    bottom: {
      'border-color': $store[id].tree[node].props.style['border-bottom-color'],
      'border-width': parseInt($store[id].tree[node].props.style['border-bottom-width']),
      'border-style': $store[id].tree[node].props.style['border-bottom-style']
    }
  }

  function onChange (event) {
    const border = event.detail
    $store[id].tree[node].props.style = {
      ...$store[id].tree[node].props.style,
      'border-left-color': border.left['border-color'],
      'border-left-style': border.left['border-style'],
      'border-left-width': `${border.left['border-width']}px`,
      'border-right-color': border.right['border-color'],
      'border-right-style': border.right['border-style'],
      'border-right-width': `${border.right['border-width']}px`,
      'border-top-color': border.top['border-color'],
      'border-top-style': border.top['border-style'],
      'border-top-width': `${border.top['border-width']}px`,
      'border-bottom-color': border.bottom['border-color'],
      'border-bottom-style': border.bottom['border-style'],
      'border-bottom-width': `${border.bottom['border-width']}px`
    }
  }
</script>
<Borders border={border} on:change={onChange} checked={true} id={node}/>