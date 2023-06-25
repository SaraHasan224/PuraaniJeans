App.Users = {
    canResendPassword: 0,
    isMerchant: 0,

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
        $('#user_id').val('');
        $('#role').val('');
        $('#email').val('');
        $('#filter_phone').val('');
        $('#daterange').val('');
        App.Helpers.removeAllfilters();
    },

    removeSelectionFilters: function () {
        $('#role').val('');
        $('#daterange').val('');
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
            d.phone = $('#filter_phone').val();
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

    isGlobalRole:function(){
        var roleName       = $( '[name=roles] option:selected').val();
        var isGlobal       = $('[name=roles] option:selected').attr('is_global');
        var merchantStores = $("#merchant_stores");
        var checkBoxToggle = $("#permission_to_all_stores_checkbox");
        var field          = $("#has_permission_to_all_stores");
        var userExist      = $("[name=id]").val(); //in edit case

        if(isGlobal == 1 && roleName == App.Constants.user_type[2]){
            checkBoxToggle.prop('checked',true);
            $("#has_permission_to_all_stores").val(1);
            $("#merchant_stores > option").prop("selected",true);
            merchantStores.trigger("change");

            setTimeout(function(){
                $("span.select2-selection__clear").css("display", "none");
            },100);

            // $("#merchant_stores :selected").map(function(i, el) {
            //    $('<input type="hidden" class="is_global_select" name="merchant_stores[]" value="'+$(el).val()+'">').insertAfter(field);
            // }).get();

        }else{
            checkBoxToggle.prop('checked',false);
            checkBoxToggle.prop('disabled',false);
            $("#has_permission_to_all_stores").val(0);
            $("#merchant_stores > option").prop("selected",false);
            merchantStores.trigger("change");
            merchantStores.removeAttr('disabled');
            $('.is_global_select').remove();
        }
    },

    permissionToAllStoreCheckbox:function(element){
        if ($(element).is(':checked')) {
            $("#has_permission_to_all_stores").val(1);
            $("#merchant_stores > option").prop("selected",true);
            $("#merchant_stores").trigger("change");

        }else{
            $("#has_permission_to_all_stores").val(0);
            $("#merchant_stores > option").prop("selected",false);
            $("#merchant_stores").trigger("change");
        }
    },

    merchantStoreSelection:function(element){
        var total_length = $(element).children('option').length;
        var get_selected_length = $('#merchant_stores :selected').length;
        if(get_selected_length < total_length){
            $("#permission_to_all_stores_checkbox").prop('checked',false);
            $("#has_permission_to_all_stores").val(0);
        }else{
            $("#permission_to_all_stores_checkbox").prop('checked',true);
            $("#has_permission_to_all_stores").val(1);
        }
    },

    viewStoreModal: function(){
        $(".view-stores").modal('hide');
    },

    viewStoresInModal: function (element) {
        var storeNames = $(element).attr('store');
        var data       = storeNames.split(',');
        var initialDiv = $("#viewStoreSection");

        initialDiv.empty();

        $.each(data, function (index, item) {
            initialDiv.append('<label class="badge badge-success mt-2">'+item+'</label>');
        });
    }

}
