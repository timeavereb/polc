/**
 * Created by Pali on 2017. 12. 08..
 */

function polc_email_notification_handler() {

    var self = this;

    jQuery("#confirmationDialog").dialog({
        autoOpen: false,
        resizeable: false,
        modal: true,
        buttons: [
            {
                text: "Küldés",
                click: function () {
                    jQuery(".plcSendEmailNotification").prop("disabled", true);
                    self.send_email();
                    jQuery(this).dialog("close");
                }
            },
            {
                text: "Mégse",
                click: function () {
                    jQuery(this).dialog("close");
                }
            }
        ]
    });

    this.send_email = function () {

        jQuery("#plc_acceptance").val(tinyMCE.get("plc_acceptance").getContent());
        jQuery("#plc_rejection").val(tinyMCE.get("plc_rejection").getContent());

        var form_id = jQuery("#plcAcceptanceWrapper").is(":visible") ? "#plcAcceptanceForm" : "#plcRejectionForm";

        jQuery.ajax({
            url: "/wp-content/themes/polc/includes/modules/email-notification-module.php",
            type: "POST",
            data: {
                data: jQuery(form_id + " input, " + form_id + " textarea").serialize()
            },
            success: function (response) {

                jQuery(".plcSendEmailNotification").prop("disabled", false);

                if (response.hasOwnProperty("error")) {
                    alert("Hiba történt!");
                    console.log(response.error);
                    return false;
                }
                alert("Levél kiküldve!");
            }
        });
    };

    jQuery(document).on("click", ".plcSendEmailNotification", function (e) {
        e.preventDefault();
        jQuery("#confirmationDialog").dialog("open");
    });
}