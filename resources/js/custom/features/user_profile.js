App.UserProfile = {
    viewStoreModal: function(){
        $(".view-stores").modal('hide');
    },
    viewStoresInModal: function (element, action) {
        var data = $(element).attr('text');
        var modelTitle = $(element).attr('title');
        var modelSubmitBtnTheme = $(element).attr('submitTheme');
        var modelSubmitBtnText = $(element).attr('submitText');

        $("#customModalWrapperLabel").html(modelTitle);

        var initialDiv = $("#customModalWrapper div.modal-dialog div.modal-content div.modal-body");
        initialDiv.empty();
        initialDiv.append(data);

        var footerSubmitDiv = $("#customModalWrapper").children().children().children('.modal-footer').children('#customModalWrapperSubmitBtn');
        footerSubmitDiv.empty();
        footerSubmitDiv.append(modelSubmitBtnText);
        footerSubmitDiv.removeClass("btn-primary");
        footerSubmitDiv.addClass(modelSubmitBtnTheme);
        footerSubmitDiv.children('.submitModelSuccess').attr('onClick',App.UserProfile.deleteUserAccount(this));
    },
    deleteUserAccount: function () {
        let orderId = $(this).attr("data-order-id");

        let onSuccess = function (response) {
            var initialDiv = $("#customModalWrapper div.modal-dialog div.modal-content div.modal-body");
            initialDiv.empty();
            // $("#order_accountings_modal_wrapper").html(response);
            initialDiv.modal('hide');
        };

        let url = App.Helpers.generateApiURL(App.Constants.endPoints.deleteUserAccount);

        let requestData = {};
        App.Ajax.get(url, requestData, onSuccess, false, {}, 0);
    },


}
