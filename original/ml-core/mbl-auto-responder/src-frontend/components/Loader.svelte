<div class="loader" {...$$props}>
    <span class="loader__dot"></span>
    <span class="loader__dot"></span>
    <span class="loader__dot"></span>
    <span class="loader__dot"></span>
</div>
<style>
    .loader {
        display: inline-block;
        font-size: 20px;
        position: relative;
        height: var(--loader-size, 1em);
        width: calc(var(--loader-size, 1em) * 5);
    }

    .loader__dot {
        display: block;
        width: var(--loader-size, 1em);
        height: var(--loader-size, 1em);
        border-radius: 0.5em;
        background: var(--loader-color, #555);
        position: absolute;
        animation-duration: 0.5s;
        animation-timing-function: ease;
        animation-iteration-count: infinite;
    }

    .loader__dot:nth-child(1) {
        left: 0;
        animation-name: reveal;
    }

    .loader__dot:nth-child(2) {
        left: 0;
        animation-name: slide;
    }

    .loader__dot:nth-child(3) {
        left: 1.5em;
        animation-name: slide;
    }

    .loader__dot:nth-child(4) {
        left: 3em;
        animation-name: reveal;
        animation-direction: reverse;
    }

    @keyframes reveal {
        from {
            transform: scale(0.001);
        }
        to {
            transform: scale(1);
        }
    }

    @keyframes slide {
        to {
            transform: translateX(1.5em);
        }
    }
</style>