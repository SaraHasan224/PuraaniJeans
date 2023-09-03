App.Users = {
    initializeValidations: function () {
        $("#user_edit_form").validate();
        $("#user_create_form").validate();
    },

    clearUserSelection: function () {
        if ($(".allUsers").is(':checked')) {
            $(".allUsers").click();
        }
    },

    removeFilters: function () {
        $('#name').val('');
        $('#email').val('');
        $('#user_id').val('');
        $('#phone').val('');
        App.Helpers.removeAllfilters("users_table");
    },

    removeSelectionFilters: function () {
        App.Helpers.oTable.draw();
    },

    initializeDataTable: function () {
        let table_name = "users_table";
        var current_url;
        current_url = $("#currentUrl").val();
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.usersList);
        let columns = [
            {data: 'check', name: 'check', orderable: false, searchable: false},
            {data: 'name', name: 'name', orderable: true, searchable: true},
            {data: 'email', name: 'email', orderable: true, searchable: true},
            {data: 'phone', name: 'phone', orderable: true, searchable: true},
            {data: 'user_type', name: 'user_type', orderable: true, searchable: true},
            {data: 'last_login', name: 'last_login', orderable: true, searchable: true},
            {data: 'status', name: 'status', orderable: true, searchable: false},
            {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
            {data: 'updated_at', name: 'updated_at', orderable: true, searchable: false},
        ];

        let postData = function (d) {
            d.user_id = $('#user_id').val();
            d.name = $('#name').val();
            d.email = $('#email').val();
            d.phone = $('#phone').val();
        };
        let orderColumn = [[2, "desc"]];
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn , [], false, true, 10, 1);
    },

    initializeBulkDelete: function () {
        var countIds = [];
        $.each($("input[name='data_raw_id[]']:checked"), function () {
            countIds.push($(this).val());
        });
        let user_count = 'users';
        if (countIds.length == 1) {
            user_count = 'user';
        }
        let text = 'You want to delete selected ' + user_count + '.';
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.bulkDeleteUsers);
        App.Helpers.bulkRecordsDelete(text, url);
    },

    createUserFormBinding: function () {
        $("#create-user").bind("click", function (e) {
            if ($("#user_create_form").valid()) {
                let url = App.Helpers.generateApiURL(
                    App.Constants.endPoints.createUser
                );

                let onSuccess= function (data) {
                    console.log("success: ", data)
                    if(data.type == "success") {
                        window.location.href = '/users';
                        App.Helpers.showSuccessMessage( data.message );
                    }
                }
                let requestData = $("#user_create_form").serialize();
                App.Ajax.post(url, requestData, onSuccess, false, {});
            }
        });
    },

    changeStatus: function (id, status) {
        swal({
                title: "Are you sure to continue?",
                text: "You are changing user status.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Continue",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    let url = App.Helpers.generateApiURL(App.Constants.endPoints.changeUserStatus);
                    let onSuccess = function (data) {
                        console.log("success: ", data)
                        if (data.type == "success") {
                            App.Helpers.refreshDataTable();
                        }
                    }
                    let requestData = {id, status};
                    App.Ajax.post(url, requestData, onSuccess, false, {});
                }else{
                }
            });
    },

    editUserFormBinding: function (userId) {
        $("#edit-user").bind("click", function (e) {
            if ($("#user_edit_form").valid()) {
                let url = App.Helpers.generateApiURL(
                    App.Constants.endPoints.editUser+"/"+userId
                );
                let onSuccess= function () {
                    if(data.type == "success") {
                        window.location.href = '/users';
                        App.Helpers.showSuccessMessage( data.message );
                    }
                }
                let requestData = $("#user_edit_form").serialize();
                App.Ajax.post(url, requestData, onSuccess, false, {});
            }
        });
    },
}
