<script>
    let active = 'settings'
</script>
<div class="tabs">
    <button class:active={active === 'settings'} on:click={() => { active = 'settings' }}>
        Оформление
    </button>
    <button class:active={active === 'blocks'} on:click={() => { active = 'blocks' }}>
        Блоки
    </button>
</div>
<div style={active === 'blocks' ? '' : 'display:none'}>
    <slot name="blocks"></slot>
</div>
<div style={active === 'settings' ? '' : 'display:none'}>
    <slot name="settings"></slot>
</div>
<style>
    .tabs {
        display: flex;
        /*border-right: 1px solid #dddddd;*/
    }

    button {
        flex: 0 0 50%;
        margin: 0;
        border: none;
        background: #f7f7f7;
        border-bottom: 1px solid #dddddd;
        cursor: pointer;
        padding: 10px 15px;
        font-size: 16px;
        font-weight: 600;
        box-sizing: border-box;
    }
    button:first-child {
        border-right: 1px solid #dddddd;
    }

    .active {
        border-bottom-color: transparent;
        background-color: #fff;
    }
</style>