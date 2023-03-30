(function ($) {
  $(document).ready(function () {
    //Handle the click events for the reaction buttons
    $('.custom-reaction').on('click', function () {
      var $this = $(this);
      var reaction_type = $this.data('reaction-type');
      var reaction_count = $this.data('data');
      var allClass = $('.custom-reaction');
      var nonce = reacto_reactions.nonce;

      if ($this.hasClass('clicked')) {
        $this.removeClass('clicked');
        let firstLetter = $this
          .attr('data-reaction-type')
          .charAt(0)
          .toUpperCase();
        let join_remaining_letters =
          firstLetter + '' + $this.attr('data-reaction-type').slice(1);
        $this.find('.reaction-label').text(join_remaining_letters);
        console.log($this.attr('data-reaction-type'));
      } else if (allClass.hasClass('clicked')) {
        allClass.each(function (index) {
          if ($(this).hasClass('clicked')) {
            $(this).removeClass('clicked');
            let firstLetter = $(this)
              .attr('data-reaction-type')
              .charAt(0)
              .toUpperCase();
            let join_remaining_letters =
              firstLetter + '' + $(this).attr('data-reaction-type').slice(1);
            $(this).find('.reaction-label').text(join_remaining_letters);
          }
          $this.addClass('clicked');
          $this.find('.reaction-label').text('1 Vote(s)');
        });
      } else {
        $this.addClass('clicked');
        $this.find('.reaction-label').text('1 Vote(s)');
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
          // $this.data('count', ++count);
          // $this.find('.reaction-label').text(count + ' Vote(s)');
        },
      });
    });

    $('.reaction-count').each(function () {
      var count = $(this).data('reaction-count');
      $(this).text(count + ' Votes');
    });
  });
})(jQuery);
