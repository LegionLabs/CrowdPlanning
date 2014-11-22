/**
 * Created by apple on 10/11/14.
 */

var session = gup('session') ? gup('session') : "DEFAULT_SESSION";
var user = gup('user') ? gup('user') : "DEFAULT_USER";

$(document).ready(function(){


    load_simple();
    load_proccessed();

    $('#add_new').click(function(){
        add_new_action();
    });

    $('#input_add').keypress(function(event){

        if (event.keyCode == 13) {
            add_new_action();
        }

    });

    $(document).on('click','#add_left',function(){

        var d_id = $(this).data('d_id');
        var html = $("#pr_"+d_id).html();
        $("#pr_"+d_id).remove();
        $("#action_library").append(html);

        add_new_action_active(d_id);

    });

    /*
     * drop event working
     */

    $("#current_plan").on("drop", function(event) {
        event.preventDefault();
        event.stopPropagation();

        var inputs = $('#current_plan input');

        var base = $('#base_url').val();

        $(inputs).each(function() {

            var button = '<img class="arroundstyler" src="./images/icon_small-arrow.png" id="helper" />';

            console.log($(this).prop('id'));

            var type = $(this).prop('id');
            var type1 = $(this).next().prop('id');

            if(type1 != 'helper' && type != 'helper' && type1 != ''){
                $(button).insertAfter($(this));
            }

        });

    });

});

function add_new_action_active(id){

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'update_action' , 'value' : id , 'session': session, 'user': user  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '';

                for(var k=0;k<data.msg.length;k++){
                    html += getActionHtmlElem(data.msg[k]['lib_name']);

                }

                $('#action_library').html(html);

            }else{

                alert(data.msg);

            }

        }

    });

}

function load_simple(){

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'load_simple' , 'session': session, 'user': user  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '<ul id="ul_styler">';

                for(var k=0;k<data.msg.length;k++){
                    html +='<li id="pr_'+data.msg[k]['action_id']+'"><span class="btn btn-success" data-d_id="'+data.msg[k]['action_id']+'" id="add_left">Add</span>&nbsp; &nbsp; <span id="txt">'+data.msg[k]['action_name']+'</span><br> </li>';

                }

                html +='</ul>';

                $('#right-bottom').html(html);
                $('#input_add').val('');

            }else{

                alert(data.msg);

            }

        }

    });


}

function load_proccessed(){

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'load_proccessed' , 'session': session, 'user': user  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '<ul id="ul_styler">';

                for(var k=0;k<data.msg.length;k++){
                    //html +='<input type="button" class="alert alert-success" value="'+data.msg[k]['action_name']+'" />';
                    html += getActionHtmlElem(data.msg[k]['lib_name']);

                }

                html +='</ul>';

                $('#action_library').html(html);
                $('#input_add').val('');

            }else{

                alert(data.msg);

            }

        }

    });

}

function add_new_action(){

    var suggested_value = $('#input_add').val();

    if(suggested_value == ''){
        alert('Please provide some input');
        return false;
    }

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'save_action' , 'value' : suggested_value , 'session': session, 'user': user  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '<ul id="ul_styler">';

                for(var k=0;k<data.msg.length;k++){
                   html +='<li id="pr_'+data.msg[k]['action_id']+'"><span class="btn btn-success" data-d_id="'+data.msg[k]['action_id']+'" id="add_left">Add</span>&nbsp; &nbsp; <span id="txt">'+data.msg[k]['action_name']+'</span><br> </li>';

                }

                html +='</ul>';

                $('#right-bottom').html(html);
                $('#input_add').val('');

            }else{

                alert(data.msg);

            }

        }

    });

}


// Create new action button string
function getActionHtmlElem(nameStr, type) {
    //return '<input type="button" id="' + nameStr + '_lib-btn' + '" class="action alert alert-success" draggable="true" ondragstart="drag(event)" value="' + nameStr + '" />';
    return '<input type="button" id="' + nameStr + '_lib-btn' + '" class="action" draggable="true" ondragstart="drag(event)" value="' + nameStr + '" />';
}

//original function starts

//function add_new_action(){
//
//    var suggested_value = $('#input_add').val();
//
//    if(suggested_value == ''){
//        alert('Please provide some input');
//        return false;
//    }
//
//    $.ajax({
//        url: "ajax_requests.php",
//        type: "POST",
//        data : { 'action' : 'save_action' , 'value' : suggested_value , 'session' :gup('session')  },
//        context: document.body,
//        cache: false,
//        async: false,
//        dataType: "json",
//        success: function(data) {
//
//            if(!data.error){
//
//                var html = '';
//
//                for(var k=0;k<data.msg.length;k++){
//                    html +='<input type="button" style="margin:5px;" class="btn btn-info" value="'+data.msg[k]['action_name']+'"  /> ';
//                }
//
//                $('#action_library').html(html);
//                $('#input_add').val('');
//
//            }else{
//
//                alert(data.msg);
//
//            }
//
//        }
//
//    });
//
//}

// original function  end
