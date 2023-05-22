App.Customer = {
    isAdmin: 0,
    countIds: [],

  initializeValidations: function () {
        $("#search-form").validate();
    },

    exportCustomers: function () {
        let id = $("#id").val();
        let name = $("#name").val();
        let email = $("#email").val();
        let phone = $("#phone").val();
        let member_since = $("#daterange").val();
        let merchant_name = $("#merchant_name").val();
        let status = $("#status").val();
        let city = $("#city").val();
        let verification = $("#verification").val();

        let query_string = '?id=' + id + '&name=' + name + '&email=' + email + '&phone=' + phone + '&merchant_name=' + merchant_name + '&status=' + status + '&city=' + city + '&verification=' + verification;
        window.open(
            '' + App.Constants.endPoints.exportCustomers + query_string,
            '_blank'
        );
    },

    removeFilters: function () {
        $("#id").val("");
        $("#name").val("");
        $("#email").val("");
        $("#phone").val("");
        $("#daterange").val("");
        $("#merchant_name").val('').trigger('change')
        $("#store").val('').trigger('change')
        $("#customer_type").val('').trigger('change')
        $("#origin_source").val("").trigger('change');
        $("#status").val("");
        $("#city").val("");
        $("#verification").val("");
        App.Helpers.removeAllfilters();
    },

    removeSelectionFilters: function(){
        $("#daterange").val("");
        $("#merchant_name").val('').trigger('change');
        $("#store").val('').trigger('change');
        $("#customer_type").val('').trigger('change');
        $("#status").val("");
        $("#verification").val("");
        $("#origin_source").val("").trigger('change');
        App.Helpers.oTable.draw();
    },

    initializeDataTable: function () {
        let table_name = "customers_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getCustomers);
        let columns = [];
        let sortColumn;
        if (this.isAdmin == 1) {
            sortColumn = [[2, "desc"]];
            columns = [
                {data: 'show', name: 'show', orderable: false, searchable: false, className: 'show'},
                {data: 'check', name: 'check', orderable: false, searchable: false},
                {data: "row_id", name: "row_id", orderable: true, searchable: true},
                {data: "name", name: "name", orderable: true, searchable: true},
                {data: "city", name: "city", orderable: true, searchable: true},
                {data: "email", name: "email", orderable: true, searchable: true},
                {data: "phone", name: "phone", orderable: true, searchable: true},
                {data: "member_since", name: "member_since", orderable: true, searchable: true},
                {data: "status", name: "status", orderable: true, searchable: true},
                {data: "verification", name: "verification", orderable: true, searchable: true},
                {data: "customer_type", name: "customer_type", orderable: true, searchable: true},
                {data: "origin_source", name: "origin_source", orderable: true, searchable: true},
            ];
        } else {
            sortColumn = [[1, "desc"]];
            columns = [
                {data: 'show', name: 'show', orderable: false, searchable: false, className: 'show'},
                {data: "row_id", name: "row_id", orderable: true, searchable: true},
                {data: "name", name: "name", orderable: true, searchable: true},
                {data: "city", name: "city", orderable: true, searchable: true},
                {data: "email", name: "email", orderable: true, searchable: true},
                {data: "phone", name: "phone", orderable: true, searchable: true},
                {data: "member_since", name: "member_since", orderable: true, searchable: true},
                {data: "status", name: "status", orderable: true, searchable: true},
                {data: "verification", name: "verification", orderable: true, searchable: true},
                {data: "origin_source", name: "origin_source", orderable: true, searchable: true},
            ];
        }
        let postData = function (d) {
            d.id = $("#id").val();
            d.name = $("#name").val();
            d.email = $("#email").val();
            d.phone = $("#phone").val();
            d.member_since = $("#daterange").val();
            d.merchant_name = $("#merchant_name").val();
            d.role = $("#role").val();
            d.store = $("#store").val();
            d.status = $("#status").val();
            d.city = $("#city").val();
            d.verification = $("#verification").val();
            d.customer_type = $("#customer_type").val();
            d.origin_source = $("#origin_source").val();
        };

        let orderColumn = sortColumn;
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], false);
    },

    removeEnvironment: function () {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.setEnvironment);

        let onSuccess = function (response) {
            let obj = document.getElementById('merchant_name');
            App.Helpers.getAppsByMerchantId(obj, '{{$defaultEnvironment}}')
        };
        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    markAsTesting: function (method_action) {
        App.Customer.countIds = [];
        $.each($("input[name='data_raw_id[]']:checked"), function () {
          App.Customer.countIds.push($(this).val());
        });

        let record_count = 'customers';

        if (App.Customer.countIds.length == 1) {
          record_count = 'customer';
        }

        if (App.Customer.countIds.length == 0) {
          App.Helpers.selectRowsFirst("Please select at least one customer");
        } else {
          let action = function (isConfirm) {
            if (isConfirm) {

              if ($(".cbbox_all_prod").is(':checked')) {
                $(".cbbox_all_prod").click();
              }

              let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateCustomers);

              let requestData = {"customer_ids": App.Customer.countIds, "type": method_action};

              let success = function (response) {
                App.Helpers.refreshDataTable();
              };
              App.Ajax.post(url, requestData, success, false, {});
            }
          };
          let action_to_be_taken = '';
          if (method_action == 1) {
            action_to_be_taken = 'mark as live';
          } else if (method_action == 2) {
            action_to_be_taken = 'mark as testing';
          }
          App.Helpers.confirm('You want to ' + action_to_be_taken + ' selected ' + record_count + '.', action);

        }
  },

  updateCustomerStatus: function (thisKey, customerId) {
    var customerStatusValue = $(thisKey).find(':selected').text();
    let action = function (isConfirm) {
      if (isConfirm) {
        var customerStatus = $(thisKey).val();
        let onSuccess     = function (response) {

        };
        let requestData = {'customer_status' : customerStatus, 'customer_id' :customerId}
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateCustomerStatus);
        App.Ajax.post(url, requestData, onSuccess, false, '', 0);
      }
    };

    App.Helpers.confirm('You want to mark selected customer as ' + customerStatusValue.toLowerCase() + '.', action);

  },
};
