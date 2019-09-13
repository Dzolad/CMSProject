tinymce.init({ selector: 'textarea' });

$(document).ready(function () {        
    $('#select_all_boxes').click(function (event) {
        if(this.checked) {
            $('.check_boxes').each(function () {
                this.checked = true;
            });
        } else {
            $('.check_boxes').each(function () {
                this.checked = false;
            });
        }
    });                     
});
//    var div_box = "<div id='load-screen'><div id='loading'></div></div>";
//    $("body").prepend(div_box);
//
//    $('#load-screen').delay(700).fadeOut(600, function(){
//        $(this).remove();
//    });

function load_users_online() {
    $.get("admin_functions.php?onlineusers=result", function(data){ 
        $(".usersonline").text(data);
    });
}

setInterval(function(){
    load_users_online();
}, 500);

