<script>
  import Editor from './components/Editor.svelte'
  import Switch from './components/Switch.svelte'
  import Hours from './main/Hours.svelte'
  import Minutes from './main/Minutes.svelte'
  import WeekDays from './main/WeekDays.svelte'
  import VHours from './main/VHours.svelte'
  import Days from './main/Days.svelte'
  import VMinutes from './main/VMinutes.svelte'

  export let templates = []
  export let isOn = false
  export let unsubscribe = false
  export let templateId = 0
  export let datetime = ''
  export let mails = [
    {
      mail_order: 0,
      days: 0,
      hour: 10,
      minute: 0,
      subject: '',
      interval_type: 'interval',
      message: ''
    }
  ]
  let active = 0

  function change () {
    // const mailOrder = 0
    // mails = [{
    //   mail_order: mailOrder,
    //   days: 0,
    //   hour: 10,
    //   minute: 0,
    //   subject: '',
    //   interval_type: 'interval',
    //   message: ''
    // }]
    active = 0
  }

  function add () {
    const mailOrder = mails.length
    mails = [...mails, {
      mail_order: mailOrder,
      days: 0,
      hour: 10,
      minute: 0,
      subject: '',
      interval_type: 'interval',
      message: ''
    }]
    active = mailOrder
  }

  function remove (mailOrder) {
    mails = mails.reduce((previousValue, currentValue) => {
      let next
      if (currentValue.mail_order < mailOrder) {
        next = [...previousValue, currentValue]
      } else if (currentValue.mail_order > mailOrder) {
        next = [...previousValue, { ...currentValue, mail_order: currentValue.mail_order - 1 }]
      } else {
        next = previousValue
      }
      return next
    }, [])
    active = 0
  }
