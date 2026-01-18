<script>
  import { template } from './store/set'
  import rest from '../rest'
  import Loader from '../components/Loader.svelte'
  import { ui } from './store/ui'

  function create () {
    $ui.loading = true
    const formData = new FormData()
    formData.append('template', JSON.stringify($template))
    fetch(rest('editor/create'), {
      method: 'POST',
      body: formData
    }).then(r => r.json())
      .then((res) => {
        if (res.ok) {
          window.location.href = `${window.__mblar_data__.plugin_uri}&edit=${res.id}`
        } else {
          console.error(res)
        }
      })
      .finally(() => {
        $ui.loading = false
      })
  }

  function save () {
    $ui.loading = true
    const formData = new FormData()
    formData.append('template', JSON.stringify($template))
    fetch(rest('editor/save'), {
      method: 'POST',
      body: formData
    }).then(r => r.json())
      .then((res) => {
        if (res.ok) {

        } else {
          console.error(res)
        }
      })
      .finally(() => {
        $ui.loading = false
      })
  }
</script>
<nav>
    <div style="width: 100%;">
        <div class="top">
            <div class="in">
                <a href={window.__mblar_data__.plugin_uri}>
                    <span class="dashicons dashicons-email-alt"></span>
                </a>
                <span class="dashicons dashicons-arrow-right-alt"></span>
                <input type="text" class="current" bind:value={$template.name} disabled={$ui.loading}/>
            </div>
            <div class="flex">
                <div class="loader-wrap">
                    {#if $ui.loading}
                        <Loader/>
                    {/if}
                </div>
                {#if (!$template.id)}
                    <button disabled={$ui.loading} on:click={create} class="button button-primary">
                        Сохранить
                    </button>
                {:else }
                    <button disabled={$ui.loading} on:click={save} class="button button-primary">
                        Сохранить
                    </button>
                {/if}
            </div>
        </div>
    </div>
</nav>
<style>
    nav {
        position: relative;
        min-height: 49px;
        background-color: #f6f6f6;
        padding: 0 15px;
        border-bottom: 1px solid #dddddd;
        display: flex;
        align-items: center;
    }

    .top {
        display: flex;
        align-items: center;
    }

    .in {
        display: flex;
        align-items: center;
        flex-grow: 1;
        padding-right: 30px;
    }

    .button {
        min-width: 180px;
    }

    a {
        text-decoration: none;
    }

    .dashicons-arrow-right-alt {
        font-size: 14px;
        line-height: 20px;
        padding: 0 5px;
    }

    .current {
        display: inline-block;
        padding: 15px 5px;
        min-height: auto;
        width: 100%;
        height: 16px;
        border: none;
        font-size: 14px;
    }

    .flex {
        display: flex;
        align-items: center;
    }

    .loader-wrap {
        display: flex;
        align-items: center;
        width: 100px;
    }
</style>