jQuery(function() {
  var element = $('.wpac-loop');
  if(!element.length) {
    return;
  }
  var post_type = element.data('post_type');
  var items = element.parent().find('.' + post_type);
  items.appendTo(element);
});
