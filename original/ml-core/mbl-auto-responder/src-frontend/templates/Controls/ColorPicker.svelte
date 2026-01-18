<script>
  import { hex2Color } from 'chyme'
  import Picker, { CircleVariant } from 'svelte-awesome-color-picker'
  import CTextInput from './CTextInput.svelte'
  import { createEventDispatcher } from 'svelte'

  export let isOpen = false
  export let value = '#cccccc'
  export let _hex = value
  let input
  let button
  let wrapper
  const HEX_COLOR_REGEX = /^#?([A-F0-9]{6}|[A-F0-9]{8})$/i
  const dispatch = createEventDispatcher()
  $: if (_hex && _hex.toLowerCase() !== value.toLowerCase()) {
    // noinspection JSCheckFunctionSignatures
    dispatch('change', { value: _hex })
  }
  $: color = textColor(value)
  $: if (isOpen) {
    console.log('where')
    if (document.getElementById('EmailEditor')) {
      document.getElementById('EmailEditor').classList.add('color-picker--active')
    }
  } else {
    if (document.getElementById('EmailEditor')) {
      document.getElementById('EmailEditor').classList.remove('color-picker--active')
    }
  }

  function updateHex (val) {
    if (HEX_COLOR_REGEX.test(val)) {
      _hex = val
    } else {
      _hex = value
      input.value = value
    }
  }

  function mousedown ({ target }) {
    if (isOpen && !wrapper.contains(target)) {
      isOpen = false
    }
  }

  function textColor (hex) {
    const colorFull = hex2Color({ hex })
    return (colorFull.v > 0.8 && colorFull.s < 0.2) ? '#333333' : '#ffffff'
  }

  function onInput ({ target }) {
    if (HEX_COLOR_REGEX.test(target.value)) {
      updateHex(target.value)
    }
  }

  function onChange ({ target }) {
    updateHex(target.value)
  }
</script>
<svelte:window on:mousedown={mousedown}/>
<div bind:this={wrapper}>
    <button bind:this={button} on:click={() => { isOpen = true }}>
        <input value={value}
               bind:this={input}
               on:input={onInput}
               on:change={onChange}
               type="text"
               style="background-color: {value};color: {color}">
    </button>
    <Picker bind:hex={_hex}
            isAlpha={false}
            isInput={false}
            bind:isOpen
            components={{
                ...CircleVariant,
                textInput: CTextInput
            }}
    />
</div>
<style>
    div {
        display: inline-block;
    }

    button {
        display: flex;
        align-items: center;
        cursor: pointer;
        border: none;
        background: none;
        padding: 0;
    }

    input {
        border: 1px solid #cccccc;
        border-radius: 17px;
        padding: 6px 12px;
        color: #fff;
        height: 32px;
        width: 100px;
        box-sizing: border-box;
    }
</style>