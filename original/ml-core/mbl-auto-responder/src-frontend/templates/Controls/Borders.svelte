<!--suppress JSSuspiciousNameCombination, JSCheckFunctionSignatures -->
<script>
  import Switch from './Switch.svelte'
  import Border from './Border.svelte'
  import { createEventDispatcher } from 'svelte'

  export let id
  export let border = {
    left: {
      'border-color': '#000000',
      'border-width': 1,
      'border-style': 'solid'
    },
    right: {
      'border-color': '#000000',
      'border-width': 1,
      'border-style': 'solid'
    },
    top: {
      'border-color': '#000000',
      'border-width': 1,
      'border-style': 'solid'
    },
    bottom: {
      'border-color': '#000000',
      'border-width': 1,
      'border-style': 'solid'
    }
  }
  export let checked = true
  const dispatch = createEventDispatcher()

  function switchChange () {
    if (!checked) {
      change({
        left: border.left,
        right: border.left,
        top: border.left,
        bottom: border.left
      })
    }
  }

  function change (val) {
    dispatch('change', { ...border, ...val })
  }
</script>
<div class="row">
    <div class="col-left"></div>
    <div class="col-right">
        <div class="root__control root__control-sw">
            <span style="margin-right: 10px;">Отдельно</span>
            <Switch bind:checked on:change={switchChange}/>
        </div>
    </div>
</div>
{#if checked}
    <div class="root__control">
        <div class="label">Слева</div>
        <Border id="${id}1"
                value={border.left}
                on:change={e => { change({ left: e.detail }) }}
        />
    </div>
    <div class="root__control">
        <div class="label">Справа</div>
        <Border id="{id}2"
                value={border.right}
                on:change={e => { change({ right: e.detail }) }}
        />
    </div>
    <div class="root__control">
        <div class="label">Сверху</div>
        <Border id="{id}3"
                value={border.top}
                on:change={e => { change({ top: e.detail }) }}
        />
    </div>
    <div class="root__control">
        <div class="label">Снизу</div>
        <Border id="{id}4"
                value={border.bottom}
                on:change={e => { change({ bottom: e.detail }) }}
        />
    </div>
{:else}
    <div class="root__control">
        <div class="label">Общая</div>
        <Border id="{id}all"
                value={border.left}
                on:change={e => {
                    change({
                        left: { ...e.detail },
                        right: { ...e.detail },
                        top: { ...e.detail },
                        bottom: { ...e.detail }
                    })
                }}
        />
    </div>
{/if}
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

    .root__control {
        margin-bottom: 15px;
    }

    .root__control-sw {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .label {
        width: 100%;
        font-weight: normal;
        margin-bottom: 3px;
        white-space: nowrap;
    }
</style>