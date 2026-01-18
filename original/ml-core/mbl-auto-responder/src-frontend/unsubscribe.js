import App from './unsubscribe/App.svelte'

const app = new App({
  target: document.getElementById('mblar-unsubscribe-root'),
  props: {}
})
export default app

if (module.hot) {
  module.hot.dispose(function () {
    document.querySelectorAll('style[id^="svelte-"]').forEach(e => e.remove())
    app.$destroy()
  })
  module.hot.accept()
}
