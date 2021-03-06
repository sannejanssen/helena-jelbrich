(function($) {

    // Slideshow behavior
  Drupal.behaviors.slider = {
    attach: function(context) {

      $("#slider").responsiveSlides({
        auto: false,
        pager: false,
        nav: true,
        speed: 500,
      });
    }
  };

  // Responsive menu
  Drupal.behaviors.responsivemenu = {
    attach: function(context) {

      // NAVIGATION MENU
      var $navigation = $('nav ul.menu');

      // Add extra menu item
      $navigation.find('li.first').before('<li id="navigationtoggle"><a href="#"></a></li>');

      // verberg niet actieve elementen
      $navigation.find('li:not(#navigationtoggle)').hide();

      // disable first element
      $navigation.find('#navigationtoggle a').click(function(e) {
        e.preventDefault();
      });

      // slideToggle
      $navigation.find('#navigationtoggle').click(function() {
        $navigation.find("li:not(#navigationtoggle)").slideToggle();
      });
    }
  };

  // Set default value for mailchimp input form
  Drupal.behaviors.formselect = {
    attach: function(context) {

      $("#mailchimp-newsletter-nieuwsbrief .form-type-textfield").each(function() {
        var label = $(this).find("label");
        label.find("span").remove();

        var labeltext = "Email";

        if( ($.trim(label.text()) ) == 'Name'  ) {
          labeltext = "Naam";
        }

        var input = $(this).find("input");
        input.val('');
        input.attr('placeholder', labeltext);

      });

      $('.form-item-mailchimp-lists-mailchimp-nieuwsbrief-mergevars-FNAME').after('<span id="subscribe-tooltip">Klik op de pijl om te bevestigen</span>');
      $('.mailchimp input#edit-submit').val('Inschrijven');
    }
  };

  // Add extra classes to album covers
  Drupal.behaviors.albumcovers = {
    attach: function(context) {

      var albums = $('.collection .album').length;

      var count = 0;

      $('.collection .album').each(function() {
        count += 1;

        if( count % 2 == 0) {
          $(this).addClass('second');
        }

        if( count % 3 == 0) {
          $(this).addClass('third');
        }

        if( count % 4 == 0) {
          $(this).addClass('fourth');
        }
      });
    }
  };

    // Add extra classes to album covers
  Drupal.behaviors.webshopcovers = {
    attach: function(context) {

      var albums = $('.webshop .album').length;

      var count = 0;

      $('.webshop .album').each(function() {
        count += 1;

        if( count % 2 == 0) {
          $(this).addClass('second');
        }

        if( count % 3 == 0) {
          $(this).addClass('third');
        }

        if( count % 4 == 0) {
          $(this).addClass('fourth');
        }
      });
    }
  };
})(jQuery);