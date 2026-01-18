<script>
  export let block
  export let id = 'root'
  export let uid
  $: type = block.tree[id].type
  $: children = block.tree[id].children ? block.tree[id].children : []
  $: props = obProps(block.tree, id)

  function obProps (tree, id) {
    let result = {}
    let style = ''
    if (block.tree[id].props) {
      if (block.tree[id].props.style) {
        const styles = block.tree[id].props.style
        for (const key in styles) {
          style += `${key}:${styles[key]};`
        }
      }
      result = {
        ...block.tree[id].props,
        style
      }
    }

    return result
  }
</script>
{#if children.length}
    {#if type === 'text'}
        {@html children}
    {:else }
        <svelte:element this={type} {...props}>
            {#each children as _id (`${uid}_${block.id}_${_id}`)}
                <svelte:self block={block} uid={uid} id={_id}/>
            {/each}
        </svelte:element>
    {/if}
{:else }
    <svelte:element this={type} {...props}/>
{/if}
