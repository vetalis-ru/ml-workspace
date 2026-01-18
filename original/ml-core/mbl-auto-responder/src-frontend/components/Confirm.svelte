<script>
  import { createEventDispatcher } from 'svelte'

  export let title = ''
  export let open = true
  const dispatch = createEventDispatcher()

  function close () {
    open = false
  }
</script>
{#if open}
    <div class="root">
        <div on:click={close} class="backdrop"></div>
        <div class="body">
            <h3>{title}</h3>
            <slot></slot>
            <div class="group">
                <button on:click={() => {
                  open = false
                  dispatch('confirm')
                }}
                        class="btn confirm">Подтвердить
                </button>
                <button on:click={() => {
                  open = false
                  dispatch('cancel')
                }} class="btn cancel">Отмена
                </button>
            </div>
        </div>
    </div>
{/if}
<style>
    .root {
        position: fixed;
        padding-top: 50px;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 99999;
    }

    .backdrop {
        position: absolute;
        top: 0;
        background: #0c0c0c;
        opacity: 0.3;
        width: 100%;
        height: 100%;
    }

    .body {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        padding: 20px;
        width: 100%;
        z-index: 2;
    }

    @media (min-width: 768px) {
        .body {
            max-width: 540px;
        }
    }

    h3 {
        margin-bottom: 20px;
        text-align: center;
        font-size: 24px;
    }

    .group {
        margin-top: 10px;
        text-align: center;
    }

    .btn {
        display: inline-block;
        width: 120px;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .confirm {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
        margin-right: 15px;
    }

    .confirm:hover {
        color: #fff;
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .cancel {
        background: #dddddd;
    }

    .cancel:hover {
        background: #d2cece;
    }
</style>