</script>
<!-- <div class="wpm-inline-loader"></div>-->
<div class="top">
    <label>Включить
        <Switch value={1} bind:checked={isOn} on:change={change} name="mblar[list][is_on]"/>
    </label>
    {#if isOn}
        <select name="mblar[list][template_id]" bind:value={templateId}>
            <option value={0}>Без шаблона</option>
            {#each templates as template (template.id)}
                <option value={+template.id}>{template.name}</option>
            {/each}
        </select>
    {/if}
</div>
{#if isOn}
    <div class="tabs">
        <ul class="tabs-ul">
            {#each mails as mail, index (mail.mail_order)}
                <li class="tabs-li">
                    <button type="button" class="tabs__button"
                            class:active={active === mail.mail_order}
                            on:click={() => { active = mail.mail_order }}>
                        Письмо {mail.mail_order + 1}
                    </button>
                </li>
            {/each}
            <li class="button-wrap">
                <button on:click={add} class="button button-primary button-icon" type="button">
                    <span class="dashicons dashicons-plus"></span> <span>Письмо</span>
                </button>
            </li>
        </ul>
    </div>
    {#each mails as mail, index (mail.mail_order)}
        <div style={active === mail.mail_order ? '' : 'display:none'}>
            <input type="hidden" bind:value={mail.mail_order} name="mblar[mail][{mail.mail_order}][mail_order]">
            <div class="tab__inner">
                <div class="wpm-row">
                    <div>
                        <label>
                            <input value="interval" bind:group={mail.interval_type}
                                   type="radio" name="mblar[mail][{mail.mail_order}][interval_type]"> Дни часы минуты
                        </label>
                        <label style="margin-left: 10px;">
                            <input value="day_of_the_week" bind:group={mail.interval_type}
                                   type="radio" name="mblar[mail][{mail.mail_order}][interval_type]"> Конкретный день
                        </label>
                    </div>
                    {#if mail.interval_type === 'interval'}
                        <div class="interval">
                            Отправить письмо через
                            <label class="schedule-days-wrap">
                                <Days bind:value={mail.days} name="mblar[mail][{mail.mail_order}][days]"/>
                                дней
                            </label>
                            <label class="schedule-days-wrap">
                                <VHours bind:value={mail.hour} name="mblar[mail][{mail.mail_order}][hour]"/>
                                часов
                            </label>
                            <label class="schedule-days-wrap">
                                <VMinutes bind:value={mail.minute} name="mblar[mail][{mail.mail_order}][minute]"/>
                                минут
                            </label>
                            после
                            {#if mail.mail_order === 0}начала срока доступа{:else }предыдущего письма{/if}
                        </div>
                    {:else }
                        <div class="interval">
                            Отправить письмо в следующий
                            <label class="schedule-days-wrap">
                                <WeekDays bind:value={mail.days} name="mblar[mail][{mail.mail_order}][days]"/>
                            </label>
                            в
                            <label class="schedule-days-wrap">
                                <Hours bind:value={mail.hour} name="mblar[mail][{mail.mail_order}][hour]"/>
                                часов
                            </label>
                            <label class="schedule-days-wrap">
                                <Minutes bind:value={mail.minute} name="mblar[mail][{mail.mail_order}][minute]"/>
                                минут
                            </label>
                            после
                            {#if mail.mail_order === 0}начала срока доступа{:else }предыдущего письма{/if}
                        </div>
                    {/if}
                </div>
                <div class="wpm-row">
                    <label>
                        <input type="text" class="large-text" bind:value={mail.subject}
                               name="mblar[mail][{mail.mail_order}][subject]"
                               placeholder="Тема письма">
                    </label>
                </div>
                <div class="wpm-row">
                    <Editor id="mail_text_{mail.mail_order}" mediaButtons
                            name="mblar[mail][{mail.mail_order}][message]"
                            value={mail.message}
                            on:change={({ detail: { content } }) => { mail.message = content }}
                    />
                </div>
            </div>
            <div style="text-align: right">
                {#if mails.length > 1}
                    <button class="button button-cancel" type="button" on:click={() => { remove(mail.mail_order) }}>
                        <span class="dashicons dashicons-trash"></span> Удалить
                    </button>
                {/if}
            </div>
        </div>
    {/each}
    {#if mails.length}
        {#each window.__mblar_data__.wpm_auto_login_shortcodes_tips as tip}
            {tip.tag} - {tip.label} <br>
        {/each}
    {/if}
    {#if datetime}
        <div class="wpm-row">
            <p>Рассылка от {datetime}</p>
        </div>
    {/if}
    <div class="wpm-row bottom">
        <label>
            <input type="checkbox" value={1} name="mblar[list][unsubscribe]" bind:checked={unsubscribe}>
            Отписать от всех остальных рассылок
        </label>
    </div>
{/if}
<style>
    .interval {
        padding-top: 20px;
    }

    .button-wrap {
        display: inline-block;
        float: none;
        margin-bottom: 0;
    }

    .button-icon {
        display: inline-flex;
        align-items: center;
        height: 34px;
    }

    .dashicons {
        font-size: 14px;
        height: auto;
        width: auto;
        margin-right: 5px;
    }

    .top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .bottom {
        padding-top: 20px;
        border-top: 2px solid #0c0c0c;
    }

    .tabs {
        display: flex;
        /*border-right: 1px solid #dddddd;*/
    }

    .tabs-ul {
        width: 100%;
        padding: 0.2em 0.2em 2em !important;
        border-bottom: 2px solid black !important;
    }

    .tabs-li {
        list-style: none;
        float: left;
    }

    .tabs__button {
        cursor: pointer;
        padding: 0.5em 1em;
        text-decoration: none;
        margin: 0 8px 8px 0;
        border: 1px solid #ccc;
        background-color: #f8f8f8;
    }

    .tabs__button:first-child {
        border-right: 1px solid #dddddd;
    }

    .active {
        background-color: #3b8d69;
        color: #ffffff;
    }

    .tab__inner {
        padding: 1em 1.4em;
    }

    .button-cancel {
        display: inline-flex;
        align-items: center;

    }

    .button-cancel .dashicons {
        font-size: 20px;
    }

    .button-cancel:hover {
        color: #ffffff;
        background-color: #A03B36;
        border-color: #A03B36;
        box-shadow: 0 1px 0 #A03B36;
    }
</style>
