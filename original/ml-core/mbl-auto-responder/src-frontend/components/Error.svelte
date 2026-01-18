<script>
  export let message = ''
  export let open = false
</script>
{#if open}
    <div class="snackbar-pos">
        <div class="snackbar">
            <div class="icon-wrap">
                <svg class="icon" focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                    <path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                </svg>
            </div>
            <div class="message">{message}</div>
            <div class="close-wrap">
                <button class="close-btn" on:click={() => { open = false }}>
                    <svg class="icon" focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                        <path fill="currenColor"
                              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
{/if}
<style>
    .snackbar-pos {
        z-index: 1400;
        position: fixed;
        display: flex;
        left: 170px;
        -webkit-box-pack: start;
        justify-content: flex-start;
        -webkit-box-align: center;
        align-items: center;
        bottom: 30px;
    }

    :global(.folded .snackbar-pos) {
        left: 46px;
    }

    @media (max-width: 960px) {
        .snackbar-pos {
            left: 46px;
        }
    }

    .snackbar {
        transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        border-radius: 4px;
        box-shadow: rgb(0 0 0 / 20%) 0px 3px 5px -1px, rgb(0 0 0 / 14%) 0px 6px 10px 0px, rgb(0 0 0 / 12%) 0px 1px 18px 0px;
        font-family: Roboto, Helvetica, Arial, sans-serif;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.43;
        letter-spacing: 0.01071em;
        background-color: rgb(211, 47, 47);
        display: flex;
        padding: 6px 16px;
        color: rgb(255, 255, 255);
    }

    .icon-wrap {
        margin-right: 12px;
        padding: 7px 0px;
        display: flex;
        font-size: 22px;
        opacity: 0.9;
    }

    .icon {
        user-select: none;
        width: 1em;
        height: 1em;
        display: inline-block;
        fill: currentcolor;
        flex-shrink: 0;
        transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-size: inherit;
    }

    .close-wrap {
        display: flex;
        align-items: flex-start;
        padding: 4px 0px 0px 16px;
        margin-left: auto;
        margin-right: -8px;
    }

    .close-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        background-color: transparent;
        outline: 0px;
        border: 0px;
        margin: 0px;
        cursor: pointer;
        user-select: none;
        vertical-align: middle;
        appearance: none;
        text-decoration: none;
        text-align: center;
        flex: 0 0 auto;
        border-radius: 50%;
        overflow: visible;
        transition: background-color 150ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        color: #fff;
        padding: 5px;
        font-size: 1.125rem;
    }

    .close-btn:hover {
        background-color: rgba(0, 0, 0, 0.04);
    }

    .message {
        padding: 8px 0px;
        min-width: 0px;
        overflow: auto;
    }
</style>