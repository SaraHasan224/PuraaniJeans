App.Closet = {
    countIds: [],
    initializeValidations: function () {
        $("#search-form").validate();
    },
    removeFilters: function (id) {
        $("#closet_reference").val("");
        $("#closet_name").val("");
        App.Helpers.removeAllfilters(id);
    },
    removeSelectionFilters: function () {
        $("#customer").val("");
        $("#closet_name").val('').trigger('change');
        App.Helpers.oTable.draw();
    },
    closetProductViewModal: function (element, id) {
        var data = $(element).attr('text');
        var modelTitle = $(element).attr('title');
        var modelSubmitBtnTheme = $(element).attr('submitTheme');
        var modelSubmitBtnText = $(element).attr('submitText');

        let url = App.Helpers.generateApiURL(
            App.Constants.endPoints.getClosetsProductDetail+id
        );
        $("#customModalWrapperLabel").html(modelTitle + " Details");

        let onSuccess= function (data) {
            var initialDiv = $("#customModalWrapper div.modal-dialog div.modal-content div.modal-body");
            initialDiv.empty();
            initialDiv.append(data);
        };
        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, "ignoreSuccessFormatting");

        var footerSubmitDiv = $("#customModalWrapper").children().children().children('.modal-footer').children('#customModalWrapperSubmitBtn');
        footerSubmitDiv.empty();
        footerSubmitDiv.append(modelSubmitBtnText);
        footerSubmitDiv.removeClass("btn-primary");
        footerSubmitDiv.addClass(modelSubmitBtnTheme);
    },
    initializeDataTable: function () {
        let table_name = "closet_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getClosets);
        let sortColumn = [[2, "desc"]];
        let columns = [
            {data: 'show', name: 'show', orderable: false, searchable: false, className: 'show'},
            // {data: "id", name: "id", orderable: true, searchable: true},
            {data: "closet_name", name: "closet_name", orderable: true, searchable: true},
            // {data: "customer_name", name: "customer_name", orderable: true, searchable: true},
            {data: "closet_reference", name: "closet_reference", orderable: true, searchable: true},
            {data: "logo", name: "logo", orderable: true, searchable: true},
            {data: "banner", name: "banner", orderable: true, searchable: true},
            {data: "status", name: "status", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: true},
            {data: "updated_at", name: "updated_at", orderable: true, searchable: true}
        ];
        let postData = function (d) {
            d.closet_name = $("#closet_name").val();
            d.closet_reference = $("#closet_reference").val();
        };

        let orderColumn = sortColumn;
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], true);
    },
    initializeClosetProductsDataTable: function (ref) {
        let table_name = "closet_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getClosetsProducts)+ref;
        let sortColumn = [[2, "desc"]];
        let columns = [
            {data: 'check', name: 'check', orderable: false, searchable: false, className: 'show'},
            {data: "name", name: "name", orderable: true, searchable: true},
            {data: "bs_category", name: "bs_category", orderable: true, searchable: false},
            {data: "category_name", name: "category_name", orderable: true, searchable: false},
            {data: "price", name: "price", orderable: true, searchable: true},
            {data: "discounted_price", name: "discounted_price", orderable: true, searchable: true},
            {data: "quantity", name: "quantity", orderable: true, searchable: true},
            {data: "image", name: "image", orderable: true, searchable: false},
            {data: "shipping_price", name: "shipping_price", orderable: true, searchable: true},
            {data: "status", name: "status", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: true},
            {data: "updated_at", name: "updated_at", orderable: true, searchable: true}
        ];
        let postData = function (d) {
            // d.customer = $("#customer").val();
            // d.closet_name = $("#closet_name").val();
        };

        let orderColumn = sortColumn;
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], true);
    },

    initializeClosetCustomerDataTable: function (ref) {
        let table_name = "closet_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getClosetsProducts)+ref;
        let sortColumn = [[2, "desc"]];
        let columns = [
            {data: 'check', name: 'check', orderable: false, searchable: false, className: 'show'},
            {data: "name", name: "name", orderable: true, searchable: true},
            {data: "bs_category", name: "bs_category", orderable: true, searchable: false},
            {data: "category_name", name: "category_name", orderable: true, searchable: false},
            {data: "price", name: "price", orderable: true, searchable: true},
            {data: "discounted_price", name: "discounted_price", orderable: true, searchable: true},
            {data: "quantity", name: "quantity", orderable: true, searchable: true},
            {data: "image", name: "image", orderable: true, searchable: false},
            {data: "shipping_price", name: "shipping_price", orderable: true, searchable: true},
            {data: "status", name: "status", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: true},
            {data: "updated_at", name: "updated_at", orderable: true, searchable: true}
        ];
        let postData = function (d) {
            // d.customer = $("#customer").val();
            // d.closet_name = $("#closet_name").val();
        };

        let orderColumn = sortColumn;
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], true);
    },

    initializeClosetOrdersDataTable: function (ref) {
        let table_name = "closet_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getClosetsProducts)+ref;
        let sortColumn = [[2, "desc"]];
        let columns = [
            {data: 'check', name: 'check', orderable: false, searchable: false, className: 'show'},
            {data: "name", name: "name", orderable: true, searchable: true},
            {data: "bs_category", name: "bs_category", orderable: true, searchable: false},
            {data: "category_name", name: "category_name", orderable: true, searchable: false},
            {data: "price", name: "price", orderable: true, searchable: true},
            {data: "discounted_price", name: "discounted_price", orderable: true, searchable: true},
            {data: "quantity", name: "quantity", orderable: true, searchable: true},
            {data: "image", name: "image", orderable: true, searchable: false},
            {data: "shipping_price", name: "shipping_price", orderable: true, searchable: true},
            {data: "status", name: "status", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: true},
            {data: "updated_at", name: "updated_at", orderable: true, searchable: true}
        ];
        let postData = function (d) {
            // d.customer = $("#customer").val();
            // d.closet_name = $("#closet_name").val();
        };

        let orderColumn = sortColumn;
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], true);
    },
    editCustomerFormBinding: function (userId) {
        $("#customer-user").bind("click", function (e) {
            if ($("#closet_edit_form").valid()) {
                let url = App.Helpers.generateApiURL(
                    App.Constants.endPoints.editCustomer+"/"+userId
                );
                let onSuccess= function () {
                    if(data.type == "success") {
                        window.location.href = '/closets';
                        App.Helpers.showSuccessMessage( data.message );
                    }
                };
                let requestData = $("#customer_edit_form").serialize();
                App.Ajax.post(url, requestData, onSuccess, false, {});
            }
        });
    },
    updateCustomerStatus: function (thisKey, customerId) {
        var customerStatusValue = $(thisKey).find(':selected').text();
        let action = function (isConfirm) {
            if (isConfirm) {
                var customerStatus = $(thisKey).val();
                let onSuccess = function (response) {

                };
                let requestData = {'customer_status': customerStatus, 'customer_id': customerId};
                let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateCustomerStatus);
                App.Ajax.post(url, requestData, onSuccess, false, '', 0);
            }
        };

        App.Helpers.confirm('You want to mark selected customer as ' + customerStatusValue.toLowerCase() + '.', action);

    },
};
