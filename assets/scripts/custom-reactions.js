(function ($) {
  $(document).ready(function () {
    //Handle the click events for the reaction buttons
    $('.custom-reaction').on('click', function () {
      var $this = $(this);
      var reaction_type = $this.data('reaction-type');
      var count = $this.data('count');
      var allClass = $('.custom-reaction');
      var nonce = reacto_reactions.nonce;

      if ($this.hasClass('clicked')) {
        // Remove the clicked class and update the reaction label
        $this.removeClass('clicked');
        var firstLetter = $this
          .attr('data-reaction-type')
          .charAt(0)
          .toUpperCase();
        var join_remaining_letters =
          firstLetter + '' + $this.attr('data-reaction-type').slice(1);

        if (count > 1) {
          $this.data('count', count - 1);
          $this.find('.reaction-label').text($this.data('count') + ' Vote(s)');
        } else {
          $this.data('count', 0);
          $this.find('.reaction-label').text(join_remaining_letters);
        }
      } else if (allClass.hasClass('clicked')) {
        allClass.each(function () {
          if ($(this).hasClass('clicked')) {
            $(this).removeClass('clicked');
            let previousCount = parseInt($(this).data('count'));
            let firstLetter = $(this)
              .attr('data-reaction-type')
              .charAt(0)
              .toUpperCase();
            let join_remaining_letters =
              firstLetter + '' + $(this).attr('data-reaction-type').slice(1);

            if (previousCount > 1) {
              $(this).data('count', previousCount - 1);
              $(this)
                .find('.reaction-label')
                .text($(this).data('count') + ' Vote(s)');
            } else {
              $(this).data('count', 0);
              $(this).find('.reaction-label').text(join_remaining_letters);
            }
          }
          $this.addClass('clicked');
          $this.data('count', count + 1);
          $this.find('.reaction-label').text($this.data('count') + ' Vote(s)');
        });
      } else {
        $this.addClass('clicked');
        $this.data('count', count + 1);
        $this.find('.reaction-label').text($this.data('count') + ' Vote(s)');
      }

      $.ajax({
        url: reacto_reactions.ajax_url,
        type: 'POST',
        data: {
          action: 'reacto_submit_custom_reaction',
          security: nonce,
          reaction_type: reaction_type,
          current_user: reacto_reactions,
        },
        success: function (response) {
          if (response.success) {
            $this.data('count', ++count);
            $this.find('.reaction-label').text(count + ' Vote(s)');
          } else {
            console.log(response);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(textStatus + ': ' + errorThrown);
        },
      });
    });
  });
})(jQuery);
