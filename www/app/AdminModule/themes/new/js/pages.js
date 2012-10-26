$(document).ready(function(){
	//category change
	$('.category-change').livequery('change', (function(){
		var page = $(this).attr('data-id');
		var new_cat = $(this).val();
		$.post('?do=changeCategory',{page : page, new_cat: new_cat});
	}));
    
    $('.template-change').livequery('change', (function(){
        var page = $(this).attr('data-id');
        var template = $(this).val();
        $.post('?do=changeTemplate',{page : page, template: template});
    }));
	
	//publish change
	$('.chboxPublished').live('click', function(){
		var page = $(this).attr('data-id');
		var published = $(this).attr('checked');
		$.post('?do=changePublished',{page : page, published: published});
	});
	
	//timepicker
	$('#frmformNewPage-publish_time, #frmformEditPage-publish_time').livequery(function(){
		$(this).datepicker({dateFormat: 'dd.mm.yy'});
	});
    
	$('#frmnew-time, #frmedit-time').livequery(function(){
	
    });
    
    $('#frmformNewPage-content, #frmformEditPage-content').livequery(function(){
        $(this).htmlarea();
        var area = $(this);
        $('div.jHtmlArea').mouseleave(function(){
            area.htmlarea('updateTextArea');
           area.htmlarea('updateHtmlArea'); 
        });
    });
    $('#frmformEditPage-description, #frmformNewPage-description').livequery(function(){
        $(this).css('height', '20px');
        $(this).elastic();
    });
    $('#frmformPreviewToolbar-btnClose').live('click', function(){Shadowbox.close()});
    /*
    $('#frmformEditPage-content, #frmformNewPage-content').livequery(function(){
        $(this).htmlarea();    
    });
     */

    $('#frmformEditPage-css_files, #frmformNewPage-css_files, #frmformEditPage-js_files, #frmformNewPage-js_files').live('click', function(){
     $(this).elastic();
     var ext = $(this).attr('data-ext');
     var offset = $('#pageEditor').offset();
     var element = $(this);
     $('#file_browser').css({
         left: $('#pageEditor').width() + offset.left,
         top: offset.top,
         visibility: 'visible'
     });
     $('#file_browser h3').html(ext.toUpperCase() + ' browser');
     $('#file_browser').draggable({ cursor: 'pointer' });
     $('#file_browser .browser').fileTree(
     {
       root: '',
       script: '?do=browseFiles&ext=' + ext,
       multiFolder: false
        }
        ,
     function(file) {
       element.val( element.val() + ' ' + file );
    });
    });

   

    //SHADOWBOX    
	Shadowbox.init({
	    skipSetup: true,
		players: ["html"],
		modal: false
	});
    
});
