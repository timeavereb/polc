/**
 * Header handler js.
 */
function polc_header_handler() {

    var self = this,
        animation_wrapper = "";

    /**
     * Registration form validation.
     * @returns {*}
     */
    this.validate_register = function () {

        jQuery("#plc_reg_form").validate({
            rules: {
                username: "required",
                email: {
                    required: true,
                    email: true
                },
                'email-conf': {
                    equalTo: jQuery("#email")
                },
                password: {
                    required: true,
                    minlength: 6
                },
                'password-conf': {
                    equalTo: jQuery("#password")
                },
                terms: {
                    required: true
                }
            },
            messages: {
                username: "Kérlek add meg a felhasználóneved!",
                email: "Kérlek add meg az e-mail címed!",
                password: {
                    required: "Kérlek add meg a jelszavad!",
                    minlength: "A jelszavadnak legalább 6 karakter hosszúnak kell lennie!"
                },
                'email-conf': {
                    equalTo: "A két e-mail cím nem egyezik!"
                },
                'password-conf': {
                    equalTo: "A két jelszó nem egyezik!"
                },
                terms: {
                    required: "Nem fogadtad ez az adatkezelési nyilatkozatot!"
                }
            }
        });

        return jQuery("#plc_reg_form").valid();
    };

    /**
     * Login form validation.
     * @returns {*}
     */
    this.validate_login = function () {

        jQuery("#plc_login_form").validate({
            rules: {
                login: "required",
                password: "required"
            },
            messages: {
                login: "A bejelentkezéshez meg kell adnod az e-mail címed vagy a felhasználóneved!",
                password: "A bejelentkezéshez meg kell adnod a jelszavad!"
            }
        });

        return jQuery("#plc_login_form").valid();
    };

    /**
     * Lost password form validation.
     * @returns {*}
     */
    this.validate_lost_password = function () {

        jQuery("#plc_lost_password_form").validate({
            rules: {
                lost_password_login: "required",
                email: "required"
            },
            messages: {
                lost_password_login: "A jelszavad helyreállításához meg kell adnod az e-mail címed vagy a felhaszálóneved!",
            }
        });

        return jQuery("#plc_lost_password_form").valid();
    };

    /**
     * Reset password form validation.
     * @returns {*}
     */
    this.validate_reset_password = function () {

        jQuery("#plc_password_reset_form").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 5
                },
                'password-conf': {
                    equalTo: jQuery("#password")
                }
            },
            messages: {
                password: {
                    required: "Kérlek add meg az új jelszavad!",
                    minlength: "A jelszavadnak legalább 5 karakter hosszúnak kell lennie!"
                },
                'password-conf': {
                    equalTo: "A két jelszó nem egyezik!"
                }
            }
        });

        return jQuery("#plc_password_reset_form").valid();
    };

    this.change_section = function (id) {

        jQuery(".section").hide();
        jQuery("#section_" + id).show();

        jQuery(".section_btn").removeClass("active");
        jQuery("#section_" + id + "_btn").addClass("active");
    };

    /**
     * Add animation class to listers.
     */
    this.isScrolledIntoView = function () {

        jQuery(animation_wrapper).each(function () {
            var elementTopPosition = jQuery(this).position().top + 100;
            var totalHeight = jQuery(window).height();
            var topPosition = jQuery(window).scrollTop();
            var bottomOfScreen = topPosition + totalHeight;
            if (bottomOfScreen >= elementTopPosition) {
                jQuery(this).addClass("polc-load");
            }
        });
    };

    /**
     * Datepicker config.
     * @returns {{clearText: string, clearStatus: string, closeText: string, prevText: string, nextText: string, currentText: string, monthNames: string[], monthNamesShort: string[], dayNames: string[], dayNamesShort: string[], dayNamesMin: string[], dateFormat: string, firstDay: number, changeYear: boolean, changeMonth: boolean, yearRange: string}}
     */
    this.datepicker_config = function () {

        return {
            clearText: 'Effacer',
            clearStatus: '',
            closeText: 'Bezárás',
            prevText: 'Előző',
            nextText: 'Következő',
            currentText: 'Mostani',
            monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
            monthNamesShort: ['Jan', 'Feb', 'Márc', 'Apr', 'Máj', 'Jún', 'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
            dayNames: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'CSütörtök', 'Péntek', 'Szombat'],
            dayNamesShort: ['V', 'H', 'K', 'Sze', 'Cs', 'p', 'Szo'],
            dayNamesMin: ['V', 'H', 'K', 'Sze', 'Cs', 'p', 'Szo'],
            dateFormat: 'yy.mm.dd.',
            firstDay: 1,
            changeYear: true,
            changeMonth: true,
            yearRange: "-110:+0"
        };
    };

    /**
     * Global entry point.
     */
    jQuery(document).ready(function () {

        //navigation
        jQuery('.plc_side_naviagtion').click(function () {
            jQuery(this).toggleClass('opened');
        });

        jQuery(document).keypress(function (event) {
            if (jQuery("#plc_login_popup").is(":visible") && event.keyCode == 13) {
                event.preventDefault();
                jQuery("#plc_login_btn").click();
            }
        });

        animation_wrapper = jQuery(document).find(".animate");

        console.log(animation_wrapper);

        self.isScrolledIntoView();

        jQuery(document).on("scroll", function () {
            self.isScrolledIntoView();
        });

        /****************************
         *  Register events
         * ************************/

        /**
         * Registration popup init.
         */
        jQuery("#plc_reg_popup").dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            closeOnEscape: true,
            resizable: false
        });

        /**
         * Register open
         */
        jQuery(".signup").click(function () {
            jQuery("#plc_reg_popup").dialog("open");
            jQuery("#plc_login_popup").dialog("close");
        });

        jQuery(".quit").click(function () {
            jQuery(this).parent().dialog("close");
        });

        /**
         * Init registration
         */
        jQuery("#plc_register_btn").click(function (event) {

            event.preventDefault();

            //Hide user errors
            jQuery(".plcErrorText").hide();
            jQuery(".plcErrorText").text("");

            //Init register form validation
            if (!self.validate_register()) {
                return false;
            }

            //If everything is okay at this point, we call the register module.
            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/register-module.php",
                method: "POST",
                data: {
                    'username': jQuery("#plc_reg_popup input[name=\"username\"]").val(),
                    'email': jQuery("#plc_reg_popup input[name=\"email\"]").val(),
                    'password': jQuery("#plc_reg_popup input[name=\"password\"]").val(),
                    'password-conf': jQuery("#plc_reg_popup input[name=\"password-conf\"]").val(),
                    'email-conf': jQuery("#plc_reg_popup input[name=\"email-conf\"]").val()
                },
                success: function (response) {
                    //If there any error we display it to the user.
                    if (response.error) {
                        if (response.error) {
                            jQuery.each(response.error, function (k, v) {
                                console.log(k);
                                jQuery.each(v, function (key, value) {
                                    jQuery("#" + k + "-error-msg").show();
                                    jQuery("#" + k + "-error-msg").append(value + "<br>");
                                });
                            });
                        }
                    }

                    if (response.success) {
                        jQuery.event.trigger("polc_alert", {title: "Siker", msg: response.success});
                    }

                    //If everything is okay at this point, the registration was successful.
                    else {
                        jQuery("#plc_reg_popup").dialog("close");
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });


        /****************************
         **Login events
         ***********************/

        /**
         *Login popup init.
         */
        jQuery("#plc_login_popup").dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            closeOnEscape: true,
            resizable: false
        });

        /**
         *Login open
         */
        jQuery(".login").click(function () {
            jQuery("#plc_reg_popup").dialog("close");
            jQuery("#plc_login_popup").dialog("open");
        });

        /**
         * Init login.
         */
        jQuery("#plc_login_btn").click(function (event) {

            event.preventDefault();

            //Init login form validation
            if (!self.validate_login()) {
                return false;
            }

            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/login-module.php",
                method: "POST",
                data: {
                    'login': jQuery("#plc_login_popup input[name=\"login\"]").val(),
                    'password': jQuery("#plc_login_popup  input[name=\"password\"]").val(),
                },
                success: function (response) {

                    if (response.error) {
                        jQuery.event.trigger("polc_alert", {msg: response.error, title: "Hiba"});
                        return false;
                    }

                    if (response.data) {
                        location.reload();
                    }
                }
            });
        });

        /****************************
         **Logout events
         ***********************/

        /**
         * Logout event.
         */
        jQuery("#plc_logout").click(function () {
            event.preventDefault();

            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/logout-module.php",
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });

        /****************************
         **Lost Password event
         ***********************/

        jQuery("#plc_lost_password_btn").click(function (event) {
            event.preventDefault();
            jQuery("#plc_login_popup").dialog("close");
            jQuery("#plc_lost_password_popup").dialog("open");
        });

        jQuery("#plc_lost_password_popup").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            buttons: {
                "Ok": function () {

                    if (!self.validate_lost_password()) {
                        return false;
                    }

                    jQuery.ajax({
                        url: "/wp-content/themes/polc/includes/modules/lost-password-module.php",
                        method: "POST",
                        data: {
                            action: "retrieve",
                            login: jQuery("#lost_password_login").val()
                        },
                        success: function (response) {
                            try {
                                if (response.error) {
                                    jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                                    return false;
                                }

                                if (response.success) {
                                    jQuery.event.trigger("polc_alert", {title: "Siker", msg: response.success});
                                    return false;
                                }

                            } catch (exception) {
                                console.log(exception);
                            }
                        }
                    });

                    jQuery(this).dialog("close");
                }
            }
        });

        /****************************
         **Password reset event
         ***********************/
        jQuery("#plc_password_reset_ok").click(function (event) {
            event.preventDefault();

            if (!self.validate_reset_password()) {
                return false;
            }

            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/lost-password-module.php",
                method: "POST",
                data: {
                    action: "reset",
                    key: jQuery("#key").val(),
                    user_id: jQuery("#user_id").val(),
                    password: jQuery("#password").val(),
                    password_conf: jQuery("#password-conf").val()
                },
                success: function (response) {
                    if (response.error) {
                        jQuery.event.trigger('polc_alert', {title: 'Hiba', msg: response.error});
                    }

                    if (response.success) {
                        jQuery("#success").val(1);
                        jQuery.event.trigger('polc_alert', {title: "Siker", msg: response.success});
                    }
                }
            });
        });


        /****************************
         **Alert event
         ***********************/

        jQuery(document).on("polc_alert", function (event, data) {
            jQuery("#plc_alert_popup").dialog("open");
            jQuery("#plc_alert_popup").html("<h1>" + data.title + "</h1><p>" + data.msg + "</p>")
        });

        jQuery("#plc_alert_popup").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            buttons: {
                "Ok": function () {
                    jQuery(this).dialog("close");
                    jQuery.event.trigger("plc_alert_closed");
                }
            }
        });

        jQuery("#plc_login_popup").siblings('div.ui-dialog-titlebar').remove();
        jQuery("#plc_reg_popup").siblings('div.ui-dialog-titlebar').remove();
        jQuery("#plc_alert_popup").siblings('div.ui-dialog-titlebar').remove();
        jQuery("#plc_lost_password_popup").siblings("div.ui-dialog-titlebar").remove();
    });
}

