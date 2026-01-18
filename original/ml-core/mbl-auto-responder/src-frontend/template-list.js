import ListPage from './ListPage.svelte'

const app = new ListPage({
  target: document.getElementById('mblar-root'),
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
