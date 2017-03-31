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


function openDeleteDialog(dialogId, title, deleteRoute, deleteButton) {
    return $(dialogId).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        title: title,
        buttons: {
            "Delete": function () {
                $("#loading").show();
                $(this).dialog("close");

                $.ajax({
                    url: deleteRoute,
                    type: "DELETE",
                    contentType: "application/x-www-form-urlencoded",
                    success: function (data) {
                        try {
                            console.log("Result:");
                            console.log(data);

                            var success = data['success'];
                            if (success) {
                                deleteButton.addClass('disabled');
                                deleteButton.prev().addClass('disabled');
                            }
                            else {
                                $(dialogId).html('<p>' + data['message'] + '</p>');

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
                        }
                        catch (ex) {
                            $(dialogId).html('<p>' + ex.message + '</p>');

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
                    error: function (data) {
                        console.log('Error');
                        console.log(data);
                        $("#loading").hide();

                        $(dialogId).html("<p>Could not delete it. (" + data["status"] + ")</p>");

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