/**
 * Content handler js.
 */
function polc_content_handler() {

    var self = this;

    jQuery(document).ready(function () {

        //Favorite handler
        jQuery(document).on("click", "#plcFavoriteBtn", function () {
            jQuery.ajax({
                url: "/wp-content/themes/polc/includes/modules/favorite-subscribe-module.php",
                method: "POST",
                data: {
                    action: "favorite",
                    mode: "story",
                    obj_id: jQuery("#plcFavoriteBtn").attr("data-post-id")
                },
                success: function (response) {
                    if (response.error) {
                        jQuery.event.trigger("polc_alert", {title: "Hiba", msg: response.error});
                        return false;
                    }
                    if (response.success) {
                        jQuery("#plcFavoriteBtn").fadeOut(500, function () {
                            jQuery(this).text(response.success).fadeIn(500);
                            if(jQuery(this).hasClass("favorited")){
                                jQuery(this).removeClass("favorited");
                            }else{
                                jQuery(this).addClass("favorited");
                            }
                        });
                    }
                }
            });
        });

        jQuery(".plcChapterSelect").change(function () {
            window.location.href = jQuery(this).find(':selected').attr("data-link");
        });

        //next chapter
        jQuery(".next").click(function () {
            if (jQuery(".plcChapterSelect option:selected").next().length > 0) {
                window.location.href = jQuery(".plcChapterSelect option:selected").next().attr("data-link");
            }
        });

        //previous vissza
        jQuery(".prev").click(function () {
            if (jQuery(".plcChapterSelect option:selected").prev().length > 0) {
                window.location.href = jQuery(".plcChapterSelect option:selected").prev().attr("data-link");
            }
        });

        jQuery(".plc_text_contrast").click(function () {
            if (jQuery(this).hasClass('day')) {
                jQuery(this).removeClass('day').addClass('night');
                jQuery('.plc_story_content').addClass('nightstyle');
                jQuery('.polcCommentWrapper').addClass('nightstyle');
                jQuery('.polcSocialShareAndTags').addClass('nightstyle');
            }
            else {
                jQuery(this).removeClass('night').addClass('day');
                jQuery('.plc_story_content').removeClass('nightstyle');
                jQuery('.polcCommentWrapper').removeClass('nightstyle');
                jQuery('.polcSocialShareAndTags').removeClass('nightstyle');
            }

        });



        //justify or left
        jQuery('.text_alignment').click(function () {
            if (jQuery(this).hasClass('left')) {
                jQuery(this).addClass('justify').removeClass('left');
                jQuery('.plc_story_content').addClass('justify');
            }
            else {
                jQuery(this).removeClass('justify').addClass('left');
                jQuery('.plc_story_content').removeClass('justify');
            }

        });
        //font style
        jQuery('.fontstyle').click(function () {
            if (jQuery('.fontstyle_list').hasClass('show')) {
                jQuery('.fontstyle_list').removeClass('show');
            }
            else {
                jQuery('.fontstyle_list').addClass('show');
            }

        });
        jQuery('.select_ptserif').click(function () {
            if (jQuery('.plc_story_content').hasClass('font_pt_serif')) {
                jQuery('.plc_story_content').removeClass('font_pt_serif');
            }
            else {
                jQuery('.plc_story_content').addClass('font_pt_serif');
                jQuery('.plc_story_content').removeClass('font_ubuntu font_titillium');
                jQuery('.fontstyle_list').removeClass('show');
            }

        });
        jQuery('.select_ubuntu').click(function () {
            if (jQuery('.plc_story_content').hasClass('font_ubuntu')) {
                jQuery('.plc_story_content').removeClass('font_ubuntu');
            }
            else {
                jQuery('.plc_story_content').addClass('font_ubuntu');
                jQuery('.plc_story_content').removeClass('font_pt_serif font_titillium');
                jQuery('.fontstyle_list').removeClass('show');
            }

        });
        jQuery('.select_titillium').click(function () {
            if (jQuery('.plc_story_content').hasClass('font_titillium')) {
                jQuery('.plc_story_content').removeClass('font_titillium');
            }
            else {
                jQuery('.plc_story_content').addClass('font_titillium');
                jQuery('.plc_story_content').removeClass('font_pt_serif font_ubuntu');
                jQuery('.fontstyle_list').removeClass('show');
            }

        });

        //font size
        jQuery('.fontsize').click(function () {
            if (jQuery('.fontsize_list').hasClass('show')) {
                jQuery('.fontsize_list').removeClass('show');
            }
            else {
                jQuery('.fontsize_list').addClass('show');
            }

        });

        jQuery(".fontsizeDefault").click(function (event) {
            event.preventDefault();
            jQuery("h1").animate({"font-size": "26px"});
            jQuery("h2").animate({"font-size": "20px"});
            jQuery("h3").animate({"font-size": "22px"});
            jQuery("p").animate({"font-size": "16px", "line-height": "32px"});

        });

        jQuery(".fontsizeBig").click(function (event) {
            event.preventDefault();
            jQuery("h1").animate({"font-size": "32px"});
            jQuery("h2").animate({"font-size": "28px"});
            jQuery("h3").animate({"font-size": "26px"});
            jQuery("p").animate({"font-size": "20px", "line-height": "36px"});

        });

        jQuery(".fontsizeMedium").click(function (event) {
            event.preventDefault();
            jQuery("h1").animate({"font-size": "30px"});
            jQuery("h2").animate({"font-size": "26px"});
            jQuery("h3").animate({"font-size": "24px"});
            jQuery("p").animate({"font-size": "18px", "line-height": "34px"});

        });

        jQuery(".fontsizeSmall").click(function (event) {
            event.preventDefault();
            jQuery("h1").animate({"font-size": "24px"});
            jQuery("h2").animate({"font-size": "18px"});
            jQuery("h3").animate({"font-size": "20px"});
            jQuery("p").animate({"font-size": "14px", "line-height": "30px"});

        });

        jQuery("a").click(function () {
            jQuery("a").removeClass("selected");
            jQuery(this).addClass("selected");

        });
    });
}