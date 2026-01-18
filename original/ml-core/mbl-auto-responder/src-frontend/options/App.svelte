<script>
  import Alert from './Alert.svelte'
  import Shortcode from './Shortcode.svelte'
  import Field from './Field.svelte'
  import Loader from '../components/Loader.svelte'
  import { writable } from 'svelte/store'
  import rest from '../rest'
  import axios from 'axios'
  import { onMount } from 'svelte'
  import Text from './Text.svelte'

  let form
  const store = writable({
    loading: true,
    options: [],
    texts: [],
    success: false
  })

  async function submit () {
    $store = {
      ...$store,
      loading: true,
      success: false
    }
    try {
      const response = await axios.post(rest('options/save'), new FormData(form))
      if (response.data.ok) {
        $store = {
          ...$store,
          success: true,
          options: response.data.options
        }
      }
    } catch (e) {
      console.error(e)
    }
    $store.loading = false
  }

  onMount(async () => {
    try {
      const response = await axios.get(rest('options'))
      if (response.data.ok) {
        $store = {
          ...$store,
          options: response.data.options.filter(i => i.group === 'common'),
          texts: response.data.options.filter(i => i.group === 'text'),
          loading: false
        }
      }
    } catch (e) {
      console.error(e)
    }
  })
</script>
<div class="container">
    <div class="title">
        <h1 style="margin-right: 15px;">Настройки</h1>
        {#if $store.loading}
            <Loader/>
        {/if}
    </div>
    {#if $store.success}
        <Alert/>
    {/if}
    <form on:submit|preventDefault={submit} bind:this={form}>
        {#each $store.options as option (option.id)}
            <Field id="mblar_{option.id}" name={option.id} value={option.value}>
                <span slot="label">{option.label}</span>
            </Field>
        {/each}
        <h2>Шорткоды</h2>
        <Field>
            <span slot="label">Отписаться от всех рассылок</span>
            <span slot="shortcode">
                <Shortcode>[m_unsubscribe_all]</Shortcode>
            </span>
        </Field>
        <Field>
            <span slot="label">Отписаться от текущей рассылки</span>
            <span slot="shortcode">
                <Shortcode>[m_unsubscribe_current]</Shortcode>
            </span>
        </Field>
<!--        <Field>-->
<!--            <span slot="label">Форма отписки</span>-->
<!--            <span slot="shortcode">-->
<!--                <Shortcode>[m_unsubscribe_form]</Shortcode>-->
<!--            </span>-->
<!--        </Field>-->
        <h2>Тексты</h2>
        {#each $store.texts as text (text.id)}
            <Text id="mblar_{text.id}" name={text.id} value={text.value}>
                <span slot="label">{text.label}</span>
            </Text>
        {/each}
        <div class="submit">
            <button class="button button-primary" disabled={$store.loading}>Сохранить</button>
        </div>
    </form>
</div>
<style>
    .container {
        padding: 0 15px;
    }

    .title {
        display: flex;
        align-items: center;
    }

    .submit {
        margin-top: 10px;
    }
</style>