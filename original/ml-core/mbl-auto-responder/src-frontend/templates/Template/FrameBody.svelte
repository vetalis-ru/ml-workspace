<script>
  import { template } from '../store/set'
  import EmailLayout from './EmailLayout.svelte'
  import Node from './Node.svelte'
</script>
<EmailLayout>
    <Node block={$template.header} uid="header"/>
    <Node block={$template.content} uid="content"/>
    <Node block={$template.footer} uid="footer"/>
</EmailLayout>