/**
 * Created by Pali on 2017. 06. 11..
 */

function polc_author_handler() {

    var self = this;

    this.validate_data_change = function () {

        var params = {};
        params.rules = {};
        params.messages = {};

        /*params.rules = {
            'plc_user_display_name': "required"
        };

        params.messages = {
            'plc_user_display_name': "A név kötelező!"
        };*/

        var $form = jQuery("#plcDataChangeForm");
        $form.validate(params);
        return $form.valid();
    };

    this.validate_password_change = function () {

        var params = {};

        params.rules = {
            'plc_old_password': "required",
            'plc_new_password': {required : true, minlength: 3},
            'plc_new_password_conf': {equalTo: jQuery("#plc_new_password")}
        };

        params.messages = {
            'plc_old_password': "A jelszó megadása kötelező!",
            'plc_new_password': "Az jelszónak legalább 6 karakter hosszúnak kell lennie!",
            'plc_new_password_conf': "A két jelszónak azonosnak kell lennie!"
        };

        var $form = jQuery("#plcPasswordChangeForm");
        $form.validate(params);
        return $form.valid();
    };

    jQuery(document).ready(function () {

        jQuery("#plc_user_birth_date").datepicker(polc_header_handler.datepicker_config());

        jQuery("#plcUserDataSaveBtn").click(function () {

            if (!self.validate_data_change()) {
                return false;
            }

            if (jQuery("#plc_new_password").val() != "") {
                console.log("validate pw");
                if (!self.validate_password_change()) {
                    return false;
                }
            }

            var params = jQuery("#plcDataChangeForm, #plcPasswordChangeForm").serialize();

            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/data-change-module.php",
                type: "POST",
                data: params,
                success: function (response) {

                    if (response.hasOwnProperty("error")) {
                        jQuery.event.trigger("polc_alert", {title: "Figyelmeztetés", msg: response.error});
                        return false;
                    }

                    jQuery.event.trigger("polc_alert", {title: "Siker", msg: response.message});
                }
            });
        });
    });

    //Add to favorite
    jQuery(".addFavouriteUser").click(function () {
        jQuery.ajax({
            url: "/wp-content/themes/polc/includes/modules/favorite-subscribe-module.php",
            method: "POST",
            data: {
                action: "favorite",
                mode: "author",
                obj_id: jQuery(".addFavouriteUser").attr("data-author-id")
            },
            success: function (response) {
                if (response.error) {
                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                    return false;
                }
                if (response.success) {
                    jQuery("#addFavouriteText").fadeOut(500, function () {
                        jQuery(this).text(response.success).fadeIn(500);
                    });
                }
            }
        });
    });

    //Upload image
    jQuery(".changeImage").click(function () {
        jQuery("#file").click();
    });

    jQuery("#file").change(function () {
        var ext = jQuery('#file').val().split('.').pop().toLowerCase();
        if (jQuery.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
            jQuery.event.trigger("polc_alert", {title: "Hiba", msg: "Nem kép formátumot válaszottál ki!"});
            return false;
        } else {
            jQuery("#uploadimage").submit();
        }
    });

    jQuery("#uploadimage").on('submit', function (e) {
        e.preventDefault();
        jQuery("#message").empty();
        jQuery('#loading').show();
        jQuery.ajax({
            url: "/wp-content/themes/polc/includes/modules/upload-avatar.php",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.error) {
                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: data.error});
                }
                if (data) {
                    jQuery(".plcUserImage").css("background-image", 'url(\'' + data.src + '\')')
                }
            }
        });
    });

    jQuery(".addStory").click(function () {
        jQuery("#addStoryPopup").dialog("open");
    });

    jQuery("#addStoryPopup").dialog({
        closeOnEscape: true,
        resizable: false,
        autoOpen: false,
        modal: true,
        open: function () {
            jQuery("#content-category").val(0);
            jQuery("#content-subcategory").val(0);
            jQuery("#content-genre").val(0);
            jQuery(".storySubCategory, .storyGenre, .submit").hide();
        }
    });

    jQuery("#addStoryPopup").siblings('div.ui-dialog-titlebar').remove();

    jQuery("#content-category").change(function () {
        if (jQuery(this).val() == 0) {
            jQuery("#content-subcategory").val(0);
            jQuery("#content-genre").val(0);
            jQuery(".storySubCategory").hide();
            jQuery(".submit").hide();
            jQuery(".storyGenre").hide();
        } else {
            if (jQuery("#content-category option:selected").text() == "Original") {
                jQuery(".storySubCategory").hide();
                jQuery(".storyGenre").show();
            } else {
                jQuery(".storySubCategory").show();
            }
        }
    });

    jQuery("#content-subcategory").change(function () {
        if (jQuery(this).val() == 0) {
            jQuery(".storyGenre").hide();
            jQuery(".submit").hide();
            jQuery("#content-genre").val(0);
        } else {
            jQuery(".storyGenre").show();
        }
    });

    jQuery("#content-genre").change(function () {
        if (jQuery(this).val() == 0) {
            jQuery(".submit").hide();
        } else {
            jQuery(".submit").show();
        }
    });
}