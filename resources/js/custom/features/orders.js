App.Order = {
    isAdmin: 0,
    isInvoiceOrder: 0,
    allowReload: 0,
    selectedOrders: [],
    tagOrders: [],
    itemIds: [],
    productQty: [],
    deletedItemIds: [],
    orderId: null,
    refundOrderId: 0,
    refundAction: '',
    discountApplied:0,

    initializeInternationalShippingGoogleMap: function ( country_code = App.Constants.defaultCountryNameCode , countryCodesGiven = false) {
        $('#form_create_order_add').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        const input    = document.getElementById("customer_address");
        const geocoder = new google.maps.Geocoder;

        const fieldKey = input.id.replace("customer_", "");
        const isEdit   = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

        const latitude  = parseFloat(document.getElementById(fieldKey + "-latitude").value) || App.Constants.default_lat;
        const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || App.Constants.default_long;

        var codes = JSON.parse( $("#store_countries").val() );
        var options = {};
        if(countryCodesGiven){
            options = {
                componentRestrictions: {country: country_code},
                strictBounds: false,
                types: ['geocode', 'establishment'],
            }
        }else{
            options = {
                strictBounds: false,
                types: ['geocode', 'establishment'],
            }
        }
        App.Constants.googleAutoComplete = new google.maps.places.Autocomplete(input, options);
        App.Constants.googleAutoCompleteLsr = google.maps.event.addListener(App.Constants.googleAutoComplete, 'place_changed', function () {
            const place = App.Constants.googleAutoComplete.getPlace();
            if (!place.geometry) return;
            geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    App.Order.getAddressCity(results[0])
                    const lat     = results[0].geometry.location.lat();
                    const lng     = results[0].geometry.location.lng();
                    const address = results[0].formatted_address;
                    App.Order.setLocationCoordinates('address', lat, lng, address);
                    App.Order.selectLocation();
                }
            });

            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                input.value = "";
                return;
            }

        });
    },

    initializeValidations: function () {
        $("#search-form").validate();

        $(".allOrders").click(function () {
            if ($(this).is(":checked")) {
                $(".theClass").not(":disabled").each(function () {
                    App.Order.tagOrders.push($(this).val());
                });
            } else {
                App.Order.tagOrders = [];
            }
        });

        $(document).on("click", ".theClass", function () {
            if ($(this).is(":checked")) {
                App.Order.tagOrders.push($(this).val());
            } else {
                App.Order.tagOrders.splice($.inArray($(this).val(), App.Order.tagOrders), 1);
            }

        });
    },

    exportOrders: function () {
        let store = $("#store").val();
        let tags = $("#tags").val();
        let placement_type = $("#placement_type").val();
        let merchant_name = $("#merchant_name").val();
        let customer_name = $("#customer_name").val();
        let customer_email = $("#customer_email").val();
        let customer_phone_number = $("#customer_phone_number").val();
        let order_id = $("#order_id").val();
        let order_reference_number = $("#order_reference_number").val();
        let order_status = $("#order_status").val();
        let payment_status = $("#payment_status").val();
        let payment_gateway = $("#payment_gateway").val();
        let fulfillment_status = $("#fulfillment_status").val();
        let date_range = $("#daterange").val();
        let export_type = $("#export_type").val();
        let is_abandoned_checkout = $("#is_abandoned_checkout").val();
        let recovery_status = $("#recovery_status").val();
        let edited_orders = $("#edited_orders").val();
        let sms_status = $("#sms_status").val();
        let is_invoice_order = App.Order.isInvoiceOrder;
        let selected_orders = JSON.stringify(App.Order.selectedOrders);
        let query_string = '?merchant_name=' + merchant_name + '&customer_name=' + customer_name + '&customer_email=' + customer_email + '&customer_phone_number=' + customer_phone_number + '&order_id=' + order_id + '&order_reference_number=' + order_reference_number + '&order_status=' + order_status + '&payment_status=' + payment_status +'&payment_gateway=' + payment_gateway +'&fulfillment_status=' + fulfillment_status + '&date_range=' + date_range + '&placement_type=' + placement_type + '&export_type=' + export_type+ '&store=' + store + "&selected_orders=" + selected_orders + "&tags=" + tags+ "&recovery_status=" + recovery_status+ "&edited_orders=" + edited_orders+ "&is_abandoned_checkout=" + is_abandoned_checkout+ "&sms_status=" + sms_status +"&is_invoice_order=" + is_invoice_order;
        window.open(
            '' + App.Constants.endPoints.exportOrders + query_string,
            '_blank'
        );

    },

    removeFilters: function () {
        $("#placement_type").val("");
        $("#customer_id").val("");
        $("#merchant_id").val("");
        $("#merchant_name").val('').trigger('change');
        $("#customer_name").val("");
        $("#customer_email").val("");
        $("#customer_phone_number").val("");
        $("#order_id").val("");
        $("#merchant_order_id").val("");
        $("#order_reference_number").val("");
        $("#order_status").val("");
        $("#payment_status").val("");
        $("#payment_gateway").val("");
        $("#fulfillment_status").val("");
        $("#daterange").val("");
        $("#store").val("").trigger('change');
        $("#tags").val("").trigger('change');
        $("#recovery_status").val("").trigger('change');
        $("#edited_orders").val("").trigger('change');
        $("#sms_status").val("").trigger('change');
        App.Helpers.removeAllfilters();
    },

    removeSelectionFilters: function(){
        $("#merchant_name").val('').trigger('change');
        $("#recovery_status").val('').trigger('change');
        $("#edited_orders").val('').trigger('change');
        $("#sms_status").val('').trigger('change');
        $("#daterange").val("");
        $("#store").val("").trigger('change');
        $("#tags").val("").trigger('change');
        $("#order_status").val("");
        $("#payment_status").val("");
        $("#checkout_type").val("");
        $("#gateway_checkout_type").val("");
        $("#payment_gateway").val("");
        $("#fulfillment_status").val("");
        $(".expandFilterWrap #placement_type").val("");
        App.Helpers.oTable.draw();
    },

    initializeDataTable: function () {
        let table_name = "orders_table";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getOrders);
        let columns = [];
        if(App.Order.isInvoiceOrder == 1){
            // Invoice Listing with additional columns
            if (this.isAdmin == 1) {
                console.log('invoice listing 1');
                columns = [
                    {data: "show", name: "show", orderable: false, searchable: false},
                    {data: "check", name: "check", orderable: false, searchable: false},
                    {data: "row_id", name: "row_id", orderable: true, searchable: true},
                    {data: "customer_id", name: "customer_id", orderable: true, searchable: true},
                    {data: "store", name: "store", orderable: true, searchable: true},
                    {data: "created_at", name: "created_at", orderable: true, searchable: true},
                    {data: "merchant_order_id", name: "merchant_order_id", orderable: true, searchable: true},
                    {data: "placement_status", name: "placement_status", orderable: true, searchable: true},
                    {data: "comment", name: "comment", orderable: true, searchable: true},
                    {data: "tags", name: "tags", orderable: true, searchable: true},
                    {data: "payment_method", name: "payment_method", orderable: true, searchable: true},
                    {data: "payment_gateway", name: "payment_gateway", orderable: true, searchable: true},
                    {data: "merchant_name", name: "merchant_name", orderable: true, searchable: true},
                    {data: "customer_name", name: "customer_name", orderable: true, searchable: true},
                    {data: "customer_phone_number", name: "customer_phone_number", orderable: true, searchable: true},
                    {data: "total_amount", name: "total_amount", orderable: true, searchable: true},
                    {data: "discount", name: "discount", orderable: true, searchable: true},
                    {data: "order_amount", name: "order_amount", orderable: true, searchable: true},
                    {data: "placement_type", name: "placement_type", orderable: false, searchable: true},
                    {data: "ref_no", name: "ref_no", orderable: true, searchable: true},
                    {data: "customer_city", name: "customer_city", orderable: true, searchable: true},
                    {data: "payment_status", name: "payment_status", orderable: true, searchable: true},
                    {data: "bsecure_fulfillment_status", name: "bsecure_fulfillment_status", orderable: true, searchable: true},
                    {data: "checkout_type", name: "checkout_type", orderable: true, searchable: true},
                    {data: "gt_checkout_type", name: "gt_checkout_type", orderable: true, searchable: true},
                    {data: "environment", name: "environment", orderable: true, searchable: true},
                    {data: "recovery_status", name: "recovery_status", orderable: true, searchable: true},
                ];
            } else {
                columns = [
                    {data: "show", name: "show", orderable: false, searchable: false},
                    {data: "check", name: "check", orderable: false, searchable: false},
                    {data: "row_id", name: "row_id", orderable: true, searchable: true},
                    {data: "customer_id", name: "customer_id", orderable: true, searchable: true},
                    {data: "store", name: "store", orderable: true, searchable: true},
                    {data: "created_at", name: "created_at", orderable: true, searchable: true},
                    {data: "merchant_order_id", name: "merchant_order_id", orderable: true, searchable: true},
                    {data: "placement_status", name: "placement_status", orderable: true, searchable: true},
                    {data: "comment", name: "comment", orderable: true, searchable: true},
                    {data: "tags", name: "tags", orderable: true, searchable: true},
                    {data: "payment_method", name: "payment_method", orderable: true, searchable: true},
                    {data: "payment_gateway", name: "payment_gateway", orderable: true, searchable: true},
                    {data: "customer_name", name: "customer_name", orderable: true, searchable: true},
                    {data: "customer_phone_number", name: "customer_phone_number", orderable: true, searchable: true},
                    {data: "discount", name: "discount", orderable: true, searchable: true},
                    {data: "order_amount", name: "order_amount", orderable: true, searchable: true},
                    {data: "placement_type", name: "placement_type", orderable: false, searchable: true},
                    {data: "ref_no", name: "ref_no", orderable: true, searchable: true},
                    {data: "customer_city", name: "customer_city", orderable: true, searchable: true},
                    {data: "payment_status", name: "payment_status", orderable: true, searchable: true},
                    {data: "bsecure_fulfillment_status", name: "bsecure_fulfillment_status", orderable: true, searchable: true},
                    {data: "checkout_type", name: "checkout_type", orderable: true, searchable: true},
                    {data: "gt_checkout_type", name: "gt_checkout_type", orderable: true, searchable: true},
                    {data: "environment", name: "environment", orderable: true, searchable: true},
                    {data: "recovery_status", name: "recovery_status", orderable: true, searchable: true},
                    {data: "actions", name: "actions", orderable: true, searchable: true},
                ];
            }
        }else{
            // Order Listing
            if (this.isAdmin == 1) {
                columns = [
                    {data: "show", name: "show", orderable: false, searchable: false},
                    {data: "check", name: "check", orderable: false, searchable: false},
                    {data: "row_id", name: "row_id", orderable: true, searchable: true},
                    {data: "customer_id", name: "customer_id", orderable: true, searchable: true},
                    {data: "store", name: "store", orderable: true, searchable: true},
                    {data: "created_at", name: "created_at", orderable: true, searchable: true},
                    {data: "merchant_order_id", name: "merchant_order_id", orderable: true, searchable: true},
                    {data: "placement_status", name: "placement_status", orderable: true, searchable: true},
                    {data: "tags", name: "tags", orderable: true, searchable: true},
                    {data: "payment_method", name: "payment_method", orderable: true, searchable: true},
                    {data: "payment_gateway", name: "payment_gateway", orderable: true, searchable: true},
                    {data: "merchant_name", name: "merchant_name", orderable: true, searchable: true},
                    {data: "customer_name", name: "customer_name", orderable: true, searchable: true},
                    {data: "customer_phone_number", name: "customer_phone_number", orderable: true, searchable: true},
                    {data: "total_amount", name: "total_amount", orderable: true, searchable: true},
                    {data: "discount", name: "discount", orderable: true, searchable: true},
                    {data: "order_amount", name: "order_amount", orderable: true, searchable: true},
                    {data: "placement_type", name: "placement_type", orderable: false, searchable: true},
                    {data: "ref_no", name: "ref_no", orderable: true, searchable: true},
                    {data: "customer_city", name: "customer_city", orderable: true, searchable: true},
                    {data: "payment_status", name: "payment_status", orderable: true, searchable: true},
                    {data: "bsecure_fulfillment_status", name: "bsecure_fulfillment_status", orderable: true, searchable: true},
                    {data: "checkout_type", name: "checkout_type", orderable: true, searchable: true},
                    {data: "gt_checkout_type", name: "gt_checkout_type", orderable: true, searchable: true},
                    {data: "environment", name: "environment", orderable: true, searchable: true},
                    {data: "recovery_status", name: "recovery_status", orderable: true, searchable: true},
                ];
            } else {
                columns = [
                    {data: "show", name: "show", orderable: false, searchable: false},
                    {data: "check", name: "check", orderable: false, searchable: false},
                    {data: "row_id", name: "row_id", orderable: true, searchable: true},
                    {data: "customer_id", name: "customer_id", orderable: true, searchable: true},
                    {data: "store", name: "store", orderable: true, searchable: true},
                    {data: "created_at", name: "created_at", orderable: true, searchable: true},
                    {data: "merchant_order_id", name: "merchant_order_id", orderable: true, searchable: true},
                    {data: "placement_status", name: "placement_status", orderable: true, searchable: true},
                    {data: "tags", name: "tags", orderable: true, searchable: true},
                    {data: "payment_method", name: "payment_method", orderable: true, searchable: true},
                    {data: "payment_gateway", name: "payment_gateway", orderable: true, searchable: true},
                    {data: "customer_name", name: "customer_name", orderable: true, searchable: true},
                    {data: "customer_phone_number", name: "customer_phone_number", orderable: true, searchable: true},
                    {data: "discount", name: "discount", orderable: true, searchable: true},
                    {data: "order_amount", name: "order_amount", orderable: true, searchable: true},
                    {data: "placement_type", name: "placement_type", orderable: false, searchable: true},
                    {data: "ref_no", name: "ref_no", orderable: true, searchable: true},
                    {data: "customer_city", name: "customer_city", orderable: true, searchable: true},
                    {data: "payment_status", name: "payment_status", orderable: true, searchable: true},
                    {data: "bsecure_fulfillment_status", name: "bsecure_fulfillment_status", orderable: true, searchable: true},
                    {data: "checkout_type", name: "checkout_type", orderable: true, searchable: true},
                    {data: "gt_checkout_type", name: "gt_checkout_type", orderable: true, searchable: true},
                    {data: "environment", name: "environment", orderable: true, searchable: true},
                    {data: "recovery_status", name: "recovery_status", orderable: true, searchable: true},
                    {data: "actions", name: "actions", orderable: true, searchable: true},
                ];
            }
        }


        let postData = function (d) {
            d.merchant_id = $("#merchant_id").val();
            d.customer_id = $("#customer_id").val();
            d.merchant_name = $("#merchant_name").val();
            d.customer_name = $("#customer_name").val();
            d.customer_email = $("#customer_email").val();
            d.customer_phone_number = $("#customer_phone_number").val();
            d.store_slug = $("#store_slug").val();
            d.order_id = $("#order_id").val();
            d.merchant_order_id = $("#merchant_order_id").val();
            d.order_reference_number = $("#order_reference_number").val();
            d.order_status = $("#order_status").val();
            d.placement_type = $("#placement_type").val();
            d.payment_status = $("#payment_status").val();
            d.payment_gateway = $("#payment_gateway").val();
            d.fulfillment_status = $("#fulfillment_status").val();
            d.checkout_type = $("#checkout_type").val();
            d.gateway_checkout_type = $("#gateway_checkout_type").val();
            d.date_range = $("#daterange").val();
            d.store = $("#store").val();
            d.tags = $("#tags").val();
            d.recovery_status = $("#recovery_status").val();
            d.edited_orders = $("#edited_orders").val();
            d.is_invoice_order = App.Order.isInvoiceOrder;
        };

        let orderColumn = [[2, "desc"]];
        let searchEnabled = true;
        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], true, true, 10, 1);
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

    changeFlaggedOrderStatus: function (order_id, previous_order_status, merchant_id) {
        let text = "You want to change the status?";

        let action = function (isConfirm) {
            if (isConfirm) {
                let status = $("#verification_order_status").val();
                let status_label = $("#verification_order_status option:selected").text();
                let onSuccess = function (response) {
                    $('#order-id').removeClass('text-danger');
                    $('#orderStatusWrapper').html('<span>' + status_label + '</span>');
                };

                let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateOrderVerificationStatus);
                let requestData = {id: order_id, placement_status: status, merchant_id: merchant_id};

                App.Ajax.post(url, requestData, onSuccess, false, {});
            } else {
                $('#verification_order_status').val(previous_order_status)
            }
        };
        App.Helpers.confirm(text, action);
    },

    cloneOrder: function (url) {
        window.location.href = url;
    },

    orderLogs: function (url) {
        window.location.href = url;
    },

    showOrderAccountingsModal: function () {
        $(document).on("click", '.get-order-accountings', function (e) {
            let orderId = $(this).attr("data-order-id");

            let onSuccess = function (response) {
                $("#order_accountings_modal_wrapper").html(response);
                $('.order-accountings-modal').modal('show');
            };

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getOrderAccountings);
            url += '/' + orderId;

            let requestData = {};
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);

        });
    },

    getMerchantStores: function (merchant_id) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getAllStoresByMerchant);
        let requestData = {merchant_id: merchant_id};
        let onSuccess = function (response) {
            App.Helpers.fileDropdown('store', response, 'Select store', '');
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    saveOrderTags: function (listing) {
        let form = $('#form_merchant_order_tags');

        if (form.valid()) {

            if (listing != undefined && App.Order.tagOrders.length <= 0) {
                swal("Error!", 'Please select orders to apply tags!', "warning");
                return false;
            }

            let selected_orders = '';
            selected_orders = JSON.stringify(App.Order.tagOrders);

            const onSuccess = function (selected_orders) {
                if (App.Order.tagOrders.length > 0) {
                    $('.apply-tags-modal').modal('hide');
                    App.Helpers.refreshDataTable();
                    $(".allOrders").prop('checked', false);
                    $(".cbbox_tags").prop('checked', false);
                    App.Order.tagOrders = [];
                }
            };

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.saveOrderTags);
            let requestData = form.serialize();

            // if ($.trim(requestData) == '' || $.trim(requestData) == null || $.trim(requestData) == undefined || ($('#form_merchant_order_tags #tags').val() && $('#form_merchant_order_tags #tags').val().length <= 0)) {
            //     swal("Error!", 'Please select tag(s) to apply!', "warning");
            //     return false;
            // }

            if (App.Order.tagOrders.length > 0) {
                requestData += '&selected_orders=' + selected_orders;
            }
            App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
        }
    },


    initializeOrderSelect: function (order_id) {
        const success = function (response) {
            let orderTags = response.orderTags;
            orderTags = orderTags.map(function (tag) {
                return tag.tag_id;
            });
            $('#tags').val(orderTags).trigger('change');
        };
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getOrderTags);
        let requestData = {'order_id': order_id};
        App.Ajax.get(url, requestData, success, null, {}, 0);
    },

    removeSelectedOrders: function () {
      App.Order.selectedOrders = [];
    },

    getCollapseData: function (el, collapseId, orderRef, orderId) {
    if ($(`#${collapseId}`).hasClass('show')) {
      $(el).attr('aria-expanded',false);
      $(`#${collapseId}`).collapse('hide');
    }
    else {
      const rowCount = $(`#${collapseId} .table tbody tr`).length;
      if (rowCount == 0) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getOrderCollapseData);
        let requestData = {collapseId: collapseId, order_ref: orderRef, orderId: orderId};
        let onSuccess = function (response) {
          if (response.render_html) {
            $(el).attr('aria-expanded',true);
            $(`#${collapseId} .table > tbody:last-child`).append(response.render_html);
            $(`#${collapseId}`).collapse('show')
          }
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
      } else {
        $(el).attr('aria-expanded',true);
        $(`#${collapseId}`).collapse('show')
      }
    }
  },

    getFlaggedShipmentLogs: function (el,collapseId, orderId) {
    if ($(`#${collapseId}`).hasClass('show')) {
      $(el).attr('aria-expanded',false);
      $(`#${collapseId}`).collapse('hide');
    }
    else {
      const rowCount = $(`#${collapseId} .table tbody tr`).length;
      if (rowCount == 0) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getFlaggedShipmentLogs);
        let requestData = {collapseId: collapseId, orderId: orderId};
        let onSuccess = function (response) {
          if (response.render_html) {
            $(el).attr('aria-expanded',true);
            $(`#${collapseId} .table > tbody:last-child`).append(response.render_html);
            $(`#${collapseId}`).collapse('show')
          }
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
      } else {
        $(el).attr('aria-expanded',true);
        $(`#${collapseId}`).collapse('show')
      }
    }
  },

    clearOrderSelection: function () {
    App.Products.selectedOrders = [];
    App.Order.tagOrders = [];
    if ($(".allOrders").is(':checked')) {
      $(".allOrders").click();
    }
  },

    resetFiltersOptions: function(){
      if($(".FilterDropDownMenu").hasClass('show')){
        $(".FilterDropDownMenu").removeClass('show');
        $(".customCheckboxContainer .filterCheckbox").each(function(){
          $(this).prop('checked',false);
          var inputValue = $(this).attr('value');
          if (!this.checked) {
              $("#"+inputValue).val(null).trigger("change");
          }
          $('.'+inputValue).css("display","none");
        });
      }
  },

  filterColumnsSearch: function(){
    inputValue     = $('#filterColumnsSearch').val();
    allColumns_     = ['all','customer_id','customer_name','customer_email','merchant_order_id','customer_phone_number','order_reference_number','order_id'];
    selectedCols   = $("#js-select2").select2('val');

    $("#js-select2 option").each(function(){
        if(selectedCols.indexOf( $(this).val() ) > -1){
            $('#' + $(this).val() ).attr('value',inputValue);
        }else{
            $('#' + $(this).val() ).val('');
        }
    });
    App.Helpers.oTable.draw();
  },

  printOrderDetail:function () {
    $('.orderDetails .collapse').each(function () {
      $(this).addClass('show')
    })
    setTimeout(function (args) {
      var printContents = document.querySelector('.wrapper').innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }, 200)
  },

  sendAbandonedOrderSms:function (orderId) {
      const success = function (response) {
          App.Helpers.refreshDataTable()
      };
    let url = App.Helpers.generateApiURL(App.Constants.endPoints.sendAbandonedOrderSms);
    let requestData = {'order_id': orderId};
    App.Ajax.post(url, requestData, success, null, {}, 0);
  },

    saveReasonForFlaggedShipment: function (formId) {
        const form = $("#" + formId);
        var itemId = $('.customFlaggedRow').find('#orderItemId').val();

        if (form.valid()) {
            let onSuccess = function (response) {
                $('.row-'+itemId).find('.fulfillment-status').text($("#shipmentStatus option:selected").text());
                $('.row-'+itemId).find('.flaggedItemBtn').hide();
                $('.flagged-item-modal').modal('hide');
                $('.logDetailWrap').find('#orderFulfillmentStatus').text(response.orderFulfillmentStatus);
            };
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.saveReasonForFlaggedShipment);
            let requestData = form.serialize()
            App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
        }
    },

    flaggedOrderItem: function (itemId, previousShipmentStatus) {
        $('.customFlaggedRow').find('#orderItemId').val(itemId);
        $('.customFlaggedRow').find('#previousShipmentStatus').val(previousShipmentStatus);
        $('.customFlaggedRow').find("#shipmentStatus").val('').trigger('change');
        $('.customFlaggedRow').find("#blameTo").val('').trigger('change');
        $('.customFlaggedRow').find('#reasonForFlaggedOrder').val('');
    },

    calculateOrderTotal: function(){
        let sub_total = 0;
        let total_discount = 0;

        $("#orderItemsRow tr").each(function () {
            let price = Number($(this).find('.item-price').data('price'));
            let discount = Number($(this).find('.item-discount').data('discount'));
            let qty = Number($(this).find('#product_qty').val());
            if ($.trim(qty) == '' || qty == 0) {
                qty = 1;
            }

            sub_total += (price - discount) * qty;
        });
        let shipping_price = $('.orderInfoBox').find('.shipping_price').text();
        let service_charges = $('.orderInfoBox').find('.service-charges').text();
        let additional_charges = $('.orderInfoBox').find('.additional-charges').text();
        total_discount = $('.orderInfoBox').find(".total-discount").text();

        shipping_price = parseFloat(shipping_price);
        service_charges = parseFloat(service_charges);
        additional_charges = parseFloat(additional_charges);
        let order_grand_total = ( (sub_total + shipping_price + service_charges + additional_charges) - total_discount);

        // $('.orderInfoBox').find(".total-discount").text(total_discount);
        $('.orderInfoBox').find(".sub-total").text(sub_total);
        $('.box-body').find(".grand-total").text(order_grand_total);
    },

    editOrderItems: function () {
      if (App.Order.discountApplied == App.Constants.ON){
        $('.add-order-item').removeClass('d-none');
        $('.save-edit-order-class').removeClass('d-none');
        $('.discount_applied_message').removeClass('d-none');
      }else {
        $('.edit-order-class').removeClass('d-none');
        $('.qty-txt').hide();
      }
    },

    deleteOrderItem: function (thisRow, itemId) {
        var itemCount = $('#orderItemsRow tr').length;
        if(itemCount > 1){
            if(itemId != null){
                App.Order.deletedItemIds.push(itemId);
            }
            $(thisRow).closest('tr').remove();
            App.Order.calculateOrderTotal();
        }else{
            swal("Warning!", 'You cannot delete last order item.', "warning");
        }
    },

    addNewItemBtn: function () {
        $('#addItemForm #name').val('');
        $('#addItemForm #price').val('');
        $('#addItemForm #quantity').val('');
    },

    updatePrice: function (thisKey) {
        var qty   = $(thisKey).closest('#product_qty').val();
        var id    = $(thisKey).closest('tr').data('id');
        var price = $(thisKey).data('price');
        var discount = $(thisKey).closest('tr').find('.item-discount').data('discount');
        if ($.trim(qty) == '' || qty == 0) {
            qty = 1;
        }

        var item_price = qty * price;
        var sale_item_price = (price - discount);
        $(thisKey).closest('tr').find('.qty-txt').text(qty);
        $(thisKey).closest('tr').find('.item-price').text(item_price);
        $(thisKey).closest('tr').find('.item-discount').text(discount * qty);
        $(thisKey).closest('tr').find('.item-sale-price').text(sale_item_price * qty);

        App.Order.calculateOrderTotal();

        if(id != undefined){
            App.Order.itemIds.push(id);
            App.Order.productQty.push(qty);
        }

    },

    saveOrderItem: function (orderId) {
        var productQtyObj = {};
        var newProductObj = {};
        var valid = true;

        $.each(App.Order.itemIds , function(key , val){
            productQtyObj[val] = App.Order.productQty[key];
        });


        $("#orderItemsRow .new-item-row").each(function (i) {
            newProductObj[i] = {
                "product_name"  : $(this).find('.new-item-name').text(),
                "product_price" : $(this).find('.new-item-price').data('price'),
                "product_qty"   : $(this).find('.new-item-qty').val(),
                "product_image" : $(this).find('.new-item-image').prop('src'),
            };
        });


        let requestData = {
            "orderId"        : orderId,
            "productQtyArr"  : JSON.stringify(productQtyObj),
            "newProductObj"  : JSON.stringify(newProductObj),
            "deletedItemIds" : JSON.stringify(App.Order.deletedItemIds),
        };

        $("#orderItemsRow tr").each(function (i) {
            if($(this).find('#product_qty').val() == '' || $(this).find('#product_qty').val() == 0){
                App.Helpers.showErrorMessage({'error': 'Product quantity cannot be empty or zero'});
                valid = false;
            }
        });

        if(valid) {
            let onSuccess = function (response) {
                location.reload();
            };
            let onFailure = function (response) {};

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.saveOrderItem);
            App.Ajax.post(url, requestData, onSuccess, onFailure, {}, 0);
        }
    },

    addOrderItem: function (formId) {
        const form = $("#" + formId);

        if (form.valid()) {
            let onSuccess = function (response) {
                $('.add-item-modal').modal('hide');
                $("#orderItemsRow").append(response.render_html);
                App.Order.calculateOrderTotal();
            };
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.addOrderItem);
            let requestData = form.serialize()
            App.Ajax.post(url, requestData, onSuccess, false, 'hide_message', 0);
        }
    },


    updatePaymentStatus: function (thisKey, orderId) {
        var paymentStatus = $(thisKey).val();
        let onSuccess     = function (response) {
            App.OrderLogs.setOrderLogs(orderId);
        };
        let requestData = {'paymentStatus' : paymentStatus, 'orderId' :orderId}
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateOrderPaymentStatus);
        App.Ajax.post(url, requestData, onSuccess, false, '', 0);
    },

    selectLocation: function () {
        const form       = $('.locationFieldWrap #address-form-type').val();
        const lat        = $('.locationFieldWrap #address-latitude').val();
        const long       = $('.locationFieldWrap #address-longitude').val();
        const address    = $('.locationFieldWrap #address-formatted').val();
        const store_slug = $("#merchant_store").select2().find(":selected").data("store_slug");

        let onSuccess = function (response) {
            $("#customer_address").val(address);
            $('#customer_address').valid();
            $("#customer_address").prop('readOnly',false);
            //$("#address").val(address);

            $("#customer-address-field-wrapper #country").val(response.countryId);
            $("#customer-address-field-wrapper #province").val(response.provinceId);
            $("#customer-address-field-wrapper #city").val(response.cityId);
            $("#customer-address-field-wrapper #area").val(response.areaId);
        };

        let onFailure = function (response) {
            $("#customer_address").val('');
        };

        let url = App.Helpers.generateApiURL(App.Constants.endPoints.locationDetailForInternationalShipment);
        let requestData = {
            latitude   : parseFloat(lat) ,
            longitude  : parseFloat(long),
            store_slug : store_slug
        };
        App.Ajax.post(url, requestData, onSuccess, onFailure, {}, 0);
    },

    openLocationModal: function (form) {
        const lat     = $(`#order_create_form_${form} #lat`).val();
        const long    = $(`#order_create_form_${form} #long`).val();
        const address = $(`#order_create_form_${form} #address`).val();
        $('#form_create_order_add #address-form-type').val(form);
        if (lat && long && address) {
            var latlng = new google.maps.LatLng(lat, long);
            App.Order.setMarkerWithLatLng(latlng)
        }
        else {
            var latlng = new google.maps.LatLng(App.Constants.default_lat, App.Constants.default_long);
            App.Constants.MAP.setCenter(latlng)
            App.Constants.MAP.setZoom(13);
            App.Constants.MARKER.setVisible(false);
        }
    },

    selectCurrentLocation: function () {
        if (navigator.geolocation) {
            // var lat = 24.878458;
            // var lng = 67.06415729999999;
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var latlng = new google.maps.LatLng(lat, lng);
                App.Order.setMarkerWithLatLng(latlng)
            })
        }
    },

    setMarkerWithLatLng: function (latlng) {
        if(latlng){
            const geocoder = new google.maps.Geocoder;
            geocoder.geocode({'location': latlng}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    App.Order.getAddressCity(results[0]);
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    const address = results[0].formatted_address;
                    App.Order.setLocationCoordinates('address', lat, lng, address);
                    App.Constants.MARKER.setPosition(latlng);
                    App.Constants.MAP.setCenter(latlng);
                    App.Constants.MAP.setZoom(17);
                    App.Constants.MARKER.setVisible(true);
                }
            });
        }
    },

    setLocationCoordinates: function (key, lat, lng, address) {
        const latitudeField  = document.getElementById(key + "-" + "latitude");
        const longitudeField = document.getElementById(key + "-" + "longitude");
        const addressField   = document.getElementById(key + '-' + "formatted");
        //const addressSearch  = document.getElementById(key + '-' + "input");
        latitudeField.value  = lat;
        longitudeField.value = lng;
        addressField.value   = address;
        //addressSearch.value  = address;
    },

    getAddressCity: function(address){
        var addressComponents = address.address_components;
        var addressDetails = {
            'city': '',
            'country': '',
        };
        $.each(addressComponents, function( index, component ) {
            const types = component.types
            if(($.inArray("locality", types) != -1) && ($.inArray("political", types) != -1)) {
                addressDetails.city = component.long_name;
                $('.locationFieldWrap #address-city').val(component.long_name)
            }
            if(($.inArray("country", types) != -1) && ($.inArray("political", types) != -1)) {
                addressDetails.country = component.long_name;
                $('.locationFieldWrap #address-country').val(component.long_name)
            }
        });
        return addressDetails;
    },

    checkInternationalDelivery: function(){
        var areaBaseShipment   = $("#merchant_store").select2().find(":selected").data("area_based_shipment");
        var internationalCheck = $("#merchant_store").select2().find(":selected").data("international_delivery");
        var countriesCode = [];
        var countriesCodeCheck = false;
        if( areaBaseShipment == 0 || (areaBaseShipment == 1 && internationalCheck == 1) ){
            //$(".area-based-shipment").css('display','none');
            //$(".international-delivery").css('display','block');

            $(".area-based-shipment").hide();
            App.Order.locationDropdown(true);
            $(".international-delivery").show();
            App.Order.addCustomerAddressFieldsInWrapper('add');
            if(areaBaseShipment == 0){
                App.Order.initializeInternationalShippingGoogleMap( countriesCode , false );
            }else{
                App.Order.initializeInternationalShippingGoogleMap( countriesCode , true );
            }
            $("#is_international_Shiping").val(internationalCheck);
        }else{
            $(".area-based-shipment").show();
            App.Order.locationDropdown(false);
            $(".international-delivery").hide();
            App.Order.addCustomerAddressFieldsInWrapper('remove');
            $("#is_international_Shiping").val(internationalCheck);
            App.Helpers.removeGoogleInstance();
        }

        $("#merchant_store").on("select2:select" , function(e){
            let areaBaseShipment = $(e.params.data.element).data('area_based_shipment');
            let check = $(e.params.data.element).data('international_delivery');

            if( areaBaseShipment == 0 || (areaBaseShipment == 1 && check == 1) ){
                $(".area-based-shipment").hide();
                App.Order.locationDropdown(true);
                $(".international-delivery").show();
                App.Order.addCustomerAddressFieldsInWrapper('add');
                $("#is_international_Shiping").val(check);
            }else{
                $(".area-based-shipment").show();
                App.Order.locationDropdown(false);
                $(".international-delivery").hide();
                App.Order.addCustomerAddressFieldsInWrapper('remove');
                $("#is_international_Shiping").val(check);
                App.Helpers.removeGoogleInstance();
            }
        });
    },

    locationDropdown: function($disabled = false){
        var data = ['country' , 'province' , 'city' , 'area'];
        $.map(data,function(value){
            $(`.area-based-shipment #${value}`).prop('disabled',$disabled);
        });
    },

    addCustomerAddressFieldsInWrapper: function(check = 'add'){
        var data = ['country' , 'province' , 'city' , 'area'];
        $.map(data,function(value){
            if(check == 'add'){
                $("#customer-address-field-wrapper").append('<input type="hidden" id="'+value+'" name="'+value+'">');
            }else if(check == 'remove'){
                $(`#customer-address-field-wrapper #${value}`).remove();
            }
        });
    },

    updateOrderPaymentMode: function (action, orderId = null, refundReason = null) {
        let onSuccess = function (response) {
            App.Helpers.refreshDataTable();
            App.Order.selectedOrders = [];
            if(response.message_body != undefined){
                if(response.message_body.failure.length > 0) {
                    App.Order.openMessagesModal(response);
                }
            }
        };

        let onFailure = function (response) {
            App.Helpers.refreshDataTable();
            App.Order.selectedOrders = [];
        };

        if(orderId == null){
            if(App.Order.selectedOrders.length > 0) {
                orderId = JSON.stringify(App.Order.selectedOrders);
            }else{
                return App.Helpers.showErrorMessage({'error': 'Please select at least one order'});
            }
        }else{
            orderId =  JSON.stringify([orderId]);
        }

        if(refundReason == null) {
            let confirmAction = function (isConfirm) {
                if (isConfirm) {
                    let requestData = {'orderId': orderId, 'action': action, 'refund_reason': refundReason}
                    let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateOrderPaymentMode);
                    App.Ajax.post(url, requestData, onSuccess, onFailure, 'hide_message_open_modal');
                }
            }
            App.Helpers.confirm('You want to perform this action', confirmAction);
        }else{
            let requestData = {'orderId': orderId, 'action': action, 'refund_reason': refundReason}
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateOrderPaymentMode);
            App.Ajax.post(url, requestData, onSuccess, onFailure, 'hide_message_open_modal');
        }
    },

    openRefundReasonModal: function (action, orderId = null) {

        if(orderId == null){
            if(App.Order.selectedOrders.length > 0) {
                App.Order.refundOrderId = JSON.stringify(App.Order.selectedOrders);
            }else{
                return App.Helpers.showErrorMessage({'error': 'Please select at least one order'});
            }
        }else{
            App.Order.orderId = orderId;
            App.Order.refundOrderId =  JSON.stringify([orderId]);
        }

        App.Order.refundAction = action;
        $('#refundReason').val('').keyup();
        $('#orderRefundReason').modal('show');
    },

    saveRefundReason: function () {
        var refundReason = $('#refundReason').val();
        let onSuccess = function () {
            $('#orderRefundReason').modal('hide');
            App.Order.updateOrderPaymentMode(App.Order.refundAction, App.Order.orderId, refundReason);
            App.Order.orderId = null;
        }

        let url = App.Helpers.generateApiURL(App.Constants.endPoints.saveOrderRefundReason);
        let requestData = {'orderId' :App.Order.refundOrderId, 'refundReason' :refundReason}
        App.Ajax.post(url, requestData, onSuccess, false, 'hide_message_with_loader', 0);

    },

    openMessagesModal: function (response) {
        var success = response.message_body.success;
        var failure = response.message_body.failure;
        $('#successMessages').html('');
        $('#failureMessages').html('');
        if(success.length > 0) {
            var html = '';
            for (var i = 0; i < success.length; i++) {
                html += '<tr><td><span class="payment-status-response">' + success[i].order_ref + '</span></td><td><span class="payment-status-response">' + success[i].message + '</span></td></tr>';
            }
            $('#successMessages').html(html);
        }else{
            $('#successMessages').html('<tr><td><span class="payment-status-response">---</span></td><td><span class="payment-status-response">---</span></td></tr>');
        }

        if(failure.length > 0) {
            var html = '';
            for (var j = 0; j < failure.length; j++) {
                html += '<tr><td><span class="payment-status-response">' + failure[j].order_ref + '</span></td><td><span class="payment-status-response">' + failure[j].message + '</span></td></tr>';
            }
            $('#failureMessages').html(html);
        }else
        {
            $('#failureMessages').html('<tr><td><span class="payment-status-response">---</span></td><td><span class="payment-status-response">---</span></td></tr>');
        }

        $('#responseMessages').modal('show');

    },

    getVoucherifyPromotionsDetails:function(){
        var attr = [];
        $("#promotion_details").html('');
        attr.push({
            'promotion_id'    : $(".promotion-detail-btn").attr('promotion_id'),
            'promotion_name'  : $(".promotion-detail-btn").attr('promotion_name'),
            'promotion_type'  : $(".promotion-detail-btn").attr('promotion_type'),
            'promotion_amount': $(".promotion-detail-btn").attr('promotion_amount'),
            'promotion_campaign_id': $(".promotion-detail-btn").attr('promotion_campaign_id'),
            'promotion_redeemed_at': $(".promotion-detail-btn").attr('promotion_redeemed_at'),
        });
        $.each(attr[0], function(key,val) {
            var label_name = key.replaceAll('_',' ');
            label_name = label_name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
            $("#promotion_details").append('<label>'+ label_name.toUpperCase()  +' : '+ val +'</label>');
        });
        $('.voucher_promotion_card_modal').modal('show');
    },
};