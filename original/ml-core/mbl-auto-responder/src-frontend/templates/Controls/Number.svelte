<script>
    import { createEventDispatcher } from 'svelte'

    export let value
    const dispatch = createEventDispatcher()

    function change (value) {
      // noinspection JSCheckFunctionSignatures
      dispatch('change', { value: +value })
    }
</script>
<div class="number">
    <button on:click={() => change(+value - 1)} class="btn minus" type="button">
        <span class="dashicons dashicons-minus"></span>
    </button>
    <input type="number"
           value={value}
           on:keydown={(e) => {
               if (e.key === 'Enter') change(e.target.value)
           }}
           on:blur={(e) => change(e.target.value)}
    >
    <button on:click={() => change(+value + 1)} class="btn plus" type="button">
        <span class="dashicons dashicons-plus-alt2"></span>
    </button>
</div>
<style>
    .number {
        position: relative;
        display: flex;
    }

    .btn {
        display: inline-block;
        padding: 6px;
        height: 34px;
        border: 1px solid #cccccc;
        background-color: #ffffff;
        cursor: pointer;
    }

    .minus {
        border-top-left-radius: 17px;
        border-bottom-left-radius: 17px;
        border-right: 0;
        margin-right: -1px;
    }

    .plus {
        border-top-right-radius: 17px;
        border-bottom-right-radius: 17px;
        border-left: 0;
        margin-left: -1px;
    }

    input {
        padding: 0;
        line-height: 34px;
        height: 34px;
        min-width: 34px;
        width: var(--input-width, auto);
        border-radius: 0;
        border: 1px solid rgb(204, 204, 204);
        text-align: center;
        appearance: none;
        -webkit-appearance: none;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }
</style>