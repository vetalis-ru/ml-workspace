<script>
  import ColorPicker from './ColorPicker.svelte'
  import { createEventDispatcher } from 'svelte'
  import { clickOutside } from '../../clickOutside'

  export let id
  export let value
  export let show = false
  const dispatch = createEventDispatcher()

  function change (val) {
    // noinspection JSCheckFunctionSignatures
    dispatch('change', { ...value, ...val })
  }

  function select (val) {
    show = false
    change({ 'border-style': val })
  }
</script>
<div class="group">
    <ColorPicker id={id}
                 value={value['border-color']}
                 on:change={e => change({ 'border-color': e.detail.value })}
    />
    <div style="display: flex;padding-left: 15px;width: 100%;">
        <button on:click={() => change({ 'border-width': +value['border-width'] - 1 })}
                class="btn minus" type="button"><span class="dashicons dashicons-minus"></span></button>
        <input type="number" class="control"
               value={value['border-width']}
               on:keydown={(e) => {
                   if (e.key === 'Enter') change({ 'border-width': +e.target.value })
               }}
               on:blur={(e) => change({ 'border-width': +e.target.value })}
        />
        <div class="input-group-btn">
            <button on:click={() => change({ 'border-width': +value['border-width'] + 1 })}
                    class="btn plus" type="button"><span class="dashicons dashicons-plus-alt2"></span>
            </button>
            <div class="dr-select" use:clickOutside on:click_outside={() => { show = false }}>
                <div class="">
                    <button type="button" class="dropdown-toggle" on:click={() => { show = !show }}>
                        <span class="filter-option">
                            <span style="display: inline-block;width: 100%;border:0;
                             border-bottom:2px {value['border-style']} #5f5f5f;"></span>
                        </span>
                        &nbsp;<span class="dashicons dashicons-arrow-down"></span>
                    </button>
                </div>
                {#if show}
                    <div class="select">
                        <ul class="dropdown-menu" role="menu" style="overflow-y: auto; overflow-x: hidden;">
                            <li on:click={() => select('solid')}
                                class:active={value['border-style'] === 'solid'}
                                class="select-item" tabindex="-1">
                                <hr style="border:0; border-bottom:2px solid #5f5f5f; margin:9px 0;"/>
                            </li>
                            <li on:click={() => select('dashed')}
                                class:active={value['border-style'] === 'dashed'}
                                class="select-item" tabindex="-1">
                                <hr style="border:0; border-bottom:2px dashed #5f5f5f; margin:9px 0;"/>
                            </li>
                            <li on:click={() => select('dotted')}
                                class:active={value['border-style'] === 'dotted'}
                                class="select-item" tabindex="-1">
                                <hr style="border:0; border-bottom:2px dotted #5f5f5f; margin:9px 0;"/>
                            </li>
                        </ul>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
<style>
    :root {
        --picker-width: 270px
    }

    .group {
        position: relative;
        display: flex;
        width: 100%;
    }

    .btn {
        display: inline-block;
        padding: 6px;
        height: 34px;
        border: 1px solid #cccccc;
        background-color: #ffffff;
        cursor: pointer;
    }

    .dr-select {
        height: 32px;
        width: 100%;
        margin-left: -1px;
        background-color: #fff;
        border: 1px solid #cccccc;
        border-top-right-radius: 17px;
        border-bottom-right-radius: 17px;
    }

    .control {
        padding: 0;
        line-height: 34px;
        height: 34px;
        width: 34px;
        border-radius: 0;
        border: 1px solid rgb(204, 204, 204);
        text-align: center;
        appearance: none;
        -webkit-appearance: none;
    }

    .control {
        -moz-appearance: textfield;
    }

    .control::-webkit-outer-spin-button,
    .control::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }

    .minus {
        border-top-left-radius: 17px;
        border-bottom-left-radius: 17px;
        border-right: 0;
        margin-right: -1px;
    }

    .plus {
        border-left: 0;
        margin-left: -1px;
    }

    .input-group-btn {
        display: flex;
        width: 100%;
    }

    .dropdown-toggle {
        height: 32px;
        width: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
    }

    .filter-option {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .select {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        padding: 9px 0;
        z-index: 1060;
        min-width: 100%;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 17px;
        background-color: #fff;
        margin: 0;
    }

    li {
        padding: 6px 15px;
        cursor: pointer;
    }

    li:hover {
        background-color: #32cb4b;
    }

    .active {
        background-color: #32cb4b;
    }
</style>