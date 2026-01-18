<script>
  import Image from '../Controls/Image.svelte'

  export let id
  export let node
  export let store
</script>
<Image
        bind:img={$store[id].tree[node].props.src}
        bind:thumbnail={$store[id].tree[node].props.thumbnail}
/>
