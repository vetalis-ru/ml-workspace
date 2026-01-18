<script>
    export let type
    export let props = {}
    export let children = []
</script>
{#if children.length}
    {#if type === 'text'}
        {children}
    {:else }
        <svelte:element this={type} {...props}>
            {#each children as child}
                <svelte:self type={child.type} props={child.props} children={child.children}/>
            {/each}
        </svelte:element>
    {/if}
{:else }
    <svelte:element this={type} {...props}/>
{/if}
