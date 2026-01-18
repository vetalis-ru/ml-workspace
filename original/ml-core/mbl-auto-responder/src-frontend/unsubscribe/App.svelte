<script>
</script>
<form>
    <p class="title">Пожалуйста, подтвердите отписку от рассылок</p>
    <hr/>
    <div class="mailing">
        <p>Рассылки:</p>
        <ul class="mailing-list">
            <li>
                <label>
                    <input type="checkbox"> <span class="name">Курс философия и психология</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox"> <span class="name">Продвинутый курс</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox"> <span class="name">Философия PRO</span>
                </label>
            </li>
        </ul>
    </div>
    <button type="submit">Сохранить изменения</button>
</form>
<style>
    .title {
        font-size: 32px;
        font-weight: bold;
    }

    hr {
        margin: 10px 0 30px;
        border-bottom: 1px solid #eee;
    }

    .mailing {
        box-sizing: border-box;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .mailing-list {
        list-style: none;
        flex-basis: 75%;
        max-width: 75%;
        margin: 0;
    }

    label {
        display: inline-flex;
        align-items: center;
    }

    input {
        display: inline-block;
        margin: 0;
        width: 16px;
        height: 16px;
    }

    .name {
        font-size: 16px;
        margin-left: 0.5em;
    }

    button {
        display: inline-block;
        border-radius: 4px;
        background: #A0B0A1;
        color: #FFFFFF;
        border: none;
        padding: 15px;
        font-size: 1.4rem;
        text-align: center;
        text-transform: uppercase;
    }

    button:hover:not(:disabled) {
        background: #ADBEAD;
        color: #FFFFFF;
    }
</style>