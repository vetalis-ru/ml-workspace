<script>
  import Radius from '../Controls/Radius.svelte'

  export let id
  export let node
  export let store
  $: radius = {
    topLeft: parseInt($store[id].tree[node].props.style['border-top-left-radius']),
    topRight: parseInt($store[id].tree[node].props.style['border-top-right-radius']),
    bottomLeft: parseInt($store[id].tree[node].props.style['border-bottom-left-radius']),
    bottomRight: parseInt($store[id].tree[node].props.style['border-bottom-right-radius'])
  }

  function onChange ({ detail }) {
    $store[id].tree[node].props.style = {
      ...$store[id].tree[node].props.style,
      'border-top-left-radius': `${detail.topLeft}px`,
      'border-top-right-radius': `${detail.topRight}px`,
      'border-bottom-left-radius': `${detail.bottomLeft}px`,
      'border-bottom-right-radius': `${detail.bottomRight}px`
    }
  }

</script>
<Radius radius={radius} on:change={onChange} checked={true}/>