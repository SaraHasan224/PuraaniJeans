App.Products = {
    listing: 1,
    selectedProducts: [],
    countIds: [],
    syncInProgress: App.Constants.OFF,
    attributeSection: 1,
    isImported: App.Constants.OFF,

    setNoListing: function () {
        App.Products.listing = App.Constants.OFF;
        const store_id = $("#merchant_store").val();
        $(".remove_id").val(store_id);
        $("#store_id").val(store_id);
        $("#form_product_add .remove_id").trigger('change');
    },

    clearProductSelection: function () {
        App.Products.selectedProducts = [];
        if ($(".cbbox_all_prod").is(':checked')) {
            $(".cbbox_all_prod").click();
        }
    },

    changeStore: function (store_id) {
        App.Products.countIds = [];
        App.Products.selectedProducts = [];
        App.Helpers.refreshDataTable();
    },

    setDefaultJourney: function (form_id) {
        const journey_id = $("#" + form_id + " .store_id option:selected").attr('data-joureny_id');
        let quantity = $("#" + form_id + " .store_id option:selected").attr('data-quantity');
        if (quantity == undefined) {
            quantity = $("#merchant_store option:selected").attr("data-quantity");
        }
        $("#" + form_id + " #max_quantity").attr('max', quantity);
        $("#" + form_id + " #item-img-output").attr('src', App.Constants.IMAGE_PLACEHOLDER);
        $("#" + form_id + " #journey_id").val(journey_id);
        $("#" + form_id + " #max_quantity").val(quantity);
        $('#' + form_id + ' .select2').select2();
        App.Constants.CROPPER_PARENT_ID = form_id;
    },

    editProduct: function (product_id) {
        const url = App.Helpers.generateApiURL(App.Constants.endPoints.editMerchantProductBySku);

        let onSuccess = function (response) {
            $("#product_modal_wrapper").html(response.render_html);
            $("#btn_prodoct_detail").trigger('click');
            App.Products.setAdditionalNotesCount('form_product_update')
        };

        let requestData = {
            'product_id': product_id
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    getLocationSubCategory: function (form_type, category_id) {
        const form_id = 'form_product_' + form_type;
        const url = App.Helpers.generateApiURL(App.Constants.endPoints.getLocationSubCategory);

        let onSuccess = function (response) {
            App.Helpers.fileDropdown(form_id + ' #location_subcategory_id', response, 'Select Subcategory', '');
        };

        let requestData = {
            'category_id': category_id
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    initializeValidations: function () {
        $("#search-form").validate();
        $("#form_product_add").validate();
        $("#form_product_update").validate();
        $('[data-toggle="tooltip"]').tooltip();
        $('.select2').select2();

        $(".cbbox_all_prod").click(function () {
            if ($(this).is(":checked")) {
                $(".theClass").not(":disabled").each(function () {
                    App.Products.selectedProducts.push($(this).val());
                });
            } else {
                App.Products.selectedProducts = [];
            }
        });
        $(document).on("click", ".theClass", function () {
            if ($(this).is(":checked")) {
                App.Products.selectedProducts.push($(this).val());
            } else {
                App.Products.selectedProducts.splice($.inArray($(this).val(), App.Products.selectedProducts), 1);
            }
        });
    },

    removeFilters: function () {
        $("#product_id").val('').trigger('change');
        $("#is_imported").val('').trigger('change');
        $("#imported_product_id").val('');
        $("#name").val("");
        $("#category_name").val("").trigger('change');
        $("#product_delivery_type_filter ").val("").trigger('change');
        $("#sku").val("");
        $("#price").val("");
        $("#discount").val('');
        $("#delivery_time").val('');
        $("#in_stock").val('').trigger('change');
        $("#journey").val('');
        $("#store").val('').trigger('change');
        $("#created_by").val('').trigger('change');
        $("#daterange").val('');
        App.Helpers.removeAllfilters();
    },

    removeSelectionFilters: function () {
        $("#store").val('').trigger('change');
        $("#category_name").val("").trigger('change');
        $("#product_delivery_type_filter ").val("").trigger('change');
        $("#in_stock").val('').trigger('change');
        $("#is_imported").val('').trigger('change');
        $("#daterange").val('');
        App.Helpers.oTable.draw();
    },

    initializeDataTable: function () {
        let table_name = "pim_products";
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getMerchantProducts);

        const columns = [
            {data: 'show', name: 'show', orderable: false, searchable: false},
            {data: 'check', name: 'check', orderable: false, searchable: false},
            {data: "product_id", name: "product_id", orderable: true, searchable: true},
            {data: "name", name: "name", orderable: true, searchable: true},
            {data: "category_name", name: "category_name", orderable: true, searchable: true},
            {data: "image", name: "image", orderable: false, searchable: false},
            {data: "store", name: "store", orderable: true, searchable: true},
            {data: "is_imported", name: "is_imported", orderable: true, searchable: true},
            {data: "imported_product_id", name: "imported_product_id", orderable: true, searchable: true},
            {data: "sku", name: "sku", orderable: true, searchable: true},
            {data: "price", name: "price", orderable: true, searchable: true},
            {data: "size", name: "size", orderable: true, searchable: true},
            {data: "max_quantity", name: "max_quantity", orderable: true, searchable: true},
            {data: "delivery_time", name: "delivery_time", orderable: true, searchable: true},
            {data: "product_delivery_type", name: "product_delivery_type", orderable: true, searchable: true},
            {data: "in_stock", name: "in_stock", orderable: true, searchable: true},
            {data: "journey", name: "journey", orderable: true, searchable: true},
            {data: "created_by", name: "created_by", orderable: true, searchable: true},
            {data: "created_at", name: "created_at", orderable: true, searchable: true},
            {data: "short_description", name: "short_description", orderable: false, searchable: false},
        ];

        let postData = function (d) {
            d.product_id = $("#product_id").val();
            d.name = $("#name").val();
            d.category_name = $("#category_name").val();
            d.sku = $("#sku").val();
            d.price = $("#price").val();
            d.discount = $("#discount").val();
            d.delivery_time = $("#delivery_time").val();
            d.product_delivery_type = $("#product_delivery_type_filter").val();
            d.in_stock = $("#in_stock").val();
            d.is_imported = $("#is_imported").val();
            d.imported_product_id = $("#imported_product_id").val();
            d.journey = $("#journey").val();
            d.store = $("#store").val();
            d.created_by = $("#created_by").val();
            d.created_at = $("#daterange").val();
        };

        let orderColumn = [[2, "desc"]];
        let searchEnabled = true;

        App.Helpers.CreateDataTableIns(table_name, url, columns, postData, searchEnabled, orderColumn, [], false, true, 10, 1);
    },

    createManualOrder: function () {
        let store_id = $.trim($("#search-form #store").val());
        let multi_store_products_selected = false;
        let last_store_id = '';

        $.each($("input[name='data_raw_id[]']:checked"), function () {
            let store_id = $(this).attr('data-store');
            if (last_store_id != '' && store_id != last_store_id) {
                multi_store_products_selected = true;
            }
            last_store_id = store_id;
        });

        if (App.Products.selectedProducts.length < App.Constants.ON) {
            App.Helpers.selectRowsFirst("Please select at least one product.");
        } else if (multi_store_products_selected) {
            App.Helpers.selectRowsFirst("Please select same store products to create order.");
        } else {
            if ($('.prod_disabled').is(':checked')) {
                App.Helpers.selectRowsFirst("Please uncheck NOT AVAILABLE products to proceed!");
            } else {
                if (store_id == '') {
                    store_id = $(".theClass:checked:first").attr('data-store');
                }
                $("#form_product_ids #store_id").val(store_id);
                $("#form_product_ids #product_ids").val(App.Products.selectedProducts);
                $("#btn_form_product_ids").click();
            }
        }
    },

    saveProduct: function (action) {
        const form = $("#form_product_" + action);
        App.Products.setDiscountMinimum('form_product_'+ action);

        let prod_size = $("#prod_size").val();
        if(prod_size == ''){
            $('#collapseOne').addClass('show');
            $('html').animate({ scrollTop: $("#collapseOne").offset().top}, 'slow')}

        if (form.valid()) {
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.saveMerchantProduct);

            let prod_price = $("#form_product_" + action + " #prod_price").val();
            let prod_discount = $("#form_product_" + action + " #prod_discount").val();
            let discount_type = $("#form_product_" + action + " #discount_type").val();
            prod_price = parseInt(prod_price);
            prod_discount = parseInt(prod_discount);

            if (prod_discount > prod_price && discount_type == App.Constants.ON) {
                swal("Warning!", 'Discount can not be greater than product price.', "warning");
                return false;
            }

            if (discount_type != App.Constants.ON && prod_discount > 100) {
                swal("Warning!", 'Discount can not be set greater than 100%.', "warning");
                return false;
            }

            let onSuccess = function (response) {
              if (App.Products.listing === App.Constants.OFF) {
                const sku = $("#created_sku").val();
                $("#txt_sku_search").val($.trim(sku));
                $("#btn_get_local_product").click();
                $('#form_product_add #brandverseImage').val('');
                $('#form_product_update #brandverseImage').val('');
                form[0].reset();
                setTimeout(function () {
                  $(".modal-product .close").click();

                }, 100);
              } else {
                  if(action == 'add' && response.product_id){
                    window.location = "/products/"+ response.product_id+"/edit";
                  }else {
                    window.location = "/products";
                  }
              }
            };

            const productForm = document.getElementById("form_product_" + action);
            let requestData = new FormData(productForm);
            requestData.append('is_imported', App.Products.isImported)

            App.Ajax.post(url, requestData, onSuccess, false, 'upload_file', 0);
        }
    },

    togglePurchaseLimit: function (value) {
        if (value != App.Constants.ON) {
            $("#merchant_product_settings_form #wrapper_max_purchase_limt").show();
            $("#merchant_product_settings_form #wrapper_max_purchase_limt input").attr('required', true);
        } else {
            $("#merchant_product_settings_form #wrapper_max_purchase_limt input").attr('required', false);
            $("#merchant_product_settings_form #wrapper_max_purchase_limt input").removeAttr('required');
            $("#merchant_product_settings_form #wrapper_max_purchase_limt").hide();
        }
    },

    saveMerchantBuyerProtectionSettings: function(form_id){
        const form = $("#" + form_id);
        var buyerProtection = $("#buyer_protection_info").val();
        var defaultBuyerProtection = $("#defaultBuyerProtectionCheck").val();

        if (form.valid()) {
            const url = App.Helpers.generateApiURL(App.Constants.endPoints.saveMerchantBuyerProtectionSettings);
            const requestData = form.serialize();

            if(buyerProtection !== defaultBuyerProtection){
                $(".modal_checkout_btn").modal('show');
            }

            let onSuccess = function (response) {
                $("#defaultBuyerProtectionCheck").val(response[App.Constants.OFF].buyer_protection_info);
                if(buyerProtection !== defaultBuyerProtection){
                    App.Products.setCheckoutButtonCockpit();
                }
            };

            App.Ajax.post(url, requestData, onSuccess,false, false);
        }

    },

    saveMerchantProductSettings: function (form_id) {
        const form = $("#" + form_id);

        if (form.valid()) {
            const url = App.Helpers.generateApiURL(App.Constants.endPoints.saveMerchantProductSettings);
            const requestData = form.serialize();

            let onSuccess = function (response) {};

            App.Ajax.post(url, requestData, onSuccess,false, false);
        }
    },

    buyerProtectionToggle: function(element,merchant_id){
        var check      = App.Constants.OFF;
        const url      = App.Helpers.generateApiURL(App.Constants.endPoints.getCheckoutBtnAgainstCheck);
        const storeIds = JSON.parse($("#merchant_stores_ids").val());
        const length   = storeIds.length;

        if(element.is(':checked')){
            element.val(App.Constants.ON);
            check = App.Constants.ON;
        }else{
            element.val(App.Constants.OFF);
            check = App.Constants.OFF;
        }

        let onSuccess = function (response) {
            $.each(storeIds , function(index, store_id) {
                $('#save_checkout_btn_form #checkoutBtn_'+store_id).html(response.merchantCheckoutBtnBranding[index].default_checkout_btn_source_code);
            });
        };

        let requestData = {
            'merchant_id': merchant_id,
            'buyer_protection_check': check
        };
        App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    },

    setCheckoutButtonCockpit: function (check) {
        const url           = App.Helpers.generateApiURL(App.Constants.endPoints.saveMerchantCheckoutButtons);
        const storeIds      = JSON.parse($("#merchant_stores_ids").val());
        const length        = storeIds.length;
        const merchant_id   = $("[name=merchant_id]").val();
        var imageData       = [];
        var modalCloseCheck = false;

        $.each(storeIds , function(index, store_id) {
            var button = document.querySelector("#checkoutBtn_"+store_id +" .checkout_btn_container");
                domtoimage.toPng( button )
                .then(function (dataUrl) {

                    var requestData = {
                        'store_id': store_id,
                        'merchant_id': merchant_id,
                        'image_data_url':  dataUrl,
                        'checkout_btn_text': $("#checkout_btn_title_"+store_id).val(),
                        'checkout_btn_style': $("#checkout_btn_styles_"+store_id).val(),
                        'checkout_btn_source_code': $("#source_code_checkout_btn_"+store_id).val()
                    };

                    imageData.push(requestData);

                    let onSuccess = function (response) {
                        if(modalCloseCheck){
                            $(".modal_checkout_btn").modal('hide');
                        }
                    };

                    if(index === length - 1){
                        let request = { 'data': JSON.stringify(imageData)}
                        modalCloseCheck = true;
                        App.Ajax.post(url, request, onSuccess, false, {}, 0);
                    }
                })
                .catch(function (error) {
                    console.error('oops, something went wrong!', error);
                });
        });

    },

    bulkUpdateProducts: function (method_action) {
        App.Products.countIds = [];
        $.each($("input[name='data_raw_id[]']:checked"), function () {
            App.Products.countIds.push($(this).val());
        });

        let record_count = 'products';

        if (App.Products.countIds.length == 1) {
            record_count = 'product';
        }

        if (App.Products.countIds.length == 0) {
            App.Helpers.selectRowsFirst("Please select at least one product");
        } else {
            let action = function (isConfirm) {
                if (isConfirm) {

                    if ($(".cbbox_all_prod").is(':checked')) {
                        $(".cbbox_all_prod").click();
                    }

                    let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateMerchantProducts);

                    let requestData = {"delete_ids": App.Products.countIds, "action": method_action};

                    let success = function (response) {
                        App.Helpers.refreshDataTable();
                    };
                    App.Ajax.post(url, requestData, success, false, {});
                }
            };
            let action_to_be_taken = 'delete';
            if (method_action == 1) {
                action_to_be_taken = 'mark as available';
            } else if (method_action == 0) {
                action_to_be_taken = 'mark as NOT available';
            }
            App.Helpers.confirm('You want to ' + action_to_be_taken + ' selected ' + record_count + '.', action);

        }
    },

    closeModal: function () {
        if (App.Products.listing == App.Constants.OFF) {
            $("#store_id").val('');
            $("#store_id_hidden").val('');
        }

        var formFilled = false;
        $('#form_product_add input').each(function () {
            if ($(this).val() !== '') {
                formFilled = true;
            }
        });
        if (formFilled) {
            let text = "Your Product is not saved, do you want to continue closing the screen?";
            let action = function (isConfirm) {
                if (isConfirm) {
                    $('#form_product_add')[0].reset();
                    $(".modal-product").modal('hide');
                    $(".cancel").click();
                }
            }
            App.Helpers.confirm(text, action);
        } else {
            $(".modal-product").modal('hide');
        }
    },

    checkValue: function (form_id) {
        const value = $("#" + form_id + " #prod_discount").val();
        const discount_type = $("#" + form_id + " #discount_type").val();
        let element = $("#" + form_id + " .currency-code")

      if (discount_type != App.Constants.ON && value >= 100) {
            $("#prod_discount").val(99);
        }


      if(discount_type != App.Constants.ON){
        if(element.parent().hasClass('textWithField')){
          element.parent().removeClass('textWithField')
        }
        element.hide();
        $('#cap_amount').attr('disabled',false);
        $('.cap-amount').show();
      }else{
          element.show();
        if(!element.parent().hasClass('textWithField')){
          element.parent().addClass('textWithField')
        }
        $('#cap_amount').attr('disabled',true);
        $('.cap-amount').hide();
      }
    },


    setDiscountMinimum: function (form_id) {
        const discount_type = $("#" + form_id + " #discount_type").val();

        if($("#" + form_id + " #prod_discount").val()){
          if (discount_type != App.Constants.ON) {
            $("#" + form_id + " #prod_discount").prop('max',99)
          }else{
            let price = $("#" + form_id + " #prod_price").val();
            if(price && price!=0) {
              $("#" + form_id + " #prod_discount").prop('max',price-1)
            }
          }
        }
    },

    publishCatalog: function () {

        let action = function (isConfirm) {
            if (isConfirm) {

                let url = App.Helpers.generateApiURL(App.Constants.endPoints.publishCatalog);

                let requestData = {};

                let success = function (response) {
                    App.Helpers.refreshDataTable();
                };
                App.Ajax.post(url, requestData, success, false, {});
            }
        };

        App.Helpers.confirm('You want to publish catalog', action);
    },

    productPurchaseChange: function (formId, thisKey) {
        if ($(thisKey).prop('checked')) {
            $(`#${formId} #location_category_id`).prop('required', true);
        } else {
            $(`#${formId} #location_category_id`).prop('required', false);
            $(`#${formId} #location_category_id`).removeClass('error');
            $(`#${formId} #location_category_id-error`).hide();
        }
    },

    setAdditionalNotesCount: function (formId) {
        const totalCount = $(`#${formId} #text_note_for_shipment`).attr('maxLength')
        let currentCount = 0;
        if ($(`#${formId} #text_note_for_shipment`).val()) {
            currentCount = $(`#${formId} #text_note_for_shipment`).val().length
        }
        $(`#${formId} #additional_note_count`).html(currentCount + '/' + totalCount)
    },

    redirectEditProduct: function(productId){
      window.open('/products/'+productId+'/edit', '_self');
    },

    syncPIM: function () {
      let action = function (isConfirm) {
        if (isConfirm) {

          let url = App.Helpers.generateApiURL(App.Constants.endPoints.syncPIM);

          let requestData = {};

          let success = function (response) {
              if(response.syncInProgress ==  App.Constants.ON){
                App.Products.syncInProgress = App.Constants.ON
                $('#pimInProgress').show();
                $('#syncPim').hide();
              }
          };
          App.Ajax.post(url, requestData, success, false, {});
        }
      };

      App.Helpers.confirm('You want to sync PIM products', action);
    },

    uploadProductImages: function () {
      let url = App.Helpers.generateApiURL(App.Constants.endPoints.uploadProductImages);
      let onSuccess = function (response) {
        if (response) {
          $('#images-products').append(response);
          if (!$('#images-products .defaultImageButton').is(":checked")){
            $('#images-products .defaultImageButton').attr('checked', true)
          }
          $('#' + App.Constants.CROPPER_PARENT_ID + ' #item-img-output').attr('src', App.Constants.IMAGE_PLACEHOLDER);
          $('#' + App.Constants.CROPPER_PARENT_ID + ' #cropped_image').val(App.Constants.IMAGE_PLACEHOLDER);
        }
      };
      const uploadForm = document.getElementById(App.Constants.CROPPER_PARENT_ID);
      const requestData = new FormData(uploadForm);
      App.Ajax.post(url, requestData, onSuccess, false, 'upload_file', 0);
    },

    addNewVariant: function (productId) {
      App.Products.attributeSection +=1;
      const url = App.Helpers.generateApiURL(App.Constants.endPoints.getVariantAttributeSection);
      let onSuccess = function (response) {
        $("#wrapper-varaint-options").append(response);
        for (let count = 1; count < App.Products.attributeSection; count++) {
          let previousValue = $("#attribute_"+count+" option:selected").val()
          $("#attribute_"+App.Products.attributeSection +" option[value='"+previousValue+"']").remove();
        }
        $(".attributeSelect").select2({
          tags: true
        });
        $(".optionSelect").select2({
          tags: true,
          tokenSeparators: [',']
        })
        $("#attribute_"+App.Products.attributeSection).trigger('change');
        if(App.Products.attributeSection == App.Constants.maximumAttributeSection){
            $('#form_variant_add .addNewVariantBtn').addClass('out_of_screen')
        }
        $('#form_variant_add').validate();
      };
      let requestData = {
        'section': App.Products.attributeSection,
        'productId': productId
      };
      App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    saveAttribute: function () {
      let form = $('#form_variant_add');
      var valid = true;

        for(var i = 2; i<=$('.nameVariantOption').length; i++){
            var field = $('#attribute_'+i);
            var wrap = $('select#attribute_'+i).closest('.select2Wrap');
            var err = wrap.find('.error');
            if(field.length > 0 && field.val() == null){
                valid = false;
                if(err.length == 0)
                {
                    wrap.prepend('<span class="error" >This field is required.</span>');
                }
            }else{
                valid = true;
                err.remove();
            }
        }

        if (form.valid() && valid){
        const url = App.Helpers.generateApiURL(App.Constants.endPoints.saveAttributeOptions);
        let onSuccess = function (response) {
          if(response){
            $("#all-variant").html(response);
          }
        };
        let requestData =  form.serialize()
        App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
      }
    },

    saveVariants:function () {
      let form = $('#form_variant_save');
      var has_variants = App.Constants.OFF;
      if (form.valid()){
        const url = App.Helpers.generateApiURL(App.Constants.endPoints.saveVariants);
        let onSuccess = function (response) {
          window.location = "/products";
        };
        if ($("#enable_variants").is(':checked')) {
          has_variants = App.Constants.ON;
        }

        let requestData = form.serialize() + '&has_variants=' + has_variants;
        App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
      }

    },

    getAttributeOptions: function (thisKey, section, productId) {
      var selected = $(thisKey).find('option:selected');
      var id = selected.data('id');
      if(id){
        const url = App.Helpers.generateApiURL(App.Constants.endPoints.getAttributeOptions);
        let onSuccess = function (response) {
          if(response.options){
            $('#option_'+section).html(' ');
            $.each(response.options, function(key, value) {
              if(response.selected_options ){
                var selected = '';
                if (key in response.selected_options){
                  selected = 'selected'
                }
              }
              $('#option_'+section).append($("<option "+selected+"></option>")
              .attr("value", value).text(value));
            });
            $(".optionSelect").select2({
              tags: true,
              tokenSeparators: [',']
            })
          }
        };

        let requestData = {'attribute_id': id, 'productId': productId};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
      }
    },

    openVarinatImageModal: function (key) {
      $('#select-image-form #variantKey').val(key)
    },

    selectVariantImage: function () {
     const imageId = $('#select-image-form input[name="image_id"]:checked').val();
     const key = $('#select-image-form #variantKey').val()
     const url = $("#select-image-form #product_image_" + imageId + " #product_image").attr('src');
     $('#form_variant_save #variant-image-'+key + ' #item-img-output').attr('src', url)
     $('#form_variant_save #image-id-'+key).val(imageId)
      $('.modal .close').click()
    },



    deleteAttribute: function (section) {
        $('#wrapper-varaint-options').find('.'+section).remove();
        App.Products.attributeSection -=1;
        if($('.nameVariantOption').length <= 2){
            $('#form_variant_add .addNewVariantBtn').removeClass('out_of_screen');
            if($('#form_variant_save').length > 0){
                $('#saveAttribute').trigger('click');
            }
        }
    },

    exportProducts: function () {
        let product_id = $("#product_id").val();
        let name = $("#name").val();
        let imported_product_id = $("#imported_product_id").val();
        let sku = $("#sku").val();
        let created_by = $("#created_by").val();
        let store = $("#store").val();
        let category_name = $("#category_name").val();
        let product_delivery_type_filter = $("#product_delivery_type_filter").val();
        let in_stock = $("#in_stock").val();
        let is_imported = $("#is_imported").val();
        let daterange = $("#daterange").val();
        let selected_products = JSON.stringify(App.Products.selectedProducts);

        let query_string = '?product_id=' + product_id + '&name=' + name + '&imported_product_id=' + imported_product_id + '&sku=' + sku + '&created_by=' + created_by + '&store=' + store + '&category_name=' + category_name +
            '&product_delivery_type_filter=' + product_delivery_type_filter + '&in_stock=' + in_stock + '&is_imported=' + is_imported + '&daterange=' + daterange + "&selected_products=" + selected_products;
        window.open(
            '' + App.Constants.endPoints.exportProducts + query_string,
            '_blank'
        );
    },

};
