<script>
  import ColorPicker from '../Controls/ColorPicker.svelte'

  export let id
  export let node
  export let store
</script>
<ColorPicker id="bgColor{node}"
             value={$store[id].tree[node].props.style['background-color']}
             on:change={e => {
               $store[id].tree[node].props.style['background-color'] = e.detail.value
             }}
/>