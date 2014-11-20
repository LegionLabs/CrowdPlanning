/**
 * Created by apple on 10/11/14.
 */

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

});

function add_new_action_active(id){

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'update_action' , 'value' : id , 'session' :gup('session')  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '';

                for(var k=0;k<data.msg.length;k++){
                    html +='<input type="button" class="alert alert-success" value="'+data.msg[k]['action_name']+'" />';

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
        data : { 'action' : 'load_simple' , 'session' :gup('session')  },
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
        data : { 'action' : 'load_proccessed' , 'session' :gup('session')  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '<ul id="ul_styler">';

                for(var k=0;k<data.msg.length;k++){
                    html +='<input type="button" class="alert alert-success" value="'+data.msg[k]['action_name']+'" />';

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
        data : { 'action' : 'save_action' , 'value' : suggested_value , 'session' :gup('session')  },
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
