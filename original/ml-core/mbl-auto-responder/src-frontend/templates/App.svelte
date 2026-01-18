<script>
  import Tabs from './Tabs.svelte'
  import Nav from './Nav.svelte'
  import Settings from './Settings.svelte'
  import Variants from './Variants.svelte'
  import Frame from './Template/Frame.svelte'
  import FrameBody from './Template/FrameBody.svelte'
  import { error, ui } from './store/ui'
  import Error from '../components/Error.svelte'

  $: if ($error !== '') {
    if ($ui.timerId) {
      clearTimeout($ui.timerId)
    }
    const timerId = setTimeout(() => {
      $error = ''
      $ui = { ...$ui, timerId: null }
    }, 5000)
    $ui = { ...$ui, timerId }
  }
</script>
<div class="container" id="EmailEditor">
    <Error open={!!$error} message={$error}/>
    <Nav/>
    <div class="row" class:block-ui={$ui.loading}>
        <div class="col1">
            <div class="view">
                <Tabs>
                    <div class="blocks" slot="blocks">
                        <Variants/>
                    </div>
                    <div class="settings" slot="settings">
                        <Settings/>
                    </div>
                </Tabs>
            </div>
        </div>
        <div class="col2">
            <Frame Component={FrameBody} name="Svelte"/>
        </div>
    </div>
</div>

<style>
    .container {
        height: calc(100vh - 97px);
        box-sizing: border-box;
        overflow-y: auto;
    }

    .row {
        position: relative;
        display: flex;
        height: calc(100% - 50px);
        box-sizing: border-box;
    }

    .col1 {
        overflow-y: auto;
        overflow-x: hidden;
        /*padding-right: 10px;*/
        flex: 0 0 420px;
        background: rgb(247, 247, 247);
        /*border-right: 1px solid #dddddd;*/
        border-left: 1px solid #dddddd;
        box-sizing: border-box;
    }

    .view {
        height: 100%;
        border-right: 1px solid #dddddd;
    }

    .col1::-webkit-scrollbar {
        width: 15px;
        height: 15px;
    }

    .col1::-webkit-scrollbar-thumb {
        background: #2196f3;
        border: 4px solid #fff;
    }

    .col1::-webkit-scrollbar-track {
        background: #ffffff;
    }

    .col2 {
        position: relative;
        overflow-y: hidden;
        flex-grow: 1;
        box-sizing: border-box;
    }

    .col2:before {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
    }

    :global(.color-picker--active .col2:before) {
        content: "";
    }

    .settings, .blocks {
        /*border-right: 1px solid #dddddd;*/
    }

    ::-webkit-scrollbar-track {
        background: #f6f6f6;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 6px;
        border: 2px solid #f6f6f6;
    }

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .blocks {
        overflow-y: auto;
        max-height: calc(100vh - 216px);
    }

    .block-ui:before {
        content: "";
        position: absolute;
        z-index: 11;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: #0c0c0c;
        opacity: 0.2;
    }
</style>