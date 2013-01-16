/**
 * @file
 * Some UI improvements.
 *
 * Borrowed from seventeen theme by Chi:
 * http://drupal.org/project/seventeen
 */
(function ($) {
  Drupal.behaviors.seventeen = {
    attach: function(context) {

      $('.messages').prepend('<span class="hide-message">X</span>').fadeIn(1000);
      $('.hide-message').click(function(){$(this).parent().fadeOut()});

      $('body.page-admin-modules td.checkbox').each(function(index, value) {
        if ($(value).find('input').attr('disabled') && !$(value).find('input').attr('checked')) {
          $(value).next().find('strong').css('color', '#F00')
        }
      })
  
    }
  }
})(jQuery);
