<script>
    export let id
    export let name = ''
    export let preview = ''
    function click () {
      window.location.href = window.location.href + `&edit=0&template=${id}`
    }
</script>
<div class="item-wr">
    <div class="item">
        <button class="img" on:click={click}>
            <img class="img-responsive"
                   src={preview}
                   alt="asas"/>
            <span class="select">Выбрать</span>
        </button>
        <div class="content">
            <span>{name}</span>
        </div>
    </div>
</div>
<style>
    .item-wr {
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 15px;
        flex: 0 0 33.333333%;
    }

    .item {
        position: relative;
        /*height: 150px;*/
    }

    .img {
        display: block;
        position: relative;
        width: 100%;
        height: 294px;
        border: 1px solid #dddddd;
        background-color: rgba(196,235,252,.2);
        overflow: hidden;
        cursor: pointer;
    }

    img {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translate(-50%,0);
        width: 100%;
        height: auto;
        transition: all .5s linear;
    }

    .img:hover img{
        transform: translate(-50%,Min(calc(294px - 100%),1px));
        transition-property: all;
        transition-timing-function: linear;
        transition-duration: 3s;
    }

    .content {
        height: 80px;
        box-shadow: 0 0 0 1px #ddd;
        border-radius: 0 0 10px 10px;
        padding: 12px 10px 0;
        background-color: #fff;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .select {
        background: #0d95e8;
        padding: 5px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: .3s ease;
        z-index: 1;
        color: #0c0c0c;
    }

    .img:hover .select {
        opacity: 1;
        font-size: 18px;
        font-weight: 600;
    }
</style>