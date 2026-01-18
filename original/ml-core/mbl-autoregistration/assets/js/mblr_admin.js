jQuery(function ($) {
    $('[data-mblr-get-ar-code]').on('click', function () {
    var $holder = $('.mblr_autoreg'),
        level = $('[name="mblr_auto_registration[level]"]').val(),
        duration = $('[name="mblr_auto_registration[duration]"]').val(),
        units = $('[name="mblr_auto_registration[units]"]').val(),
        variable = $('[name="mblr_auto_registration[variable]"]').val(),
        user_agreement = $('[name="mblr_auto_registration[user_agreement]"][type="checkbox"]').prop('checked') ? 'on' : 'off',
        is_unlimited = $('[name="mblr_auto_registration[is_unlimited]"][type="checkbox"]').prop('checked') ? 1 : 0,
        data = {
          action         : 'mblr_get_auto_reg_link',
          level          : level,
          duration       : duration,
          units          : units,
          is_unlimited   : is_unlimited,
          variable       : variable,
          user_agreement : user_agreement
        };
    
    if($('[name="mblr_auto_registration[redirect_link]"]').length) {
        data.redirect_link = $('[name="mblr_auto_registration[redirect_link]"]').val();
    }

    if($('[name="mblr_auto_registration[redirect_link_new_users]"]').length) {
        data.redirect_link_new_users = $('[name="mblr_auto_registration[redirect_link_new_users]"]').val();
    }

    if(level !== '' && duration !== '' && duration) {

      mblr_loader_layout($holder, 'start');

      $.post(ajaxurl, data, function (response) {
        $('#mblr-autoreg-link').val(response.link);
        mblr_loader_layout($holder, 'stop');
      }, 'json');
    }

    return false;
  });
});

function mblr_loader($elem, action, replace) {
  var tpl = '<div class="loader-ellipse" loader>' +
    '<span class="loader-ellipse__dot"></span>' +
    '<span class="loader-ellipse__dot"></span>' +
    '<span class="loader-ellipse__dot"></span>' +
    '<span class="loader-ellipse__dot"></span>' +
    '</div>';

  action = action || 'start';
  replace = replace !== false;

  if (action === 'start') {
    $elem[replace ? 'html' : 'append'](tpl)
  } else if (action === 'stop') {
    $elem.find('[loader]').remove();
  }
}
function mblr_loader_layout($elem, action)
{
  var $holder = jQuery('<div />', {'class' : 'mblr-loader-holder'});
  if (action === 'start') {
    mblr_loader($holder);
    $elem.append($holder);
  } else if (action === 'stop') {
    $elem.find('.mblr-loader-holder').remove();
  }
}