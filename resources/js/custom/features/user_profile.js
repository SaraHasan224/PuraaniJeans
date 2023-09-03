App.UserProfile = {
    viewStoreModal: function(){
        $(".view-stores").modal('hide');
    },
    deleteAccount: function (element, action) {
        // var data = $(element).attr('text');
        // var modelTitle = $(element).attr('title');
        // var modelSubmitBtnTheme = $(element).attr('submitTheme');
        // var modelSubmitBtnText = $(element).attr('submitText');
        let authId = $(element).attr("authId");
        swal({
                title: "Are you sure to delete your account?",
                text: "Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    let onSuccess = function (response) {

                    };

                    let url = App.Helpers.generateApiURL(App.Constants.endPoints.deleteUserAccount);
                    let requestData = {id: authId};
                    console.log("requestdaa: " , requestData)
                    App.Ajax.post(url, requestData, onSuccess, false, {}, 0);
                }else{
                }
            });
        // $("#customModalWrapperLabel").html(modelTitle);

        // var initialDiv = $("#customModalWrapper div.modal-dialog div.modal-content div.modal-body");
        // initialDiv.empty();
        // initialDiv.append(data);
        //
        // var footerSubmitDiv = $("#customModalWrapper").children().children().children('.modal-footer').children('#customModalWrapperSubmitBtn');
        // footerSubmitDiv.empty();
        // footerSubmitDiv.append(modelSubmitBtnText);
        // footerSubmitDiv.removeClass("btn-primary");
        // footerSubmitDiv.addClass(modelSubmitBtnTheme);
        // footerSubmitDiv.children('.submitModelSuccess').attr('onClick',App.UserProfile.deleteUserAccount(authId));
    },
}
