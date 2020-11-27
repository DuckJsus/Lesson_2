$().ready(function(){
    $(function(){
        $('#slides').slides({
            preload: false,
            generateNextPrev: false,
            autoHeight: true,
            play: 4000,
            effect: 'fade'
        });
    });
});