<script>
    import assets from './assets'
    export let src = ''
    export let alt = ''
</script>
<img {...$$props} src={assets('images/' + src)} alt={alt}>