App.Ajax = {
    submitForm: function (formId, onSuccess, onFailure, formClass = false) {

        let options = {
                cache: false,
                headers: [],
                beforeSend: function () {
            },

            success: function (data) {
                // swal.close();
                App.Helpers.showSuccessMessage(  data.message );
                if( onSuccess )
                {
                    onSuccess(data.body);
                }
            },

            error: function (data) {
                // swal.close();
                let response = xhr.responseJSON;
                if( response === undefined)
                {
                    response = {'error':'Something went wrong, please try again'};
                }
                else
                {
                    if( response.message === '')
                    {
                        response = {'error':'Something went wrong, please try again'};
                    }
                    else
                    {
                        response = {'error': response.message};
                    }
                }

                App.Helpers.showErrorMessage( response );

                if( onFailure )
                {
                    onFailure();
                }
            }
        };

        if (formClass) {
            $('.' + formId).ajaxForm(options);
        } else {
            $('#' + formId).ajaxForm(options);
        }
    },
    get: function (endPoint, data, onSuccess, onFailure, additionalOptions) {
        App.Ajax.__send("GET", endPoint, data, onSuccess, onFailure, additionalOptions);
    },

     post: function (endPoint, data, onSuccess, onFailure, additionalOptions) {
        App.Ajax.__send("POST", endPoint, data, onSuccess, onFailure, additionalOptions);
    },

    delete: function (endPoint, data, onSuccess, onFailure, $apiRoute, additionalOptions) {
        App.Ajax.__send("DELETE", endPoint, data, onSuccess, onFailure, additionalOptions);
    },

    __send: function (method, endPoint, data, onSuccess, onFailure, additionalOptions) {
        // console.log(arguments.callee.caller.toString())
        // console.log("Ajax send: ", method, endPoint, data, onSuccess, onFailure, additionalOptions)
        let headers = {
            'X-CSRF-Token': App.Constants.CSRF_TOKEN
        };

        let options = {
            url: endPoint,
            type: method,
            data: data,
            headers: headers,
            contentType: 'application/x-www-form-urlencoded',

            beforeSend: function () {
              if(additionalOptions != 'hide_message_with_loader'){
                $(".loading").attr('style', 'display:block');
              }
            },

            success: function (data, textStatus, xhr) {
                $(".loading").attr('style', 'display:none');
                // swal.close();

                // show api failure messages on modal
                var apiFailureResponse = 0;
                // if(data != '' && data.body.apiFailureMessage != undefined && data.body.apiFailureMessage == true && additionalOptions == 'hide_message_open_modal') {
                //     apiFailureResponse = 1;
                // }

                if( method !== "GET" && additionalOptions != 'hide_message' && additionalOptions != 'hide_message_with_loader' && apiFailureResponse == 0)
                {
                    App.Helpers.showSuccessMessage( data.message );
                }

                if( onSuccess )
                {
                    if(additionalOptions == 'ignoreSuccessFormatting'){
                        console.log("custom body",data )
                        onSuccess(data);
                    }else {
                        console.log("body",data.body )
                        onSuccess(data.body);
                    }
                }
            },

            error: function (xhr, textStatus, errorThrown) {
              $(".loading").attr('style', 'display:none');
                // swal.close();
                let response = xhr.responseJSON;
                console.log("response xhr: ", xhr)
                console.log("response error: ", response)
                if( response === undefined || response.message === '' )
                {
                    response = {'error':'Something went wrong, please try again'};
                }
                else
                {
                        if(response.body.length > 0 ){
                          response = {'error': response.body};
                        }
                        else {
                          response = {'error': response.message};
                        }
                }

                App.Helpers.showErrorMessage( response );

                if( onFailure )
                {
                    onFailure();
                }
            },

            complete: function (response) {
                if(additionalOptions != 'hide_message_with_loader') {
                    $(".loading").attr('style', 'display:none');
                }
            }
        };

        if (additionalOptions == 'upload_file') {
            options.processData = false;
            options.contentType = false;
            options.cache = false;
        }

        $.ajax(options);
    }
}
