<script>
  import Loader from '../../components/Loader.svelte'

  let frame
  export let img
  export let thumbnail
  export let loading = false

  function click () {
    if (!frame) {
      frame = window.wp.media({
        title: 'Выберите или загрузите изображение',
        button: {
          text: 'Выбрать изображение'
        },
        multiple: false
      })
      frame.on('select', function () {
        const info = frame.state().get('selection').first().toJSON()
        thumbnail = info.id
      })
    }
    frame.open()
  }

  $: if (thumbnail) {
    loading = true
    fetch(`/wp-json/wp/v2/media/${thumbnail}`)
      .then(r => r.json())
      .then(({ source_url: sourceUrl }) => { img = sourceUrl })
      .finally(() => { loading = false })
  }
</script>
{#if loading}
    <div class="center">
        <Loader style="--loader-size: 16px;--loader-color: #32cb4b"/>
    </div>
{/if}
<button on:click={click}>
    {#if img}
        <span>Сменить</span>
        <img src={img} alt="">
    {:else }
        Выбрать картинку
    {/if}
</button>
<style>
    button {
        position: relative;
        display: block;
        box-shadow: 0 0 0 0 #007cba;
        font-size: 16px;
        border-radius: 5px;
        background-color: #f0f0f0;
        width: 100%;
        height: 150px;
        line-height: 20px;
        padding: 8px 0;
        border: 0;
        text-align: center;
        cursor: pointer;
        overflow: hidden;
        font-weight: 600;
        transition: 0.3s ease;
    }

    button:hover {
        background: #ddd;
        color: #1e1e1e;
    }

    span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: 0.3s ease;
        font-size: 16px;
        font-weight: 600;
    }

    img {
        object-fit: contain;
        max-width: 100%;
        width: auto;
        height: 100%;
    }

    button:hover span {
        opacity: 1;
    }

    button:hover img {
        opacity: .5;
    }

    .center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 3;
    }
</style>