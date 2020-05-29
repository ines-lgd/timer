$(document).ready(function () {


    $("#add_user").on("click", function (e) {
        e.preventDefault();
        let url = "/get_user/" + $("#team_users").val();
        //$("#loader").show();
        $.ajax({
            type: "GET",
            url: url,
            success : function success(user) {
                console.log(user);
            }
        })
    });
})