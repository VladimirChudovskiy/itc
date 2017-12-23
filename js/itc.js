jQuery( document ).ready(function($) {
    $('.stars-wrap .star').hover(
        function(){
            var parent_obj = $(this).parent();
            var selected_index = $(this).index();
            drawLinks( parent_obj, selected_index );

        },
        function(){
            drawLinks( $(this).parent(), ($(this).parent().data('average')-1) );

        }
    );

    $('.stars-wrap .star').click(function() {
        var parent_obj = $(this).parent();
        var star_data = {
            user_vote: $(this).index() + 1,
            entity_type: parent_obj.data('entity-type'),
            entity_id: parent_obj.data('entity-id'),
            field_name: parent_obj.data('field-name')
        };

         $.ajax({
             url: '/itg-vote-do',
             method: "GET",
             data: star_data,
             success: function (data) {
                 drawLinks( parent_obj, data );
             }
         });
        drawLinks( parent_obj, 0 );
    });

    function drawLinks( parent_obj, selected_index ){
        parent_obj.find('.star').removeClass('hover');
        parent_obj.find('.star').each(function(){
            if ( $(this).index() <= selected_index ){
                $(this).addClass('hover');
            }
        });
    }
});