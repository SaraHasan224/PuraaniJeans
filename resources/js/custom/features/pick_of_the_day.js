App.PickOfTheDay = {
    listing: 1,
    selectedProducts: [],
    countIds: [],

    clearProductSelection: function () {
        App.PickOfTheDay.selectedProducts = [];
        if ($(".cbbox_all_prod").is(':checked')) {
            $(".cbbox_all_prod").click();
        }
    },

    removeSelectionFilters: function(){
        $("#store").val('').trigger('change');
        $("#in_stock").val('').trigger('change');
        $("#product_status").val('').trigger('change');
        $("#daterange").val('');
        App.Helpers.oTable.draw();
    },

    initializeValidations: function () {
        $("#search-form").validate();
    },

    initializeDataTable: function(){
        let table_name = "pim_products";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getPickOfTheDayProducts);

        const columns = [
            {data: 'show', name: 'show', orderable: false, searchable: false},
            {data: 'check', name: 'check', orderable: false, searchable: false},
            {data: 'sequence', name: 'sequence', className: 'reorder', orderable: false, searchable: false},
            {data: "position", name: "position" , orderable: true, searchable: true},
            {data: "pick_of_the_day_id", name: "pick_of_the_day_id", orderable: true, searchable: true},
            {data: "image", name: "image", orderable: false, searchable: false},
            {data: "name", name: "name", orderable: true, searchable: true},
            {data: "price", name: "price", orderable: true, searchable: true},
            {data: "sku", name: "sku", orderable: true, searchable: true},
            {data: "store", name: "store", orderable: true, searchable: true},
            {data: "imported_id", name: "imported_id", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: false},
            {data: "status", name: "status", orderable: true, searchable: false},
            {data: "in_stock", name: "in_stock", orderable: true, searchable: false},
        ];

        let postData = function (d) {
            d.id = $("#id").val();
            d.merchant_company = $("#merchant_company").val();
            d.name = $("#name").val();
            d.sku = $("#sku").val();
            d.price = $("#price").val();
            d.store = $("#store").val();
            d.in_stock = $("#in_stock").val();
            d.status = $("#product_status").val();
            d.created_at = $("#daterange").val();
        };
        let orderColumn = [[3, "asc"]];
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], false, true, 10, 1, true);
    },

    initializeReOrder: function(){
        var myArray = [];
        let reOrderRowUrl = App.Helpers.generateApiURL(App.Constants.endPoints.setPickOfTheDayProductsSequence);
        App.Helpers.oTable.on( 'row-reorder', function ( e, diff, edit ) {
            let filterData = {
                id                  : $("#search-form #id").val(),
                name                : $("#search-form #name").val(),
                sku                 : $("#search-form #sku").val(),
                price               : $("#search-form #price").val(),
                store               : $("#search-form #store").val(),
                in_stock            : $("#search-form #in_stock").val(),
                status              : $("#search-form #product_status").val(),
                created_at          : $("#search-form #daterange").val(),
            }
            for ( var i = 0 , ien = diff.length ; i < ien ; i++ ) {
                var rowData = App.Helpers.oTable.row( diff[i].node ).data();
                myArray.push({
                    id       : rowData.id,    // record id from datatable
                    position : diff[i].newData
                });
            }
            var requestData = {
                '_token'      : App.Constants.CSRF_TOKEN,
                'positions'   : myArray,
                'filters'     : filterData
            }

            let onSuccess = function (response) {
                App.Helpers.refreshDataTable();
            }
            let onFail= function (response) {
                diff = null;
            }
            App.Ajax.post(reOrderRowUrl, requestData, onSuccess, onFail, {}, 0);
        });
    },

    saveProduct: function(form_id){
        const form = $("#" + form_id);
        var product_id = $("#product :selected").val();

        if (form.valid()) {

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.savePickOfTheDayProduct);
            let onSuccess = function(){
                App.Helpers.refreshDataTable();
                $("#merchant_store").val('').change();
                $("#merchant_store option:selected").prop("selected", false);
                $("#product").find('option').not(':first').remove();
                $(".modal-product .close-model").click();
            };

            const requestData = {
                'product_id' : product_id,
            };
            App.Ajax.post(url, requestData, onSuccess, false, '', 0);
        }
    },

    bulkUpdateProducts: function (method_action) {
        App.PickOfTheDay.countIds = [];

        $.each($("input[name='data_raw_id[]']:checked"), function () {
            App.PickOfTheDay.countIds.push($(this).val());
        });

        let record_count = 'products';

        if (App.PickOfTheDay.countIds.length == 1) {
            record_count = 'product';
        }

        if (App.PickOfTheDay.countIds.length == 0) {
            App.Helpers.selectRowsFirst("Please select at least one product");
        } else {
            let action = function (isConfirm) {
                if (isConfirm) {

                    if ($(".cbbox_all_prod").is(':checked')) {
                        $(".cbbox_all_prod").click();
                    }

                    let url = App.Helpers.generateApiURL(App.Constants.endPoints.updatePickOfTheDayProducts);

                    let requestData = {"product_ids": App.PickOfTheDay.countIds, "action": method_action};

                    let success = function (response) {
                        App.Helpers.refreshDataTable();
                        $("#merchant_store").trigger('change');
                        $("#merchant_store option:selected").prop("selected", false);
                    };
                    App.Ajax.post(url, requestData, success, false, {});
                }
            };
            let action_to_be_taken = 'delete';
            if (method_action == App.Constants.ON) {
                action_to_be_taken = 'active';
            } else if (method_action == App.Constants.OFF) {
                action_to_be_taken = 'inactive';
            }
            App.Helpers.confirm('You want to ' + action_to_be_taken + ' selected ' + record_count + '.', action);

        }
    },

    closeModal: function () {
        var formFilled = false;

        if ($("#merchant_store").val() !== '') {
            formFilled = true;
        }

        if (formFilled) {
            let text = "Your product is not saved, do you want to continue closing the screen?";
            let action = function (isConfirm) {
                if (isConfirm) {
                    $('#set_pick_of_the_day')[0].reset();
                    $(".modal-product").modal('hide');
                    $("#merchant_store").trigger('change');
                    $("#merchant_store option:selected").prop("selected", false);
                    $(".cancel").click();
                }
            }
            App.Helpers.confirm(text, action);
        } else {
            $(".modal-product").modal('hide');
        }
    },

    getNonPickOfTheDayProductsByStore: function(store_id, product){
        const BShopStoreId = App.Constants.BShopStoreId;
        if(store_id > 0){
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getNonPickOfTheDayProducts);
            url += store_id;
            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown(product, response, 'Select Product', BShopStoreId);
            };
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    editProduct: function(productID, storeID, status){
        $('.update-product').find('#product_id').val(productID);
        $('.update-product').find('#merchant_store').val(storeID);
        $('.update-product').find('#status').prop('checked', status);
        $('#merchant_store').select2();
        $('.update-product').modal('show');
    },

    updateProduct: function(method_action){
        App.PickOfTheDay.countIds = [];

        var status    = +$('#status').is(':checked');
        var productID = $('.update-product').find('#product_id').val();

        if(method_action == undefined){
            method_action = status;
        }

        let action = function (isConfirm) {
            if (isConfirm) {

                App.PickOfTheDay.countIds.push(productID);

                let url = App.Helpers.generateApiURL(App.Constants.endPoints.updatePickOfTheDayProducts);

                let requestData = {"product_ids": App.PickOfTheDay.countIds, "action": method_action};

                let success = function (response) {
                    App.Helpers.refreshDataTable();
                    $('.update-product').modal('hide');
                };
                App.Ajax.post(url, requestData, success, false, {});
            }
        };
        let action_to_be_taken = 'delete';
        if (method_action == App.Constants.ON) {
            action_to_be_taken = 'active';
        } else if (method_action == App.Constants.OFF) {
            action_to_be_taken = 'inactive';
        }
        App.Helpers.confirm('You want to ' + action_to_be_taken + ' selected product.', action);
    },

    selectByStore: function(){
        $('.select-by-store').find('#merchant_store').val('').change();
        $('.select-by-store').find('#product').val('').change();
    },

    importCsv: function () {
        const form = $("#import_products_form");

        if (form.valid()) {
            const productForm = document.getElementById("import_products_form");
            let requestData = new FormData(productForm);

            let onSuccess = function () {
                App.PickOfTheDay.closeUploadModal()
                App.Helpers.refreshDataTable();
            };

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.importPickOfTheDayProducts);

            if( App.Helpers.uploadedFile){
                requestData.append('import_file', App.Helpers.uploadedFile)
            }
            App.Ajax.post(url, requestData, onSuccess, false, 'upload_file', 0);
        }
    },

    closeUploadModal: function () {
        $('#import_products_form')[0].reset();
        $('.upload-file-btn').attr('disabled', true);
        $(".import-products").modal('hide');
        $('.fileUploadMessages').html('')
        App.Helpers.uploadedFile = null;
    },

    exportProducts: function () {

        var exportType = $('input[name="export_type"]:checked').val();

        if(exportType == 'all_products'){
            App.PickOfTheDay.selectedProducts = [];
        }
        else if(exportType == 'current_page'){
            App.PickOfTheDay.selectedProducts = [];
            $('.pod-product-id').each(function() {
                var currentRow = $(this);
                App.PickOfTheDay.selectedProducts.push(currentRow.data('id'));
            });
        }
        else if(exportType == 'selected_products'){
            App.PickOfTheDay.selectedProducts = [];
            $('.theClass').each(function() {
                if ($(this).is(":checked")) {
                    App.PickOfTheDay.selectedProducts.push($(this).val());
                }
            });

            if(App.PickOfTheDay.selectedProducts == ''){
                App.Helpers.showErrorMessage({'error': 'Please select at least one product'});
                return false;
            }
        }

        let id = $("#id").val();
        let name = $("#name").val();
        let sku = $("#sku").val();
        let price = $("#price").val();
        let store = $("#store").val();
        let in_stock = $("#in_stock").val();
        let status = $("#product_status").val();
        let created_at = $("#daterange").val();

        let selected_products = JSON.stringify(App.PickOfTheDay.selectedProducts);

        let query_string = '?id=' + id + '&name=' + name + '&sku=' + sku  + '&price=' + price
            + '&store=' + store + '&created_at=' + created_at + '&in_stock=' + in_stock + '&status=' + status +
            "&selected_products=" + selected_products;
        window.open(
            '' + App.Constants.endPoints.exportPickOfTheDayProducts + query_string,
            '_blank'
        );

        $('.export-pod-products').modal('hide');
    },


}