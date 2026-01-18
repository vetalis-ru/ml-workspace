<script>
  import Number from '../Controls/Number.svelte'

  export let id
  export let node
  export let store
  export let defaultValue
  let property = 'height'

  function change () {
    if (property === 'height') {
      $store[id].tree[node].props = {
        ...$store[id].tree[node].props,
        height: defaultValue.height,
        width: null
      }
    } else {
      $store[id].tree[node].props = {
        ...$store[id].tree[node].props,
        width: defaultValue.width,
        height: null
      }
    }
  }

  function changeWidth (event) {
    $store[id].tree[node].props.width = event.detail.value
  }

  function changeHeight (event) {
    $store[id].tree[node].props.height = event.detail.value
  }
</script>

<div class="root">
    <div class="btn-group">
        <label class="btn img-size-width" class:active={property === 'width'}>
            <input type="radio" bind:group={property} on:change={change} value="width" name="property">
            <span class="icon-img-width">Ширина</span>
        </label>
        <label class="btn img-size-height" class:active={property === 'height'}>
            <input type="radio" bind:group={property} on:change={change} value="height" name="property">
            <span class="icon-img-height">Высота</span>
        </label>
    </div>
    {#if property === 'width'}
        <div class="col">
            <Number value={$store[id].tree[node].props.width} on:change={changeWidth}/>
        </div>
    {:else }
        <div class="col">
            <Number value={$store[id].tree[node].props.height} on:change={changeHeight}/>
        </div>
    {/if}
</div>
<style>
    .root {
        display: flex;
        justify-content: space-between;
        --input-width: 90px
    }

    .col {

    }

    .btn-group {
        display: flex;
        margin-bottom: 5px;
    }

    [type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .btn {
        display: inline-block;
        border: solid 1px #cccccc;
        background-color: #ffffff;
        border-radius: 17px;
        padding: 6px 12px;
        height: 32px;
        box-sizing: border-box;
        color: #555555;
    }

    .img-size-width {
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }

    .img-size-height {
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }

    .active {
        background-color: #e6e6e6;
    }
</style>