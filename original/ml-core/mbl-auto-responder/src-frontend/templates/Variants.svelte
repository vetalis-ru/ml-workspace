<script>
  import Accordion from '../components/Accordion.svelte'
  import Variant from './Variants/Variant.svelte'

  let active = 'headers'
  let wait = false
  const { headers, footers } = window.__mblar_data__.variants

  function loading () {
    wait = true
  }

  function stop () {
    wait = false
  }

  function settingControl (val) {
    active = val
  }
</script>
<div class="s-wrap" class:loading={wait}>
    <Accordion isOpen={active === 'headers'} on:onOpen={() => { settingControl('headers') }}>
        <span slot="label">Шапки</span>
        <div slot="body" class="variations">
            {#each headers as variant}
                <Variant {...variant} type="header"
                         on:loading={loading} on:stop={stop}
                />
            {/each}
        </div>
    </Accordion>
    <Accordion isOpen={active === 'footers'} on:onOpen={() => { settingControl('footers') }}>
        <span slot="label">Футеры</span>
        <div slot="body" class="variations">
            {#each footers as variant}
                <Variant {...variant} type="footer"
                         on:loading={loading} on:stop={stop}
                />
            {/each}
        </div>
    </Accordion>
</div>
<style>
    .s-wrap {
        position: relative;
    }

    .loading:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 11;
    }

    .variations {
        display: flex;
        flex-wrap: wrap;
    }

    ::-webkit-scrollbar-track {
        background: #f6f6f6;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 6px;
        border: 2px solid #f6f6f6;
    }

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
</style>