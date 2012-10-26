$(document).ready(function(){
    $('.album').livequery(function(){
        $(this).hover(function(){
         //$(this).children('.cover_photos').children('.cover_photo').css('position', 'static');
        var photos = $(this).children('.cover_photos').children('.cover_photo');
        photos.each(function(index, element){
            
             $(this).animate({
                position: 'static',
                top: 0,
                left: 0
               });
        });
    }, function(){
         //$(this).children('.cover_photos').children('.cover_photo').css('position', 'relative');
         /*
         $(this).children('.cover_photos').children('.cover_photo').animate({
             position: 'relative',
             top: $(this).css('top'),
             left: $(this).css('left')
                });
         */

        var photos = $(this).children('.cover_photos').children('.cover_photo');
        photos.each(function(index){
            switch(index)
            {
            case 0:
              var top = 25;
              var left = 0;
            break;
            case 1:
               var top = 5;
               var left = -25;
            break;
            case 2:
              var top = 5;
              var left = 25;
            break;
            case 3:
                var top = -25;
                var left = 5;
            break;
            }
            $(this).animate({
             position: 'relative',
             top: top,
             left: left
             //top: $(this).css('top'),
             //left: $(this).css('left')
                });
        });
        });
    });

    $('.multi').livequery(function(){
        $(this).MultiFile();
    });


});


