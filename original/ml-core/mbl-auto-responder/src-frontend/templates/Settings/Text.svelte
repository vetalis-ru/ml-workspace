<script>
  import TextEditor from '../../components/TextEditor.svelte'

  export let id
  export let node
  export let store
  // eslint-disable-next-line camelcase
  export let bg_node
  // eslint-disable-next-line camelcase
  $: bg = $store[id].tree[bg_node] ? $store[id].tree[bg_node].props.style['background-color'] : '#ffffff'
</script>
<TextEditor id={`${id}_${node}`}
            bg={bg}
            value={$store[id].tree[node].children}
            on:change={({ detail }) => { $store[id].tree[node].children = detail.content }}
/>