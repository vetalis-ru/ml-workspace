<script>
  import Confirm from '../components/Confirm.svelte'
  import Loader from '../components/Loader.svelte'
  import Snackbar from '../components/Snackbar.svelte'
  import { fade } from 'svelte/transition'
  import rest from '../rest'

  let timerId = null
  let loading = false
  let successSave = false
  let successMessage = ''
  let list = window.__mblar_data__.templates
  let removeCandidate = null
  let copyCandidate = null

  $: if (loading) {
    successSave = false
    removeCandidate = null
    copyCandidate = null
  }

  $: if (successSave) {
    if (timerId) {
      clearTimeout(timerId)
    }
    timerId = setTimeout(() => { successSave = false }, 5000)
  }

  function prepareRemove (id) {
    removeCandidate = list.filter(t => +t.id === +id)[0]
  }

  function cancelRemove () {
    removeCandidate = null
  }

  function confirmRemove () {
    const id = +removeCandidate.id
    loading = true
    fetch(rest(`editor/remove/${id}`), {
      method: 'POST'
    }).then(r => r.json())
      .then(r => {
        if (r.ok) {
          list = list.filter(t => +t.id !== id)
          successMessage = 'Шаблон удален'
          successSave = true
        }
      })
      .finally(() => {
        loading = false
      })
  }

  function confirmCopy () {
    const id = copyCandidate.id
    loading = true
    fetch(rest(`editor/copy/${id}`), {
      method: 'POST'
    }).then(r => r.json())
      .then(r => {
        if (r.ok) {
          list = [...list, r.template]
          successMessage = 'Шаблон скопирован'
          successSave = true
        }
      })
      .finally(() => {
        loading = false
      })
  }

  function cancelCopy () {
    copyCandidate = null
  }

  function prepareCopy (id) {
    copyCandidate = list.filter(t => +t.id === +id)[0]
  }
</script>
<Confirm open={!!removeCandidate}
         title="Подтвердите удаление"
         on:cancel={cancelRemove} on:confirm={confirmRemove}>
    {#if removeCandidate}
        <p style="font-size: 18px;text-align: center">{removeCandidate.name}</p>
    {/if}
</Confirm>
<Confirm open={!!copyCandidate}
         title="Подтвердите копирование"
         on:cancel={cancelCopy} on:confirm={confirmCopy}>
    {#if copyCandidate}
        <p style="font-size: 18px;text-align: center">{copyCandidate.name}</p>
    {/if}
</Confirm>
<Snackbar open={successSave} message={successMessage}/>
<div class="mblc-table card mb-4 p-0" style="max-width: 100%" class:loading={loading}>
    <div class="card-header py-3">
        <div class="d-flex align-items-center m-0 font-weight-bold text-primary">
            <a href={window.__mblar_data__.addPage} class="btn btn-outline-primary">
                Добавить новый
            </a>
            {#if loading}
                <Loader style="margin-left: 15px"/>
            {/if}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="dataTables_wrapper dt-bootstrap4" style="padding: 0 15px;">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-hover table-bordered dataTable" id="dataTable" width="100%"
                               cellspacing="0" role="grid" aria-describedby="dataTable_info"
                               style="width: 100%; border: 0px; border-bottom: 1px solid #000;">
                            <thead>
                            <tr role="row">
                                <th rowspan="1" colspan="1" style="width: 20px;">
                                    №
                                </th>
                                <th rowspan="1" colspan="1">
                                    Название
                                </th>
                                <th style="width: 150px;"></th>
                            </tr>
                            </thead>

                            <tbody>
                            {#each list as template, index (template.id)}
                                <tr in:fade={{ duration: 1000 }} out:fade={{ duration: 1000 }}>
                                    <td>{index + 1}</td>
                                    <td>
                                        <a href="{window.__mblar_data__.plugin_uri}&edit={template.id}">
                                            {template.name}
                                        </a>
                                    </td>
                                    <td>
                                        <div style="display: flex;justify-content: space-between">
                                            <button class="action" on:click={() => prepareCopy(template.id)}>
                                                <svg style="width: 16px;height: 16px;"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 512 512">
                                                    <path fill="currentColor"
                                                          d="M464 0c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48H176c-26.51 0-48-21.49-48-48V48c0-26.51 21.49-48 48-48h288M176 416c-44.112 0-80-35.888-80-80V128H48c-26.51 0-48 21.49-48 48v288c0 26.51 21.49 48 48 48h288c26.51 0 48-21.49 48-48v-48H176z"/>
                                                </svg>
                                            </button>
                                            <a class="action"
                                               href="{window.__mblar_data__.plugin_uri}&edit={template.id}">
                                                <span class="dashicons dashicons-edit"></span>
                                            </a>
                                            <button class="action" on:click={() => prepareRemove(template.id)}>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .action {
        display: inline-block;
        vertical-align: bottom;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #212529;
    }

    .action:hover {
        color: #0c5bac;
        text-decoration: none;
    }

    .dashicons {
        vertical-align: text-bottom;
        font-size: 22px;
    }

    .loading:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1;
    }
</style>