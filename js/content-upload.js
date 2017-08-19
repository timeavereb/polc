/**
 * Created by Pali on 2017. 06. 30..
 */

function validate_content() {

    var params = {};
    params.rules = {};
    params.messages = {};

    if (jQuery("#mode").val() == "new-volume") {
        params.rules.volume_title = "required";
        params.rules.blurb = "required";
        params.messages.volume_title = "A főcím kötelező!";
        params.messages.blurb = 'A fülszöveg kötelező!';
    }

    if (jQuery("#content_type").val() == "sequel" || jQuery("#mode").val() == "new-chapter") {
        params.rules.chapter_title = "required";
        params.messages.chapter_title = "A fejezet címe kötelező!";
    }

    jQuery("#polc-story-form").validate(params);

    if (jQuery("#story_content").val().length == 0) {
        console.log("000");
        jQuery("#story_content-error").css("display", "block");
    }

    var validation = jQuery("#polc-story-form").valid();

    if (!validation || jQuery("#story_content").val() == "") {
        return false;
    } else {
        return true;
    }
}

/**
 * Creates a tag element to variable and returns it.
 * @param value
 * @returns {string}
 */
function get_tag_element(value) {

    var tag_element = '<div class="plcTagElement" style="display:none;">' +
        '<span class="plcTagELementText">' + value + '</span>' +
        '<input type="hidden" name="post_tag[]" value="' + value + '">' +
        '<span class="plcTagElementDelete" onclick="remove_tag_element(this);"></span>' +
        '</div>';

    return tag_element;
}

/**
 * Removes tag.
 * @param sender
 */
function remove_tag_element(sender) {
    jQuery(sender).parent().fadeOut('normal', function () {
        jQuery(this).remove();
    });
}

/**
 * Appends the selected tag element to tag wrapper.
 * @param element
 */
function add_tag_element(element) {
    jQuery(element).appendTo(".plcTagContainer").fadeIn("normal");
    jQuery("#polc_tag_handler").val("");
}

/**
 * Return the selected tag values.
 * @returns {Array}
 */
function selected_tag_list() {

    var tag_elements = jQuery(".plcTagElement"),
        tag_array = [];

    jQuery.each(tag_elements, function () {
        tag_array.push(jQuery(this).find("input").val());
    });

    console.log(tag_array);

    return tag_array;
}

jQuery(document).ready(function () {

    //check agelimit 18 if it's obscene or erotic
    jQuery("#violent-content, #erotic-content").change(function () {
        if (jQuery(this).prop("checked") == true) {
            jQuery(".agelimit18").next().click();
            jQuery("#only-registered").prop("checked", true);
        }
    });

    //check agelimit 12 if it's obscene content
    jQuery("#obscene-content").change(function () {
        if (jQuery(this).prop("checked") == true && jQuery('input[name=agelimit]:checked').val() == 0) {
            jQuery(".agelimit12").next().click();
        }
    });

    //Prevent the user changeing age limit if it's a specific content type
    jQuery('input[name=agelimit]').click(function () {
        var agelimit = parseInt(jQuery(this).val());

        if (jQuery("#erotic-content").prop("checked") == true || jQuery("#violent-content").prop("checked") == true
            && 18 > agelimit) {
            return false;
        }
        if (jQuery("#obscene-content").prop("checked") == true && 12 > agelimit) {
            return false;
        }

        if (agelimit == 18) {
            jQuery("#only-registered").prop("checked", true);
        }
    });

    jQuery("#only-registered").click(function () {
        if (
            jQuery(this).prop("checked") == false &&
            (
                parseInt(jQuery('input[name=agelimit]:checked').val()) == 18 ||
                jQuery("#violent-content").prop("checked") == true ||
                jQuery("#erotic-content").prop("checked") == true
            )
        ) {
            return false;
        }
    });

    jQuery("#submit").click(function (event) {
        jQuery("#story_content-error").hide();
        tinyMCE.triggerSave();

        if (!validate_content()) {
            event.preventDefault();
            return false;
        }
        jQuery("#polc-story-form").submit();
    });

    jQuery("#polc_tag_handler").autocomplete({

        source: function (request, response) {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'ajax-tag-search',
                    tax: 'post_tag',
                    q: jQuery("#polc_tag_handler").val()
                },
                success: function (data) {

                    if (data.length == 0) {
                        return [];
                    }

                    var parsed = data.split("\n"),
                        result = [],
                        tag_array = selected_tag_list();

                    jQuery.each(parsed, function (k, v) {
                        if (jQuery.inArray(v, tag_array) < 0) {
                            var element = {
                                label: v,
                                value: v
                            };
                            result.push(element);
                        }
                    });

                    response(result);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {

            if (selected_tag_list().length == 8) {
                jQuery.event.trigger("polc_alert", {title: "Figyelmeztetés", msg: "Maximum 8 címke adható meg!"});
                return false;
            }

            if (jQuery.inArray(ui.item.value, selected_tag_list()) < 0) {
                add_tag_element(get_tag_element(ui.item.value));
                return false;
            }
        }
    });

    jQuery('#polc_tag_handler').keypress(function (event) {

        if (event.keyCode == 13) {

            if (selected_tag_list().length == 8) {
                jQuery.event.trigger("polc_alert", {title: "Figyelmeztetés", msg: "Maximum 8 címke adható meg!"});
                return false;
            }

            if (jQuery.inArray(jQuery(this).val(), selected_tag_list()) < 0 && jQuery.trim(jQuery(this).val()).length > 0) {
                add_tag_element(get_tag_element(jQuery(this).val()));
            }
        }
    });
})
;