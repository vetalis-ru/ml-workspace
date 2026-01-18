<script>
  import { createEventDispatcher, onMount } from 'svelte'
  import Loader from './Loader.svelte'
  // import assets from '../assets'

  export let id
  export let value = ''
  export let ready = false
  export let mediaButtons = false
  export let bg = '#fff'
  let _editor = null
  const dispatch = createEventDispatcher()
  onMount(
    () => {
      const props = { ...window.tinyMCEPreInit.mceInit.__editor__ }
      props.toolbar1 = props.toolbar1.replace(/wp_more,/, '')
      props.toolbar2 = props.toolbar2 + ',fontselect,fontsizeselect'
      props.plugins = 'charmap,colorpicker,hr,lists,paste,tabfocus,textcolor,wordpress,wpautoresize,wpemoji,wplink,wpdialogs,wptextpattern'
      window.wp.editor.initialize(
        id,
        {
          tinymce: {
            ...props,
            selector: `#${id}`,
            // content_css: assets('css/editor-content.css'),
            setup: (editor) => {
              _editor = editor
              editor.on('init', (e) => {
                editor.getBody().style.backgroundColor = bg
                ready = true
              })
              editor.on('input', (e) => {
                change(editor.getContent(), editor)
              })
              editor.on('Change', (e) => {
                change(editor.getContent(), editor)
              })
            }
          },
          quicktags: true,
          mediaButtons: mediaButtons
        }
      )

      return () => { window.wp.editor.remove(id) }
    }
  )

  $: if (_editor && _editor.getBody()) {
    _editor.getBody().style.backgroundColor = bg
  }

  function change (content, editor) {
    dispatch('change', { content })
  }
</script>
<div class="wrap-editor-svelte">
    {#if !ready}
        <div class="l">
            <Loader style="--loader-size: 15px; --loader-color: #2196f3"/>
        </div>
    {/if}
    <div class:loading={!ready}>
        <textarea {id} value={value} class="wp-editor-area" autocomplete="off" aria-hidden="true"></textarea>
    </div>
</div>
<style>
    .wrap-editor-svelte {
        position: relative;
    }

    :global(.wrap-editor-svelte div.mce-fullscreen) {
        z-index: 999;
    }

    .loading {
        visibility: hidden;
        pointer-events: none;
    }

    .l {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
