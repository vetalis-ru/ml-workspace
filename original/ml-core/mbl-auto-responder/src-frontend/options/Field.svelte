<script>
  export let id
  export let name
  export let value
</script>
<div class="mblar-setting">
    <div class="mblar-setting-l">
        {#if !$$slots.shortcode}
            <label for={id} class="mblar-setting-label">
                <slot name="label"></slot>
            </label>
        {:else }
        <span class="mblar-setting-label">
            <slot name="label"></slot>
        </span>
        {/if}
    </div>
    <div class="mblar-setting-r">
        {#if !$$slots.shortcode}
            <slot name="input" id={id} _name={name} value={value}>
                <input type="text" id={id} name={name} value={value} style="width: 100%;">
            </slot>
        {:else }
            <slot name="shortcode"></slot>
        {/if}
    </div>
</div>
<style>
    .mblar-setting {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        flex-wrap: wrap;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center
    }

    .mblar-setting-r {
        max-width: 450px;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1
    }

    .mblar-setting-l, .mblar-setting-r {
        padding: 10px 10px 10px 0;
    }

    @media (min-width: 768px) {
        .mblar-setting-l {
            width: 200px;
        }
    }

    .mblar-setting-label {
        color: #1d2327;
        font-weight: bold
    }
</style>