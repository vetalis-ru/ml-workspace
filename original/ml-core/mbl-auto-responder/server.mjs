import { Parcel } from '@parcel/core'
import browserSyncModule from 'browser-sync'

let bundler = new Parcel({
  entries: [`./src-frontend/${process.argv[2]}`],
  defaultConfig: '@parcel/config-default',
  defaultTargetOptions: {
    distDir: './assets/js'
  },
  serveOptions: {
    port: 1234,
    host: 'localhost',
  },
  hmrOptions: {
    https: true,
    host: 'localhost',
    port: 2000
  }
})
let browserSync = browserSyncModule.create()
bundler.watch()
  .then(() => {
    browserSync.init({
      https: true,
      proxy: {
        target: 'https://etis.top/',
        ws: true
      },
      open: false,
      // open: 'local',
      files: [],
      serveStatic: ['./assets'],
      rewriteRules: [
        {
          match: new RegExp('/wp-content/plugins/mbl-auto-responder/assets/js/main.js'),
          fn: function () {
            return '/js/main.js'
          }
        },
        {
          match: new RegExp('/wp-content/plugins/mbl-auto-responder/assets/js/template-edit.js'),
          fn: function () {
            return '/js/template-edit.js'
          }
        },
        {
          match: new RegExp('/wp-content/plugins/mbl-auto-responder/assets/js/template-list.js'),
          fn: function () {
            return '/js/template-list.js'
          }
        },
        {
          match: new RegExp('/wp-content/plugins/mbl-auto-responder/assets/js/options.js'),
          fn: function () {
            return '/js/options.js'
          }
        },
        {
          match: new RegExp('/wp-content/plugins/mbl-auto-responder/assets/js/unsubscribe.js'),
          fn: function () {
            return '/js/unsubscribe.js'
          }
        },
        {
          match: new RegExp('/wp-admin/load-styles.php'),
          fn: function () {
            return '/css/admin.css'
          }
        },
      ]
    })
  })
  .catch(e => console.error(e))