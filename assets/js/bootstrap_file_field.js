import $ from 'jquery';

$(function () {
  $('.custom-file').each(function () {
    const $container = $(this);

    $container.on('change', '.custom-file-input', function (event) {
      debugger;
      $container.find('.custom-file-label').html(event.currentTarget.files[0].name);
    });
  });
});