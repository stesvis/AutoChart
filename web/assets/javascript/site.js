$(document).ready(function () {
    // $('#menu-content li').click(function () {
    $('#menu-content li').each(function (index) {
        if ($(this).hasClass('active')) {
            $(this).next().addClass('in');
        }
    });
    //
    //     $(this).addClass('active');
    // });
});


function openDeleteDialog(dialogId, deleteRoute, deleteButton) {
    return $(dialogId).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Delete": function () {
                $("#loading").show();
                $(this).dialog("close");

                $.ajax({
                    url: deleteRoute,
                    type: "DELETE",
                    contentType: "application/x-www-form-urlencoded",
                    success: function (data) {
                        console.log("Result:");
                        console.log(data);

                        var success = data['success'];
                        if (success)
                            deleteButton.addClass('disabled');
                        else {
                            $(dialogId).find("p").html(data['message']);

                            $(dialogId).dialog({
                                resizable: false,
                                height: "auto",
                                width: 400,
                                modal: true,
                                buttons: {
                                    "Close": function () {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                        }

                        $("#loading").hide();
                    }
                    ,
                    error: function (msg) {
                        console.log('Error');
                        console.log(msg);
                        $("#loading").hide();

                        $(dialogId).find("p").html("Could not delete it. (" + msg["status"] + ")");

                        $(dialogId).dialog({
                            resizable: false,
                            height: "auto",
                            width: 400,
                            modal: true,
                            buttons: {
                                "Close": function () {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }
                });
            },
            "Cancel": function () {
                $(this).dialog("close");
            }
        }
    });
}


