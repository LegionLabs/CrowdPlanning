/**
 * Created by apple on 10/11/14.
 */

$(document).ready(function(){

    $('#add_new').click(function(){
        add_new_action();
    });

    $('#input_add').keypress(function(event){

        if (event.keyCode == 13) {
            add_new_action();
        }

    });

});

function add_new_action(){

    var suggested_value = $('#input_add').val();

    if(suggested_value == ''){
        alert('Please provide some input');
        return false;
    }

    $.ajax({
        url: "ajax_requests.php",
        type: "POST",
        data : { 'action' : 'save_action' , 'value' : suggested_value  },
        context: document.body,
        cache: false,
        async: false,
        dataType: "json",
        success: function(data) {

            if(!data.error){

                var html = '';

                for(var k=0;k<data.msg.length;k++){
                    html +='<input type="button" style="margin:5px;" class="btn btn-info" value="'+data.msg[k]['action_name']+'"  />';
                }

                $('#action_library').html(html);
                $('#input_add').val('');

            }else{

                alert(data.msg);

            }

        }

    });

}