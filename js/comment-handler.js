/**
 * Created by Pali on 2017. 08. 26..
 */

function polc_comment_handler(params) {

    var self = this,
        settings = params,
        sending = false;

    this.load_comments = function () {

        if (typeof settings == "undefined" || !settings.hasOwnProperty("id")) {
            return false;
        }

        jQuery(".plcCommentListWrapper").html(self.spinner());

        jQuery.ajax({
            url: "/wp-content/themes/polc/includes/modules/comment-module.php",
            type: "POST",
            data: {
                action: "get_comments",
                params: {
                    post_id: settings.id,
                    number: settings.number,
                    parent: settings.parent,
                    author: settings.author
                }
            },
            success: function (response) {

                if (response.hasOwnProperty("error")) {
                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                    return false;
                }

                jQuery(".plcCommentListWrapper").html(response);
            }
        });
    };

    this.send_comment = function (params) {

        if (params.content.length == 0 || sending == true) {
            return false;
        }

        sending = true;

        jQuery.ajax({
            url: "/wp-content/themes/polc/includes/modules/comment-module.php",
            type: "POST",
            data: {
                action: "add_comment",
                params: {
                    comment_post_ID: settings.id,
                    comment_parent: params.parent,
                    comment_content: params.content
                }
            },
            success: function (response) {

                sending = false;
                jQuery("#plcCommentContent").val("");

                if (response.hasOwnProperty("error")) {
                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                    return false;
                }

                self.load_comments();
            }
        }).always(function () {
            sending = false;
        });
    };

    this.spinner = function () {
        return '<img src="/wp-admin/images/loading.gif">';
    };

    jQuery(document).ready(function () {

        //New comment
        jQuery(document).on("click", "#plcSendComment", function () {
            var params = {};
            params.content = jQuery("#plcCommentContent").val();
            params.parent = null;
            if (!self.send_comment(params)) {
                return false;
            }

            self.load_comments();
        });

        //On reply show reply textarea
        jQuery(document).on("click", ".plcCommentReplyBtn", function () {
            jQuery(this).hide();
            jQuery(this).next().show();
        });

        //Send comment on keypress
        jQuery(document).on("click", ".plcSendComment", function (e) {
            var textarea = jQuery(this).prev();
            var params = {};
            params.parent = jQuery(textarea).attr("data-id");
            params.content = jQuery(textarea).val();
            self.send_comment(params);
            jQuery(textarea).hide();
        });
    });
}