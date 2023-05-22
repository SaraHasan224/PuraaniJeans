App.Auth = {
  showForm: function (form1, form2) {

      const email = $(form1 + ' #email').val();
      $(form2 + ' #email').val(email);
      $(form1).toggle();
      $(form2).toggle();

  },

  sendPasswordResetRequest: function (id) {
    let text = "You want to send reset password link?";

    let action = function (isConfirm) {
      if (isConfirm) {
        let url = App.Helpers.generateApiURL(App.Constants.endPoints.sendPasswordResetLink);
        let requestData = {user_id: id};

        App.Ajax.post(url, requestData, false, false, {});
      }
    };
    App.Helpers.confirm(text, action);
  },

  showPasswordFields: function () {
    $('#password_section').toggle();
      $('#profile input#previous_password').val('');
      $('#profile input#password').val('');
      $('#profile input#password_confirmation').val('');
  },

  sendChangeEmail: function () {
    if ($("#change_email_form").valid()) {
      let onSuccess = function (response) {
        $('#change_email_form #otp').toggle();
        $('#change_email_form #send_change_email').hide();
        $('#change_email_form #save_change_email').show();
        let otpResendTime = response.otpResendTime;
        App.Auth.setTimer(otpResendTime*60, 'change_email_form')
        setTimeout(function () {
          $('#change_email_form #resend_otp').show();
        }, otpResendTime*60000)
      };

      let url = App.Helpers.generateApiURL(App.Constants.endPoints.sendChangeEmail);

      let requestData = $('#change_email_form').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);

    }
  },

  sendChangePhoneNumber: function () {
    if ($("#change_phone_number_form").valid() && App.Helpers.validatePhoneInput('change_phone')) {
      let onSuccess = function (response) {
        $('#change_phone_number_form #otp_phone_number').toggle();
        $('#change_phone_number_form #send_change_phone').hide();
        $('#change_phone_number_form #save_change_phone').show();
        let otpResendTime = response.otpResendTime;
        App.Auth.setTimer(otpResendTime*60, 'change_phone_number_form')
        setTimeout(function () {
          $('#change_phone_number_form #resend_otp').show();
        }, otpResendTime*60000)
      };

      let url = App.Helpers.generateApiURL(App.Constants.endPoints.sendChangePhoneNumber);
      let requestData = $('#change_phone_number_form').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    }
  },

  updateEmail: function () {
    if ($("#change_email_form").valid()) {
      let onSuccess = function (response) {
        const newEmail = $('#change_email_form #email').val();
        $('#profile #email').val(newEmail)
        $('.change-email-modal').modal('hide');
        $('.logoutBtn').click();
      };

      let url = App.Helpers.generateApiURL(App.Constants.endPoints.updateEmail);

      let requestData = $('#change_email_form').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    }
  },

  updatePhoneNumber: function () {
    if ($("#change_phone_number_form").valid()) {
      let onSuccess = function (response) {
        const newPhone = $('#change_phone_number_form #change_phone').val();
        $('#profile #profile_phone').val(newPhone)
        $('.change-email-modal').modal('hide');
        $('.logoutBtn').click();
      };

      let url = App.Helpers.generateApiURL(App.Constants.endPoints.updatePhone);

      let requestData = $('#change_phone_number_form').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    }
  },

  saveProfileForm: function () {
    if ($("#profile").valid()) {
      let url = App.Helpers.generateApiURL(App.Constants.endPoints.profile);
      let onSuccess = function (response) {
        if($('#profile #change_password_checkbox').prop("checked")){
          $('.logoutBtn').click();
        }
      }
      let requestData = $('#profile').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    }
  },

  resendCode: function (userId, action, formClass) {
    let onSuccess = function (response) {
      $('#'+formClass+' #resend_otp').hide();
      let otpResendTime = response.otpResendTime;
      App.Auth.setTimer(otpResendTime*60, formClass)
      setTimeout(function () {
        $('#'+formClass+' #resend_otp').show();
      }, otpResendTime*60000)
    };

    let url = App.Helpers.generateApiURL(App.Constants.endPoints.codeResend);
    url += '/'+userId;

    let email = action == 'update_email'? $('#change_email_form #email').val() : null
    let phone = action == 'update_phone'? $('#change_phone_number_form #country_code').val() +''+ $('#change_phone_number_form #phone').val() : null
    let requestData = {'user_id': userId ,'action': action, 'email_event': action, 'email': email, 'phone': phone};
    App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
},

  setTimer: function (seconds, formClass) {
    $('#'+formClass+' #otp_error').html('');
    seconds--;
    setInterval(function () {
      if (seconds < 0) {
        return;
      }
      $('#'+formClass+' #otp_error').html('You can resend OTP after ' + seconds-- + ' seconds');
    }, 1000);
  },

  signup: function () {
    if ($("#sign-up-form").valid() &&  App.Helpers.validatePhoneInput('phone')) {
      let url = App.Helpers.generateApiURL(App.Constants.endPoints.signup);
      let onSuccess = function (response) {
        window.location = response.url
      }
      let requestData = $('#sign-up-form').serialize();
      App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
    }
  },

  bindSignupForm: function () {
    $('#sign-up-form').keypress(function (e){
      code = e.keyCode ? e.keyCode : e.which;
      if(code.toString() == 13)
      {
        e.preventDefault();
        App.Auth.signup()
      }
    })
  },

    toggleNoWebsiteField: function (thisKey) {
        if($(thisKey).is(":checked")){
            $('#sign-up-form #domain').removeAttr('required')
            $('#sign-up-form #domain').attr('readonly', 'readonly')
        }else {
            $('#sign-up-form #domain').attr('required', 'required')
            $('#sign-up-form #domain').removeAttr('readonly')
        }

    },
    loginAgencyById: function (userId) {
        let onSuccess = function (response) {
            if(response.status){
                window.location = "/";
            }
        };
        let url = App.Helpers.generateApiURL(
            App.Constants.endPoints.loginAgencyAccount
        );
        let requestData = {'userId' : userId};
        App.Ajax.post(url, requestData, onSuccess, false, {});
    },

    verifyAgencyPasswordPopup: function (userId) {
        $('#user_id').val(userId);
        $('#verify_agency_password_form #agency_password').val('');
        $('.agency-password-check').modal('show');
    },

    verifyAgencyPassword: function () {
        if ($("#verify_agency_password_form").valid()) {
            let onSuccess = function (response) {
                setTimeout(function(){
                    $('.agency-password-check').modal('hide');
                    App.Merchant.loginMerchantAccount(response.user_id);
                },3000);
            };

            let url = App.Helpers.generateApiURL(App.Constants.endPoints.verifyAgencyPassword);
            let requestData = $('#verify_agency_password_form').serialize();
            App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
        }
    },

}
