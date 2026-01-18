<script>
  import { template } from '../store/set'
</script>
<div style="padding-top: {$template.common.tree.root.props.style['padding-top']};
padding-bottom: {$template.common.tree.root.props.style['padding-bottom']};
background-color: {$template.common.tree.root.props.style['background-color']};">
    <table width="100%" cellspacing="0" cellpadding="0"
           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;margin:0;
           width:100%;height:100%;
           background-position:center top;background-repeat: no-repeat;background-size: cover;
           background-color: {$template.common.tree.root.props.style['background-color']};">
        <tr>
            <td valign="top" align="center" style="padding: 0;margin:0;">
                <table style="width:100%;height:100%;border:0;max-width: 600px;" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td valign="top">
                            <slot></slot>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>