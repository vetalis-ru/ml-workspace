import App from './App.svelte'

const app = new App({
  target: document.getElementById('mblar-root'),
  props: { ...window.__mblar_data__.state }
})
export default app

if (module.hot) {
  module.hot.dispose(function () {
    document.querySelectorAll('style[id^="svelte-"]').forEach(e => e.remove())
    app.$destroy()
  })
  module.hot.accept()
}
