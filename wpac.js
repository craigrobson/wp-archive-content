jQuery(function() {
  var wpac = jQuery('#wpac-wrapper'),
      button = wpac.find('button[type="button"]'),
      editor_holder = wpac.find('#wpac-editor');

  // Move the wpac wrapper
  wpac.insertBefore('.wp-header-end');
  // When the button is clicked
  button.on('click', function(event) {
    event.preventDefault();
    editor_holder.toggleClass('hidden');
  })

})
