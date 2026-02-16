(function($){

  if ($.fn.slick) {
      Fancybox.bind('[data-fancybox="coach-gallery"]', {
          Thumbs: false,
          Toolbar: {
            display: [
              "close",
            ],
          },
        });
}
    if ($.fn.slick) {

      $('.slider-calendar').slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          autoplay: false,
          autoplaySpeed: 2000,
          arrows: true,
          prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>',
          nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>',
        });
    }
  document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('locationSearch');
    var cards = document.querySelectorAll('.marker-card');

    // Stop script if search input or cards do not exist
    if (!searchInput || cards.length === 0) return;

    searchInput.addEventListener('input', function () {
        var searchValue = this.value.toLowerCase();

        cards.forEach(function (card) {
            var titleEl = card.querySelector('.card-title');
            var textEl = card.querySelector('.card-text');

            // Extra safety check
            if (!titleEl || !textEl) return;

            var title = titleEl.textContent.toLowerCase();
            var text = textEl.textContent.toLowerCase();

            var wrapper = card.closest('.wrapper-area');
            if (!wrapper) return;

            if (title.includes(searchValue) || text.includes(searchValue)) {
                wrapper.style.display = '';
            } else {
                wrapper.style.display = 'none';
            }
        });
    });
});



    // Open modal

    // Open modal
    $(document).on('click', '.open-booking-modal', function () {
jQuery('.open-booking-modal').length

        $('#modal-booking-id').val($(this).data('id'));
        $('#modal-name').val($(this).data('name'));
        $('#modal-email').val($(this).data('email'));
        $('#modal-date').val($(this).data('date'));
        $('#modal-start').val($(this).data('start'));
        $('#modal-end').val($(this).data('end'));
        $('#modal-amount').val($(this).data('amount'));
        $('#modal-status').val($(this).data('status'));

        $('#bookingModal').fadeIn();
    });

    // Close modal
    $(document).on('click', '#close-modal', function () {
        $('#bookingModal').fadeOut();
    });

    // Update booking
    $(document).on('click', '#update-booking', function () {

        var booking_id = $('#modal-booking-id').val();
        var status = $('#modal-status').val();

        $.post(booking_ajax.ajax_url, {
            action: 'update_booking_status',
            booking_id: booking_id,
            status: status,
            nonce: booking_ajax.nonce
        }, function (response) {

            if (response.success) {

                // Close modal
                $('#bookingModal').fadeOut();

                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Booking status updated successfully.'
                }).then(() => {
                    location.reload();
                });

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data || 'Update failed.'
                });

            }
        });

    });




})(jQuery);
