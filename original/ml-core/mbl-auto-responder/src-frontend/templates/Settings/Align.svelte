<script>
  export let id
  export let node
  export let store
</script>

<div class="root">
    <div class="btn-group">
        <label class="btn img-size-left" class:active={$store[id].tree[node].props.align === 'left'}>
            <input type="radio" bind:group={$store[id].tree[node].props.align} value="left" name="align">
            <span class="dashicons dashicons-align-pull-left"></span>
        </label>
        <label class="btn img-size-center" class:active={$store[id].tree[node].props.align === 'center'}>
            <input type="radio" bind:group={$store[id].tree[node].props.align} value="center" name="align">
            <span class="dashicons dashicons-align-center"></span>
        </label>
        <label class="btn img-size-right" class:active={$store[id].tree[node].props.align === 'right'}>
            <input type="radio" bind:group={$store[id].tree[node].props.align} value="right" name="align">
            <span class="dashicons dashicons-align-pull-right"></span>
        </label>
    </div>
</div>
<style>
    .root {
        display: flex;
        justify-content: space-between;
        --input-width: 90px
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

    .img-size-left {
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }

    .img-size-center {
        border-radius: 0;
    }

    .img-size-right {
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }

    .active {
        background-color: #e6e6e6;
    }
</style>