// For use with Rich Text Tags, Categories, and Taxonomies WordPress Plugin
function kwsTriggerSave() {
    var rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
    if (rich) {
        var ed = tinyMCE.activeEditor;
        if ('mce_fullscreen' == ed.id || 'wp_mce_fullscreen' == ed.id) {
            tinyMCE.get(0).setContent(ed.getContent({format : 'raw'}), {format : 'raw'});
        }
        tinyMCE.triggerSave();
    }
}

function wpmRemoveNonRTFields () {
    jQuery('.form-field').has('#tag-description').remove();
    jQuery('.form-field').has('#category-description').remove();
    jQuery('.form-field').has('#description').remove();
}

jQuery(document).ready(function ($) {
    // Remove the non-rich fields

    var profileTable = $('.user-edit-php .form-table, .profile-php .form-table').not('.rich-text-tags').has('textarea#description');

    // We try to get things back to normal. There's no hook in WP to allow us to place the description where it's needed.
    $('table.rich-text-tags').prev('h3').insertAfter('table.rich-text-tags');
    profileTable.prev('h3').insertBefore('table.rich-text-tags');

    // We add the fields that were in the table before to the new table.
    $('tr', profileTable).each(function () {
        if ($('textarea#description', $(this)).length === 0) {
            $(this).appendTo('table.rich-text-tags');
        }
    });

    // Then we remove the old table.
    profileTable.remove();

    // Make sure you're saving the latest content
    $('input#submit').click(function (e) {
        kwsTriggerSave();
    });
    
    setTimeout(wpmRemoveNonRTFields, 5000);
});	/* end ready() */


jQuery(document).on('tinymce-editor-init', function() {
   wpmRemoveNonRTFields();
});

// On a successful save, reset field
jQuery(document).ajaxComplete(function (e, xhr, settings) {
    if (settings.data !== undefined && settings.data.match(/action=(add|update)-tag/)) {
        tinyMCE.get(0).setContent('');
        kwsTriggerSave();
    }
});