<script>
  import Image from '../../Image.svelte'
  import Number from './Number.svelte'
  import Switch from './Switch.svelte'
  import { createEventDispatcher } from 'svelte'

  export let radius = {
    topLeft: 0,
    topRight: 0,
    bottomLeft: 0,
    bottomRight: 0
  }
  export let checked = false
  const dispatch = createEventDispatcher()
  $: style = `border-radius: ${radius.topLeft}px ${radius.topRight}px ${radius.bottomRight}px ${radius.bottomLeft}px`

  function switchChange () {
    if (!checked) {
      change({
        topLeft: radius.topLeft,
        topRight: radius.topLeft,
        bottomLeft: radius.topLeft,
        bottomRight: radius.topLeft
      })
    }
  }

  function change (val) {
    // noinspection JSCheckFunctionSignatures
    dispatch('change', { ...radius, ...val })
  }
</script>

<div class="row">
    <div class="col-left">
        <div class="radius" {style}>
            <Image src="padding-view.svg" class="radius__img"/>
        </div>
    </div>
    <div class="col-right">
        <div class="radius__control">
            <span style="margin-right: 10px;">Отдельно</span>
            <Switch bind:checked on:change={switchChange}/>
        </div>
        {#if checked}
            <div class="radius__control">
                <span style="margin-right: 10px;">Верхний слева</span>
                <Number value={radius.topLeft}
                        on:change={e => { change({ topLeft: e.detail.value }) }}
                />
            </div>
            <div class="radius__control">
                <span style="margin-right: 10px;">Верхний справа</span>
                <Number value={radius.topRight}
                        on:change={e => { change({ topRight: e.detail.value }) }}
                />
            </div>
            <div class="radius__control">
                <span style="margin-right: 10px;">Нижний справа</span>
                <Number value={radius.bottomLeft}
                        on:change={e => { change({ bottomLeft: e.detail.value }) }}
                />
            </div>
            <div class="radius__control">
                <span style="margin-right: 10px;">Нижний слева</span>
                <Number value={radius.bottomRight}
                        on:change={e => { change({ bottomRight: e.detail.value }) }}
                />
            </div>
        {:else}
            <div class="radius__control">
                <span style="margin-right: 10px;">Все</span>
                <Number value={radius.topLeft}
                        on:change={e => {
                            change({
                                topLeft: e.detail.value,
                                topRight: e.detail.value,
                                bottomLeft: e.detail.value,
                                bottomRight: e.detail.value
                            })
                        }}
                />
            </div>
        {/if}
    </div>
</div>
<style>
    .row {
        display: flex;
        justify-content: space-between;
    }

    .col-left {
        width: 25%;
    }

    .col-right {
        padding-left: 30px;
        width: 75%;
    }

    .radius {
        border: 1px dashed #8ab7ec;
        background: #d5e8ff;
        overflow: hidden;
    }

    .radius__img {
        width: 100%;
        margin: 0 auto;
        background: #ffffff;
        padding: 8px;
    }

    .radius__control {
        --input-width: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }
</style>