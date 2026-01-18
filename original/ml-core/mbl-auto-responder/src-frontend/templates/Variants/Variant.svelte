<script>
  import axios from 'axios'
  import rest from '../../rest'
  import { template } from '../store/set'
  import Loader from '../../components/Loader.svelte'
  import { createEventDispatcher } from 'svelte'
  import { error } from '../store/ui'

  export let id
  export let name
  export let type
  export let preview
  export let loading = false
  const dispatch = createEventDispatcher()
  $: active = $template[type].id === id

  function click () {
    if (loading || active) return
    dispatch('loading')
    loading = true
    axios.get(rest(`variant/${type}/${id}`), {
      timeout: 30000
    })
      .then((res) => {
        $template[type] = res.data
      })
      .catch((r) => {
        $error = 'Что-то пошло не так. Попробуйте повторить'
      })
      .finally(() => {
        dispatch('stop')
        loading = false
      })
  }
</script>
<div class="variant">
    {#if loading}
        <div class="center">
            <Loader style="--loader-size: 16px;--loader-color: #32cb4b"/>
        </div>
    {/if}
    <button class="variant__btn" on:click={click} class:active={active}>
        <img class="variant__img" src={preview} alt=""/>
    </button>
    <p class="variant__name">{name}</p>
</div>
<style>
    .variant {
        position: relative;
        flex: 0 0 100%;
        margin-bottom: 10px;
    }

    .variant__btn {
        display: block;
        width: 100%;
        height: 125px;
        background-color: #f2f2f2;
        padding: 0 3px;
        border: 1px solid #dddddd;
        transition: all .2s ease-in-out;
        cursor: pointer;
        border-radius: 17px;
    }

    .variant__btn:hover {
        border: 1px solid #32cb4b;
        box-shadow: 0 0 0 1px #32cb4b;
    }

    .variant__img {
        max-width: 100%;
        width: auto;
        height: 100%;
        object-fit: contain;
    }

    .variant__name {
        margin: 0;
        text-align: center;
        font-weight: bold;
    }

    .active {
        border: #32cb4b 2px solid;
    }

    .center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>