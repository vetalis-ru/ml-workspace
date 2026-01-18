<script>
  import Image from '../../Image.svelte'
  import Number from './Number.svelte'
  import Switch from './Switch.svelte'
  import { createEventDispatcher } from 'svelte'

  export let padding = {
    top: 0,
    bottom: 0
  }
  export let checked = true
  const dispatch = createEventDispatcher()
  $: style = `padding: ${padding.top}px 0 ${padding.bottom}px 0`

  function switchChange () {
    if (!checked) {
      // noinspection JSSuspiciousNameCombination
      change({
        top: padding.top,
        bottom: padding.top
      })
    }
  }

  function change (val) {
    // noinspection JSCheckFunctionSignatures
    dispatch('change', { ...padding, ...val })
  }
</script>

<div class="row">
    <div class="col-left">
        <div class="root" {style}>
            <Image src="padding-view.svg" class="root__img"/>
        </div>
    </div>
    <div class="col-right">
        <div class="root__control">
            <span style="margin-right: 10px;">Отдельно</span>
            <Switch bind:checked on:change={switchChange}/>
        </div>
        {#if checked}
            <div class="root__control">
                <span style="margin-right: 10px;">Сверху</span>
                <div class="number">
                    <Number value={padding.top}
                            on:change={e => { change({ top: e.detail.value }) }}
                    />
                </div>
            </div>
            <div class="root__control">
                <span style="margin-right: 10px;">Снизу</span>
                <div class="number">
                    <Number value={padding.bottom}
                            on:change={e => { change({ bottom: e.detail.value }) }}
                    />
                </div>
            </div>
        {:else}
            <div class="root__control">
                <span style="margin-right: 10px;">Все</span>
                <div class="number">
                    <Number value={padding.top}
                            on:change={e => {
                              change({
                                top: e.detail.value,
                                bottom: e.detail.value
                             })
                            }}
                    />
                </div>
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

    .root {
        border: 1px dashed #8ab7ec;
        background: #d5e8ff;
        overflow: hidden;
    }

    .root__img {
        width: 100%;
        margin: 0 auto;
        background: #ffffff;
        padding: 8px;
    }

    .root__control {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .number {
        --input-width: 32px
    }
</style>