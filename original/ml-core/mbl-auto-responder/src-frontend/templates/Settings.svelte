<script>
  import Accordion from '../components/Accordion.svelte'
  import Setting from './Settings/Setting.svelte'
  import { template } from './store/set'

  let active = 'content'

  function settingControl (val) {
    active = val
  }
</script>
<Accordion isOpen={active === 'common'} on:onOpen={() => { settingControl('common') }}>
    <span slot="label">
         Общие настройки
    </span>
    <div slot="body" class="body">
        <Setting template={template} block="common"/>
    </div>
</Accordion>
<Accordion isOpen={active === 'header'} on:onOpen={() => { settingControl('header') }}>
    <span slot="label">
         Шапка
    </span>
    <div slot="body" class="body">
        <Setting template={template} block="header"/>
    </div>
</Accordion>
<Accordion isOpen={active === 'content'} on:onOpen={() => { settingControl('content') }}>
    <span slot="label">
         Контент
    </span>
    <div slot="body" class="body">
        <Setting template={template} block="content"/>
    </div>
</Accordion>
<Accordion isOpen={active === 'footer'} on:onOpen={() => { settingControl('footer') }}>
    <span slot="label">
         Подвал
    </span>
    <div slot="body" class="body">
        <Setting template={template} block="footer"/>
    </div>
</Accordion>
<style>
    .body {
        min-height: 450px;
    }
</style>