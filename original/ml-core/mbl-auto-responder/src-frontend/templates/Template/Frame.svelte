<script>
  let frame
  let win
  let doc
  let head
  let body
  let content
  let Component
  let props

  $: {
    ({ Component, ...props } = $$props)
    try {
      if (content) content.set(props)
    } catch (e) {
    }
  }

  $: if (frame) {
    if (frame.contentDocument.readyState === 'complete' && frame.contentDocument.defaultView) {
      loadHandler()
    } else {
      frame.addEventListener('load', loadHandler)
    }
  }

  function loadHandler () {
    win = frame.contentWindow
    doc = frame.contentDocument
    doc.querySelector('html').setAttribute('xmlns', 'http://www.w3.org/1999/xhtml')
    doc.querySelector('html').setAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office')
    head = doc.head
    head.innerHTML = `<meta charset="UTF-8">
            <meta content="width=device-width, initial-scale=1" name="viewport">
            <meta name="x-apple-disable-message-reformatting">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta content="telephone=no" name="format-detection">
            <title></title>
            <style>
            html {
                overflow-x: auto;
                overflow-y: auto;
            }
            ::-webkit-scrollbar-track {
                background: #f6f6f6;
            }

            ::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 6px;
                border: 2px solid #f6f6f6;
            }

            ::-webkit-scrollbar {
                width: 10px;
                height: 10px;
            }
            </style>
        `
    body = doc.body
    body.style = 'width:100%;font-family:arial, \'helvetica neue\', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;margin:0'
    doc.insertBefore(document.doctype.cloneNode(true), doc.documentElement)
    if (Component) {
      content = new Component({ target: body, props })
    }
  }
</script>
<iframe bind:this={frame} title></iframe>
<style>
    iframe {
        border: none;
        width: 100%;
        height: 100%;
    }
</style>