<script>
  export let id
  export let name
  export let value
</script>
<div class="mblar-setting">
    <div>
        <label for={id} class="mblar-setting-label">
            <slot name="label"></slot>
        </label>
    </div>
    <div>
        <input type="text" id={id} name={name} value={value} style="width: 100%;">
    </div>
</div>
<style>
    .mblar-setting {
        margin-bottom: 12px;
    }

    .mblar-setting-label {
        color: #1d2327;
        margin-bottom: 8px;
    }
    input {
        width: 100%;
        max-width: 768px;
    }
</style>