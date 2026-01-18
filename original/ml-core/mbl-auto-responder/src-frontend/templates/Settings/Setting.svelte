<script>
  import BorderRadius from './BorderRadius.svelte'
  import BackgroundColor from './BackgroundColor.svelte'
  import Padding from './Padding.svelte'
  import SettingWrap from './SettingWrap.svelte'
  import Borders from './Borders.svelte'
  import Image from './Image.svelte'
  import Sizes from './Sizes.svelte'
  import Align from './Align.svelte'
  import Text from './Text.svelte'
  import PaddingTB from './PaddingTB.svelte'

  export let template
  export let block
  const settingsMap = {
    border_radius: BorderRadius,
    bg_color: BackgroundColor,
    padding: Padding,
    padding_tb: PaddingTB,
    borders: Borders,
    image: Image,
    image_size: Sizes,
    align: Align,
    text: Text
  }
</script>
{#if !$template[block].settings.length}
    Нет настроек
{:else }
    {#each $template[block].settings as setting (`${$template[block].id}_${setting.node_id}_${setting.type}`)}
        <SettingWrap>
            <div slot="label">{setting.label}</div>
            <div slot="control">
                <svelte:component
                        id={block}
                        this={settingsMap[setting.type]}
                        node={setting.node_id}
                        defaultValue={setting.default}
                        store={template}
                        {...setting}
                />
            </div>
        </SettingWrap>
    {/each}
{/if}
