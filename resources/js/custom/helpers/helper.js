App.Helpers = {
    oTable: null,
    toggleState: 1,
    storeUrl: '',
    uploadedFile: null,

    toggle: function () {
        if (this.toggleState === 0) {
            $("input[name='data_raw_id[]']").prop("checked", false);
            this.toggleState = 1;
        } else {
            $("input[name='data_raw_id[]']").not(":disabled").prop("checked", true);
            this.toggleState = 0;
        }
    },

    removeAllfilters: function (table_id = 'dataList') {
        $('#' + table_id).DataTable().clear().draw();
    },

    generateApiURL: function (endPoint) {
        return App.Constants.API_HOST + endPoint;
    },

    randomString: function (length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result.toUpperCase();
    },

    randomStringWithPrefix: function (length, prefix) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return prefix + '' + result.toUpperCase();
    },

    selectRowsFirst: function (text) {
        swal({
            title: "",
            text: text,
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Close",
            closeOnConfirm: false,
            closeOnCancel: true
        });
    },

    confirmWithInput: function (text, html, action) {
        swal({
                title: text,
                text: html,
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputPlaceholder: "Write something"
            },action);
    },

    confirm: function (text, action) {
        swal({
            title: "Are you sure?",
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            closeOnConfirm: true,
            closeOnCancel: true
        }, action);
    },

    showSuccessMessage: function (message) {
        $(".alert-danger").hide();
        if($('.alert .message').html() !== ''){
            message += '<br>'
        }
        $('.alert .message').prepend(message);
        $(".alert-success").show();
        this.hideMessage();
    },

    showErrorMessage: function (messages) {
        $('.alert .message').html('');
        $(".alert-success").hide();
        let errors = '';
        $.each(typeof messages.error == 'object' ? messages['error'] : messages, function (key, value) {
            errors += '<p>' + value + '</p>';
        });
        $('.alert .message').html('' + errors);
        $(".alert-danger").show();
        this.hideMessage();
    },

    scrollToTop: function (y) {
        if (y == undefined) {
            y = 0;
        }
        window.scrollTo({top: y, behavior: 'smooth'});
    },

    hideMessage: function () {
        window.scrollTo(0, 0);
        setTimeout(function () {
            $(".alert").hide();
            $('.alert .message').html('');
        }, 10000);
    },

    bulkRecordsDelete: function (text, url) {
        var countIds = [];
        $.each($("input[name='data_raw_id[]']:checked"), function () {
            countIds.push($(this).val());
        });
        console.log("countIds: ", countIds)
        if (countIds.length == 0) {
            App.Helpers.selectRowsFirst("Please select at least one row");
        } else {
            let action = function (isConfirm) {
                if (isConfirm) {
                    let requestData = {"delete_ids": countIds};
                    let success = function (response) {
                        if ($(".checkBoxes").is(':checked')) {
                            $(".checkBoxes").click();
                        }
                        App.Helpers.refreshDataTable();
                        if (response.status == "success") {
                          swal("Deleted!", response.message, "success");
                          // location.reload();
                        } else {
                          swal("Delete!", response.message, "error");
                        }
                    };
                    App.Ajax.post(url, requestData, success, false, {});
                }
            };
            App.Helpers.confirm(text, action);

        }
    },

    refreshDataTable: function () {
        if (App.Helpers.oTable) {
            App.Helpers.oTable.draw();
        }
    },

    CreateDataTableIns: function (table_id, ajax_url, columns, postData, searchEnabled, orderColumns, buttons = [], add_checkbox = true, enableAutoWidth = true, pageLength = 10, checkboxPosition = 0, reOrderRow = false, reOrderDataSource='position', ajaxRequestType='GET' , defaultContent = "" , stateSave = true) {
        if (buttons.length === 0) {
            buttons = [];
        }
        let checkbox = null;
        let dataTableId = 'dataList';
        if(table_id) {
            dataTableId = table_id;
        }
        let className = 'dt-body-center';
        if (reOrderRow){
            reOrderRow = {
                selector: 'td.reorder',
                dataSrc: reOrderDataSource
            }
        }

        if (add_checkbox) {
            checkbox = function (data, type, full, meta) {
                return '<input type="checkbox" name="data_raw_id[]" class=" theClass" value="' + full['id'] + '">';
            };
        }
        if ($.fn.dataTable.isDataTable('#' + dataTableId)) {
            App.Helpers.oTable = $('#' + dataTableId).DataTable();
        } else {
          let headers = {
            'X-CSRF-Token': App.Constants.CSRF_TOKEN
          };
            App.Helpers.oTable = $('#' + dataTableId).DataTable({
                // dom: 'Bfrtip',
                responsive: true,
                bFilter: false,
                autoWidth: enableAutoWidth,
                processing: true,
                stateSave: stateSave,
                rowReorder: reOrderRow,
                serverSide: true,
                lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                pageLength: pageLength, // default records per pag
                buttons: buttons,
                searching: searchEnabled,
                order: orderColumns,
                ajax: {
                  headers: headers,
                  type : ajaxRequestType,
                    url: ajax_url,
                    data: postData,
                    error: function(xhr, error, thrown) {

                        if(xhr.status === App.Constants.http_statues.authenticationError){
                            window.location.href = "/";
                        }
                    }
                },
                columnDefs: [{
                    "targets": checkboxPosition,
                    'searchable': false,
                    'orderable': false,
                    'checkboxes': {
                        'selectRow': true
                    },
                    'className': className,
                    'render': checkbox,
                    "defaultContent": defaultContent,
                }],
                columns: columns
            });
        }

        $('#search-form').on('submit', function (e) {
            App.Helpers.oTable.draw();
            e.preventDefault();
        });

        $('#select-form').on('submit', function (e) {
            var form = this;

            var rows_selected = table.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function (index, rowId) {
                // Create a hidden element
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'product_id[]')
                        .val(rowId)
                );
            });

            // Remove added elements
            $('input[name="id\[\]"]', form).remove();

            // Prevent actual form submission
            e.preventDefault();
        });
    },

    resetDatatableState: function(){
        if(App.Helpers.oTable){
          App.Helpers.oTable.state.clear();
        }
    },

    fileDropdown: function (dd_id, options, first_value, selected_value) {
        $("#" + dd_id).find('option').remove();
        $("#" + dd_id).append(new Option(first_value, ''));
        $.each(options, function (key, value) {
            if (value == selected_value) {
                $("#" + dd_id).append(new Option(key, value, true, true));
            } else {
                $("#" + dd_id).append(new Option(key, value));
            }
        });
    },

    fileDropdownByKeyValue: function (dd_id, options, first_value, selected_value, key1, value1) {
        $("#" + dd_id).find('option').remove();
        $("#" + dd_id).append(new Option(first_value, ''));
        $.each(options, function (key, value) {
            if (value[key1] == selected_value) {
                $("#" + dd_id).append(new Option(value[value1], value[key1], true, true));
            } else {
                $("#" + dd_id).append(new Option(value[value1], value[key1]));
            }
        });
    },

    getProductsByBShopStoreId: function(store_id){
        const BShopStoreId = App.Constants.BShopStoreId;
        if(store_id > 0){
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getBShopStoreProducts);
            url += store_id;
            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('featured_product', response, 'Select Product', BShopStoreId);
            };
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    getProductsByStoreId: function(store_id){
        if(store_id > 0){
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getProductsByStore);
            url += store_id;
            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('products', response, 'Select Product', null);
            };
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    getProvincesByCountryId: function (country_id) {
        const province_id = App.Constants.ProvinceId;

        if (country_id > 0) {
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getProvinces);
            url += country_id;
            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('province', response, 'Select Province', province_id);
            };

            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    getCitiesByProvinceId: function (province_id) {
        const city_id = App.Constants.CityId;
        const store_id = App.Constants.StoreId;

        if (province_id > 0) {
            if(App.Constants.isAreaBaseShipment &&
                (  App.Constants.internationalShipment == null  ||  App.Constants.internationalShipment == undefined  ) &&
                ( App.Constants.storeShipmentEveryWhereCase == null || App.Constants.storeShipmentEveryWhereCase == undefined )
            ){
                App.Helpers.fileDropdown('city', App.Constants.StoreCities[province_id] , 'Select City', city_id);
            }else{
                let url = App.Helpers.generateApiURL(App.Constants.endPoints.getCities);
                url += province_id;

                let requestData = {};
                let onSuccess = function (response) {
                    App.Helpers.fileDropdown('city', response, 'Select City', city_id);
                };

                App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
            }
        }
        App.Helpers.fileDropdown('area' ,[] , 'Select Area', '' );
    },

    getCitiesByProvinceIdOriginal: function (province_id) {
        const city_id = App.Constants.CityId;

        if (province_id > 0) {
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getCities);
            url += province_id;

            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('city', response, 'Select City', city_id);
            };

            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    getAreasByCityId: function (city_id , dropdownCase = false , action = '' , areaIds = []) {
        const area_id = App.Constants.AreaId;

        if (city_id > 0) {

            if(App.Constants.isAreaBaseShipment && App.Constants.internationalShipment == null && !App.Constants.storeShipmentEveryWhereCase){
                App.Helpers.fileDropdown('area', App.Constants.StoreCityAreas[city_id] , 'Select Area', area_id);
            }else{
                let url = App.Helpers.generateApiURL(App.Constants.endPoints.getAreas);
                url += city_id;
                let requestData = {};
                let onSuccess = function (response) {
                    App.Helpers.fileDropdown('area', response, 'Select Area', 0);
                    if(action == 'update'){
                        $('#area').val('').trigger('change');
                        $('#area').val(areaIds).trigger('change');
                        // If city is present but areas are empty on update case...
                        if(areaIds.includes(App.Constants.OFF) || areaIds == App.Constants.OFF){
                           App.MerchantStores.setPlaceholderInSelect2('area', 'Everywhere' , 400);
                        }
                    }else{
                        App.MerchantStores.setPlaceholderInSelect2('area');
                    }
                };

                App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
            }
        }

        if(dropdownCase && city_id == ''){
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getAreas);
            url += App.Constants.OFF;
            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('area', response, 'Select Area', area_id);
            };
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }

    },

    getAreasByCityIdOriginal: function (city_id) {
        const area_id = App.Constants.AreaId;

        if (city_id > 0) {
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getAreas);
            url += city_id;

            let requestData = {};
            let onSuccess = function (response) {
                App.Helpers.fileDropdown('area', response, 'Select Area', area_id);
            };

            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    setEnvironmentFromSelector: function (el) {
        environment_id = $(el).attr('value');
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.setEnvironment);
        if (!isVariableEmpty(environment_id)) {
            url += '/' + environment_id;
        }
        let onSuccess = function (response) {
            App.Helpers.refreshDataTable();
        };

        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    setEnvironment: function (environment_id) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.setEnvironment);
        if (!isVariableEmpty(environment_id)) {
            url += '/' + environment_id;
        }
        let onSuccess = function (response) {
            App.Helpers.refreshDataTable();
        };

        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    setStore: function (store_id, redirect) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.setStore);
        url += '/' + store_id;

        let onSuccess = function () {
            if (redirect != '') {
                window.location.href = window.location.href.split('?')[0];
            }
        };

        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    getMerchantEnvironmentInfo: function (environment_id, refresh_page) {
        let onSuccess = function (response) {
            //App.Helpers.storeUrl = response.url;
            if (refresh_page !== undefined) {
                location.reload();
                return false;
            }
        }

        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getEnvironmentInfo);
        url += '/' + environment_id;
        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    getAppsByMerchantId: function (obj, default_value) {

        let merchant_id = $(obj).find(':selected').data('merchant_id');
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getAppsByMerchantId);
        url += merchant_id;
        let requestData = {};

        let onSuccess = function (response) {
            App.Helpers.fileDropdownByKeyValue('env_selector', response, 'All', default_value, 'id', 'env_name');
            $("#wrapper_admin_envs").show();
        };

        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    twoDecimal: function (obj_id, parent_id) {
        let value;
        if (parent_id != undefined) {
            value = $("#" + parent_id + " #" + obj_id).val();
            if (value.indexOf('.') != -1) {
                value = parseFloat(value).toFixed(2)
            }
            $("#" + parent_id + " #" + obj_id).val(value);
        } else {
            value = $("#" + obj_id).val();
            if (value.indexOf('.') != -1) {
                value = parseFloat(value).toFixed(2)
            }
            $("#" + obj_id).val(value);
        }
    },

    showLoader: function () {
        $(".loading").attr('style', 'display:block');
        setTimeout(function () {
            $(".loading").attr('style', 'display:none');
        }, 500);
    },

    copyText: function (id_to_copy_value_from, parent_id, copy_tooltip_id) {
        let copyText;
        let toolTip;
        let input_type;

        if (copy_tooltip_id === undefined) {
            toolTip = $("#copy_tooltip");
        } else {
            toolTip = $("#" + copy_tooltip_id);
        }
        if (parent_id === undefined) {
            copyText = document.getElementById(id_to_copy_value_from);
        } else {
            input_type = $("#" + parent_id + " #" + id_to_copy_value_from).attr('type');
            if (input_type === "password") {
                $("#" + parent_id + " #show_password").removeClass('fa-eye');
                $("#" + parent_id + " #show_password").addClass('fa-eye-slash');
                $("#" + parent_id + " #" + id_to_copy_value_from).attr("type", "text");
            }
            copyText = $('#' + parent_id + ' #' + id_to_copy_value_from).get(0);
        }


        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/
        document.execCommand("copy");

        let originalTitle = toolTip.attr('data-original-title');

        toolTip.attr('title', 'Copied!');
        toolTip.attr('data-original-title', 'Copied!');
        $('[data-toggle="tooltip"]').tooltip();
        toolTip.hide();

        setTimeout(function () {
            toolTip.show();
        }, 100);

        setTimeout(function () {
            toolTip.attr('title', originalTitle);
            toolTip.attr('data-original-title', originalTitle);
            $('[data-toggle="tooltip"]').tooltip();
        }, 500);

    },

    setMaxLength: function (max_length, obj) {

        let obj_value = obj.value;
        if (obj_value.indexOf('.') !== -1) {
            if (obj_value.indexOf('.') == 5) {
                max_length = max_length + 2;
            } else if (obj_value.indexOf('.') > 5) {
                max_length = max_length + 3;
            } else {
                max_length = max_length + 1;
            }
        }

        if (obj.value.length > max_length) {
            obj.value = obj.value.slice(0, max_length)
        }
    },

    removeStartingZero: function (field_id) {
        const field_value = document.getElementById(field_id).value;

        if (field_value.charAt(0) == App.Constants.OFF && field_value.length > 1) {
            $("#" + field_id).val(field_value.substring(1));
        }
    },

    numberOnly: function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    },

    floatType: function (event,obj) {
        if ((event.which != 46 || $(obj).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    },

    bincodeOnly: function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 32 && charCode != 188) {
            return false;
        }
        return true;
    },

    negatvieNumber: function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (evt.target.value.length == 0) {
            if (charCode == 45) {
                return true;
            }

            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
    },

    getGender: function (gender) {
        let customer_gender = '';
        if (gender == 1) {
            customer_gender = 'Male';
        } else if (gender == 0) {
            customer_gender = 'Female';
        }

        return customer_gender;
    },

    togglePassword: function (form_id, input_id) {
        let input_type = $("#" + form_id + " #" + input_id).attr('type');

        if (input_type === "password") {
            $("#" + form_id + " #show_password").removeClass('fa-eye');
            $("#" + form_id + " #show_password").addClass('fa-eye-slash');
            $("#" + form_id + " #" + input_id).attr("type", "text");
        } else {
            $("#" + form_id + " #" + input_id).attr("type", "password");
            $("#" + form_id + " #show_password").removeClass('fa-eye-slash');
            $("#" + form_id + " #show_password").addClass('fa-eye');
        }
    },

    getPhoneInput: function (phoneId, countryCodeId, setCountryCode, countryCode, phoneNumber = "") {
        console.log(phoneId, countryCodeId, setCountryCode, countryCode, phoneNumber)
        var input = document.querySelector("#" + phoneId);
        if (input) {
            let onSuccess = function (response) {
                var iti = intlTelInput(input, {
                    initialCountry: "pk",
                    formatOnDisplay: true,
                    separateDialCode: true,
                    preferredCountries: [],
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js",
                    onlyCountries: response.countries,
                })

                if (setCountryCode && response.selectedCountry) {
                    var code = response.selectedCountry.code
                    iti.setCountry(code.toLowerCase());
                    // App.Helpers.getPhoneMask(iti.getSelectedCountryData().dialCode, phoneId)
                    input.addEventListener("countrychange", function () {
                        if (iti.getSelectedCountryData().name) {
                            let code = iti.getSelectedCountryData().dialCode
                            $('#' + countryCodeId).val(code);
                            $('#' + phoneId).val(phoneNumber);
                            // App.Helpers.getPhoneMask(code, phoneId)
                        }
                    });
                } else {
                    // App.Helpers.getPhoneMask(App.Constants.default_country_code, phoneId);
                    $("#" + countryCodeId).val(App.Constants.default_country_code);
                    input.addEventListener("countrychange", function () {
                        if (iti.getSelectedCountryData().name && !setCountryCode) {
                            let code = iti.getSelectedCountryData().dialCode
                            $('#' + countryCodeId).val(code);
                            $('#' + phoneId).val(phoneNumber);
                            // App.Helpers.getPhoneMask(code, phoneId)
                        }
                    });
                }
            };
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getCountriesData);
            if (setCountryCode) {
                let countryCode = $('#' + countryCodeId).val()
                url += '/' + countryCode
            }
            let requestData = {};
            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);

        }
    },

    validatePhoneNumber: function (thisKey) {
        let number = $(thisKey).val()
        if (number.charAt(0) == App.Constants.OFF && number.length > 1) {
            $(thisKey).val(number.substring(1));
        }
    },

    getPhoneMask: function ($code, phoneId) {
        if ($code > 0) {
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.getPhoneMaskByCode);
            url += $code;

            let requestData = {};
            let onSuccess = function (response) {
                $("#" + phoneId).inputmask({
                    "mask": response.phoneMask,
                    showMaskOnFocus: false,
                    showMaskOnHover: false,
                    jitMasking: true
                });

                const value = App.Helpers.getValueWithoutMasking(response.phoneMask);

                if (value != '' && value != undefined) {

                    $("#" + phoneId).attr('data-mask-length', value.length);

                }
            };

            App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
        }
    },

    getValueWithoutMasking: function (value) {
        value = value.replace(/\-/g, '');
        value = value.replace(/\_/g, '');
        value = value.replace(/\+/g, '');
        value = value.replace(/\|/g, '');
        value = value.replace(/\(/g, '');
        value = value.replace(/\)/g, '');
        return value;
    },

    validatePhoneInput: function (phoneId) {
        // var validationError = {
        //   "IS_POSSIBLE": 0,
        //   "INVALID_COUNTRY_CODE": 1,
        //   "TOO_SHORT": 2,
        //   "TOO_LONG": 3,
        //   "IS_POSSIBLE_LOCAL_ONLY": 4,
        //   "INVALID_LENGTH": 5,
        // };
        var input = document.querySelector('#' + phoneId);
        var iti = intlTelInputGlobals.getInstance(input);
        iti.isValidNumber();
        var error = iti.getValidationError();
        if (error != 0) {
            App.Helpers.showErrorMessage({'error': 'Invalid Phone Number Format'});
        } else {
            return true;
        }
    },

    closeShopifyStoreAlert: function () {
        $('#shopifyStoreAlert').hide();
    },

    saveQrCode: function (filename) {
        html2canvas(document.querySelector("#capture_qr_code")).then(canvas => {
            const image = canvas.toDataURL("image/jpeg");
        const base64Canvas = image.split(";base64,")[1];
        App.Helpers.downloadURI(image, filename + ".png");
    });
    },

    uploadImage: function (parent_id, objId, width, height, disableButtonId = null, maxImgSize = 1) {
        App.Constants.CROPPER_WIDTH = width;
        App.Constants.CROPPER_HEIGHT = height;
        App.Constants.CROPPER_PARENT_ID = parent_id;
        $('#upload-demo').croppie('destroy');

        App.Constants.$uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: width,
                height: height,
            },
            enforceBoundary: false,
            enableExif: true
        });

        const obj = document.getElementById(objId);
        App.Constants.imageId = $('#' + parent_id + ' #' + objId).data('id');
        App.Constants.tempFilename = $('#' + parent_id + ' #' + objId).val();
        let isValid = App.Helpers.validateImageFile(App.Constants.tempFilename)
        if(!isValid){
            App.Helpers.showErrorMessage({'error': 'Invalid Image Format'})
            return false;
        }
        let imageSize = App.Helpers.validateImageSize(obj, maxImgSize)
        if(!imageSize){
            App.Helpers.showErrorMessage({'error': 'Image size cannot be greater than '+maxImgSize+'mb'})
            return false;
        }
        $('#cancelCropBtn').data('id', App.Constants.imageId);
        App.Helpers.readFile($('#' + parent_id + ' #' + objId).get(0));
        if (disableButtonId) {
            $(`#${disableButtonId}`).removeAttr('disabled');
        }
    },

    validateImageFile: function (filename) {
        var extension =    filename.substr(filename.lastIndexOf('.')).toLowerCase();
        let imageValidExtensions = ['.png', '.jpg', '.jpeg', '.svg', '.webp', '.tif', '.tiff', '.bmp', '.raw', '.cr2', '.ico', '.cur'];
        if (imageValidExtensions.includes(extension)){
            return true;
        }
        return false;
    },

    validateImageSize: function (obj, maxImgSize) {
        var size = parseFloat(obj.files[0].size / (1024 * 1024)).toFixed(2);
        if(size > maxImgSize){
          return false;
        }
        return true;
    },

    readFile: function (input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#cropImagePop #upload-demo').addClass('ready');
                $('#cropImagePop').modal('show');
                App.Constants.rawImg = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    },

    cropImage: function (uploadToS3 = false, storeId = null, isProduct = false, isCampaign = false) {
        App.Constants.$uploadCrop.croppie('result', {
            type: 'base64',
            size: {width: App.Constants.CROPPER_WIDTH, height: App.Constants.CROPPER_HEIGHT}
        }).then(function (resp) {
            $('#' + App.Constants.CROPPER_PARENT_ID + ' #item-img-output').attr('src', resp);
            $('#' + App.Constants.CROPPER_PARENT_ID + ' #cropped_image').val(resp);
            $('#cropImagePop').modal('hide');
            $('#brandverseImage').val('');
            if (uploadToS3 == App.Constants.ON) {
                App.MerchantBranding.uploadBrandImages(storeId)
            }
            if(isProduct == App.Constants.ON){
                App.Products.uploadProductImages();
            }
            if(isCampaign == App.Constants.ON){
                App.Campaigns.uploadCampaignImages();
            }
        });
    },

    removeSpace: function (parent_id, obj_id) {
        let str = $("#" + parent_id + " #" + obj_id).val();
        str = str.replace(/\s+/g, '');
        $("#" + parent_id + " #" + obj_id).val(str);
    },

    cloneText: function (parent_id, from_id, to_id) {
        const value = $("#" + parent_id + " #" + from_id).val();
        $("#" + parent_id + " #" + to_id).val(value);
    },

    downloadURI: function (uri, name) {
        let link = document.createElement("a");
        link.download = name;
        link.href = uri;
        document.body.appendChild(link);
        link.click();
        // clearDynamicLink(link);
    },

    uploadAudioFile: function (recorder_id) {
        $("#" + recorder_id).click();
    },

    pauseRecorder: function (player_id) {
        const player = document.getElementById(player_id);
        player.pause();
    },

    removeRecorderFile: function (player_id) {

        if ($("#" + player_id).attr("src") != "") {

            let action = function (isConfirm) {
                if (isConfirm) {
                    const player = document.getElementById(player_id);
                    player.src = '';
                    $("#delete_player").val('1');
                }
            };

            App.Helpers.confirm('You want to remove this file.', action);
        }
    },

    recorder: function (recorder_id, player_id, file_url) {
        const recorder = document.getElementById(recorder_id);
        const player = document.getElementById(player_id);

        if (file_url !== undefined) {
            player.src = file_url;
        }

        recorder.addEventListener('change', function (e) {
            const file = e.target.files[0];
            $("#delete_player").val('0');
            const url = URL.createObjectURL(file);
            player.src = url;
        });
    },

    clearInput: function (thiskey) {
        $(thiskey).val(null)
    },

    removeErrorClasses: function (parentId, thisId) {
        $(`#${parentId} #${thisId}`).removeClass('error');
        $(`#${parentId} #${thisId}-error`).hide();
    },

    resetForm: function (formId) {
        $(`#${formId}`)[0].reset();
        $(`#${formId}`).validate().resetForm();
        $(`#${formId}`).find('.error').removeClass('error');
    },

    filterDoneButton: function(){
        if($(".filterDropDownMenu").hasClass('open')){
            setTimeout(function(){
                $(".filterDropDownMenu").removeClass('open');
            },100);
        }
    },

    filterActivation: function(){
        var select2Element = $('#js-select2');

        select2Element.select2({
            closeOnSelect : false,
            placeholder : "Select",
            allowHtml: true,
            allowClear: true,
            tags: true,
            theme: 'allColSelect2Container',
            /*containerCssClass: "custom-container",
            dropdownCssClass: "custom-dropdown",*/
        });

        $(".filterLink").click(function(){
            $(".filterDropDownMenu").toggleClass("open");
        });
    },

    filterClearButton: function(){
        if($(".filterDropDownMenu").hasClass('open')){
            $(".filterDropDownMenu").removeClass('open');

            $(".customCheckboxContainer .filterCheckbox").each(function(){
                $(this).prop('checked',false);
                var inputValue = $(this).attr('value');
                if (!this.checked) {
                    $("#"+inputValue).val(null).trigger("change");
                }
                $('.'+inputValue).css("display","none");
            });
            // $('#filterColumnsSearch').val('');
        }
        // $("#js-select2").find('option').prop('selected', false);
        // $(".select2-results__options > li").attr('aria-selected',false);
        // $('.SelectColLabel').text('Select');
    },

    filterColumnsSearch: function(){
        inputValue     = $('#filterColumnsSearch').val();
        inputValue     = inputValue.replace(/[^a-zA-Z0-9-.@+/:_() ]/g, "");
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

    isValidUrl: function (url) {
        if (/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(url)) {
            return true;
        } else {
            return false;
        }
    },

    getPercentage: function(number, percentage){
        return (number / 100) * percentage;
    },

    setTextBoxCount: function (parentId, textBoxId, showCountId) {
        const totalCount = $(`#${parentId} #${textBoxId}`).attr('maxLength');
        let currentCount = 0;
        if ($(`#${parentId} #${textBoxId}`).val()) {
            currentCount = $(`#${parentId} #${textBoxId}`).val().length
        }
        $(`#${parentId} #${showCountId}`).html(currentCount + '/' + totalCount)
    },

    setDropDownValues: function(field,parentClass,select2Class){
        $('#'+field).on('select2:open', function () {
            var values = $(this).val();
            var pop_up_selection = $('.select2-results__options');
            if (values != null ) {
                setTimeout(function(){
                    pop_up_selection.find("li[aria-selected=true]").hide();
                },10);
            } else {
                pop_up_selection.find("li[aria-selected=true]").show();
            }
        });

        $('.'+parentClass+' .'+select2Class).keyup(function () {
            var values = $('#'+field).val();
            var pop_up_selection = $('.select2-results__options');
            if (values != null ) {
                setTimeout(function(){
                    pop_up_selection.find("li[aria-selected=true]").hide();
                },10);
            } else {
                pop_up_selection.find("li[aria-selected=true]").show();
            }
        });

    },

    getOrderDetailNotification: function (orderId) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.getOrderNotifications);
        let requestData = {orderId: orderId};
        let onSuccess = function (response) {
            if (response.render_html) {
                $('#order-alert').modal('hide');
                $('.modal-backdrop').remove()
                $('#order-notification').html(response.render_html);
                $('#order-alert').modal('show');
                if(response.isNotificationMuted == App.Constants.OFF){

                    var audio = document.getElementById("myAudio");
                    audio.play().then( function() {
                    } )
                        .catch( function() {
                        } );
                }

                if(App.Constants.INSTANT_MODAL_CLOSING_TIME){
                    setTimeout(function (args) {
                        $('#order-alert').modal('hide');
                    }, parseInt(App.Constants.INSTANT_MODAL_CLOSING_TIME))
                }
            }
        };
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },

    markOrderNotificationSeen: function (orderId, storeId, seenType) {
        if(orderId){
            let url = App.Helpers.generateApiURL(App.Constants.endPoints.markOrderNotificationSeen);
            let requestData = {orderId: orderId, seenType: seenType, storeId: storeId};
            let onSuccess = function (response) {
                if(seenType=='print order'){
                    window.location = '/order/'+orderId+'?action=prints';
                }
                if(seenType=='view order'){
                    window.open('/order/'+orderId, 'blank');
                }
            };
            App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
        }
    },

    toggleMuteNotificationSound: function (thiskey, storeId, orderId) {
        var mute = App.Constants.ON
        if($(thiskey).hasClass('mute')) {
            $(thiskey).removeClass('mute')
            $(thiskey).addClass('un-mute')
            mute = App.Constants.OFF
        }
        else if($(thiskey).hasClass('un-mute')){
            $(thiskey).removeClass('un-mute')
            $(thiskey).addClass('mute')
            mute = App.Constants.ON
        }

        let url = App.Helpers.generateApiURL(App.Constants.endPoints.toggleMuteNotificationSound);
        let requestData = {mute: mute, storeId: storeId, orderId: orderId};
        App.Ajax.post(url, requestData, false, false, {}, 0);

    },

    getUrlParameter: function (sParam) {
        var searchParams = new URLSearchParams(window.location.search)
        return searchParams.get(sParam);
    },

    closeModal: function (modalId) {
        $(modalId).modal('hide');
    },

    customValidationForDropdowns: function (parentId,fieldId) {
        var valid = true;
        for(var i = 0; i < $('.'+parentId).length; i++){
            var field = $('#'+fieldId+i);
            var wrap = field.closest('.select2Wrap');
            var err = wrap.find('.error');
            if(field.length > 0 && field.val() == ''){
                err.show();
                valid = false;
            }else{
                err.hide();
            }
        }
        return valid;
    },

    clearBannerImage: function (parentID, defaultImgPlaceholder) {
        var defaultImgPlaceholder = $("#"+defaultImgPlaceholder).val();
        if($("#"+parentID+" #item-img-output").attr("src") != defaultImgPlaceholder){
            swal({
                    title: "Are you sure to continue?",
                    text: "If you change the banner type, your image will be lost.",
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
                        $("#"+parentID+" .clear-img").attr("src",defaultImgPlaceholder);
                        $("#"+parentID+" #image").val("");
                        $("#"+parentID+" .dlt-store-img").hide();
                    }else{
                        if($('#is_full_banner').val() == App.Constants.OFF){
                            $('#is_full_banner').val(App.Constants.ON);
                        }else{
                            $('#is_full_banner').val(App.Constants.OFF);
                        }
                        $('#is_full_banner').select2();
                    }
                });
        }
        else{
            $("#"+parentID+" .clear-img").attr("src",defaultImgPlaceholder);
            $("#"+parentID+" #image").val("");
        }
    },

    truncateStringFromMiddle:function(str , startLength , stringLength = 10) {
        if (str.length > stringLength) {
            return str.substr(0, startLength) + '...' + str.substr(str.length-20, str.length);
        }
        return str;
    },

    setFileName: function (fileName,parentClass) {
        var label = $('.'+parentClass).find('.custom-file-label');
        var name = document.getElementById(fileName).files[0].name;
        label.text(name);
    },


    clearImage: function (parent_id , thiskey) {
        $("#"+parent_id+" #item-img-output").attr("src",App.Constants.IMAGE_PLACEHOLDER);
        $(thiskey).val(null)
    },

    truncate: function (fullStr, strLen, separator) {
        if (fullStr.length <= strLen) return fullStr;

        separator = separator || ' ... ';

        var sepLen = separator.length,
            charsToShow = strLen - sepLen,
            frontChars = Math.ceil(charsToShow/2),
            backChars = Math.floor(charsToShow/2);

        return fullStr.substr(0, frontChars) +
            separator +
            fullStr.substr(fullStr.length - backChars);
    },

    arrayRemove: function (arr, value) {
        return arr.filter(function (ele) {
          return ele != value;
        });
    },

    fileUploadingError: function(msg){
        App.Helpers.showErrorMessage({'error': msg});
        $('.upload-file-btn').attr('disabled',true);
        $('#messages').html('');
        App.Helpers.uploadedFile = null;
    },


        initFileDrager: function () {
        var fileSelect = document.getElementById('file-upload'),
        fileDrag = document.getElementById('file-drag');
        fileSelect.addEventListener('change', App.Helpers.fileSelectHandler, false);
        fileDrag.addEventListener('dragover', App.Helpers.fileDragHover, false);
        fileDrag.addEventListener('dragleave', App.Helpers.fileDragHover, false);
        fileDrag.addEventListener('drop', App.Helpers.fileSelectHandler, false);
    },

    fileDragHover: function (e) {
      var fileDrag = document.getElementById('file-drag');
      e.stopPropagation();
      e.preventDefault();
      fileDrag.className = (e.type === 'dragover' ? 'hover' : 'modal-body file-upload');
    },

    fileSelectHandler: function (e) {
      // Fetch FileList object
      var files = e.target.files || e.dataTransfer.files;

      // Cancel event and hover styling
      App.Helpers.fileDragHover(e);

        if(files.length > 1) {
            App.Helpers.fileUploadingError('Only one csv file allowed');
            return false;
        }

        // Process all File objects
      for (var i = 0, f; f = files[i]; i++) {
        App.Helpers.uploadedFile = f;
        var fileName = f.name.split('.');

        if(fileName[1] != 'csv') {
            App.Helpers.fileUploadingError('File type must be csv');
            return false;
        }

        App.Helpers.parseFileReadByDragInput(f);
        $('.upload-file-btn').removeAttr('disabled');
        }
    },

    outputFileReadByDragInput: function (msg) {
        var m = document.getElementById('messages');
        m.innerHTML = msg;
    },

    parseFileReadByDragInput: function (file) {
        App.Helpers.outputFileReadByDragInput(
          '<ul>'
          + '<li>Name: <strong>' + encodeURI(file.name.replaceAll(" ","-")) + '</strong></li>'
          + '</ul>'
        );
      },

    updateGoogleAutocomplete: function(countryCode){
        var options = {
            componentRestrictions: {country: countryCode}
        };
        if (App.Constants.googleAutoComplete) {
            App.Constants.googleAutoComplete.setOptions(options);
        }
    } ,

    removeGoogleInstance: function(){
        google.maps.event.removeListener(App.Constants.googleAutoCompleteLsr);
        google.maps.event.clearInstanceListeners(App.Constants.googleAutoComplete);
        $(".pac-container").remove();
    },

    allowNumbersOnlyInField:function( element , parent_id , event){
        const discount_type = $("#"+parent_id).find("option:selected").val();
        var value = $(element).val();

        if (discount_type != App.Constants.ON && value >= 100) {
            $(element).val(99);
        }

        if((event.which == 48 || event.which == 96) && element.value.length == 1 ){
            $(element).val('');
        }
    },

    arrayFlip: function( trans ){
        var key, tmp_ar = {};
        for ( key in trans ){
            if ( trans.hasOwnProperty( key ) ){
                tmp_ar[trans[key]] = key;
            }
        }
        return tmp_ar;
    },

}
