(function($){
Fancybox.bind('[data-fancybox="coach-gallery"]', {
  Thumbs: false,
  Toolbar: {
    display: [
      "close",
    ],
  },
});

$('.slider-calendar').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>',
  });
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('locationSearch');
    var cards = document.querySelectorAll('.marker-card');

    searchInput.addEventListener('input', function () {
        var searchValue = this.value.toLowerCase();

        cards.forEach(function (card) {
            var title = card.querySelector('.card-title').textContent.toLowerCase();
            var text = card.querySelector('.card-text').textContent.toLowerCase();

            if (title.includes(searchValue) || text.includes(searchValue)) {
                card.closest('.wrapper-area').style.display = '';
            } else {
                card.closest('.wrapper-area').style.display = 'none';
            }
        });
    });
});
})(jQuery);
