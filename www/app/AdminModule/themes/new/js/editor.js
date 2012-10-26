(function($) {
  $.fn.editor = function() {
    return $(this).each(function() {
        var txt = $(this);
        var html = txt.val();
        txt.hide();
        var class = 'editor';
        txt.parent().append( $('<iframe></iframe>').addClass(class) );
        var editor = $('iframe.'+class);
        alert(editor.html);
        editor.html(html);
    });
  }
})(jQuery);