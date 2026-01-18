<script>
  import { slide } from 'svelte/transition'
  import { createEventDispatcher } from 'svelte'

  export let defaultOpen = false
  export let isOpen = defaultOpen
  const dispatch = createEventDispatcher()
  const toggle = () => {
    isOpen = !isOpen
  }

  $: if (isOpen) {
    dispatch('onOpen')
  }
</script>
<div>
    <button class="panel" on:click={toggle} aria-expanded="{isOpen}">
        <slot name="label">
            Открыть1
        </slot>
        <span class="dashicons dashicons-arrow-right-alt2" class:active={isOpen}></span>
    </button>
    {#if isOpen}
        <div transition:slide={{ duration: 300 }} class="panel__body">
            <slot name="body">Body</slot>
        </div>
    {/if}
</div>
<style>
    .panel {
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        text-align: left;
        width: 100%;
        background-color: #fff;
        border: none;
        border-bottom: 1px solid #dddddd;
        border-radius: 0;
        padding: 12px 15px;
        margin: 0;
        color: #555555;
        box-sizing: border-box;
    }

    .panel__body {
        padding: 15px 5px 15px 15px;
        background-color: #f6f6f6;
        border-bottom: 1px solid #dddddd;
        max-height: calc(100vh - 373px);
        overflow: hidden auto;
        box-sizing: border-box;
    }

    .panel__body::-webkit-scrollbar-track {
        background: #f6f6f6;
    }

    .panel__body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 6px;
        border: 2px solid #f6f6f6;
    }

    .panel__body::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .dashicons {
        color: #D2D2D2;
        float: right;
        transition: 0.4s;
    }

    .active {
        transform: rotate(90deg);
    }
</style>