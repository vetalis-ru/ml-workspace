<div class="setting">
    <div class="setting__label">
        <slot name="label"></slot>
    </div>
    <div class="setting__group">
        <slot name="control"></slot>
    </div>
</div>
<style>
    .setting {
        color: #555555;
        font-size: 14px;
    }

    .setting__label {
        padding-top: 7px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .setting__group {
        position: relative;
        margin-bottom: 15px;
    }
</style>