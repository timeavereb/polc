/**
 * Created by Pali on 2017. 08. 26..
 */

function polc_comment_handler(params) {

    var self = this,
        settings = params;

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
                    parent : settings.parent,
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

        if(params.content.length == 0){
            return false;
        }

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

                if (response.hasOwnProperty("error")) {
                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                    return false;
                }

                self.load_comments();
            }
        });
    };

    this.spinner = function () {
        return '<img src="/wp-admin/images/loading.gif">';
    };

    jQuery(document).ready(function () {

        //New comment
        jQuery(document).on("click", "#plcSendComment", function(){
            var params = {};
            params.content = jQuery("#plcCommentContent").val();
            params.parent = null;
            if(!self.send_comment(params)){
                return false;
            }
            jQuery("#plcCommentContent").val("");
            self.load_comments();
        });

        //On reply show reply textarea
        jQuery(document).on("click", ".plcCommentReplyBtn", function () {
            jQuery(this).hide();
            jQuery(this).next().show();
        });

        //Send comment on keypress
        jQuery(document).on("keyup", ".plcReplyText", function (e) {
            if ((e.keyCode || e.which) == 13) {
                var params = {};
                params.parent = jQuery(this).attr("data-id");
                params.content = jQuery(this).val();
                self.send_comment(params);
                jQuery(this).hide();
            }
        });
    });
}