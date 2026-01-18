<script>
  import { onMount } from 'svelte'

  export let text
  let root
  onMount(() => {
    function selection (event) {
      window.getSelection().selectAllChildren(event.target)
    }

    root.addEventListener('click', selection)

    return () => root.removeEventListener('click', selection)
  })
</script>
<span bind:this={root}><slot></slot></span>
