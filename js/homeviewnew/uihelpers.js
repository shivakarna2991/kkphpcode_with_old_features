function displayError(errorText) {
    $(".error-msg").text(errorText);
    $(".error-msg").show().delay(3000).fadeOut();
}
    
function displaySuccess(successText) {
    $(".success-msg").text(successText);
    $(".success-msg").show().delay(3000).fadeOut();
}

function displayFatalError(errorTxt, resultStr) {
    if (resultStr !== undefined && resultStr !== null && resultStr.indexOf("login required") > -1) {
        // Log the user out. 
        $("#shouldLogUserOut").html("yes");
        $("#errorFieldMsg").html("Login required.");
    }
    else {
        $("#errorFieldMsg").html("Fatal Error: " + errorTxt);
    }
    
    $("#errorDialog").show();
}

function displayConfirmDialog(onProgress) {
    $("#confirmDialogProgressBtn").prop("onclick", null).off("click");
    $("#confirmDialogProgressBtn").attr("onclick", "").unbind("click");
    $("#confirmDialog").show();
    $("#confirmDialogProgressBtn").click(function () {
        closeConfirmDialog();
        onProgress();
    });
}

function closeConfirmDialog() {
    $("#confirmDialog").hide();
}

function verifyDate(date) {
    // Verify the format yy-mm-dd
    return /^[0-9][0-9][0-9][0-9]\-[0-9][0-9]\-[0-9][0-9]$/.test(date);
}

function verifyTime(time) {
    // Verify the format HH:MM
    return /^[0-9][0-9]\:[0-9][0-9]$/.test(time);
}

function dynamicListAddClick(clickedEle) {
    if ($(clickedEle).parent().prev().hasClass("expandable-list-date")) {
        // This is working with date data.
        var insertElement = "";
        insertElement += "<div class='expandable-list-element expandable-list-date'>";
        insertElement += "<input type='text' class='date-picker-input'></input>";
        insertElement += "<input type='text' class='date-picker-input'></input>";
        insertElement += "</div>";
        $(clickedEle).parent().prev().after(insertElement);

        // Transform the element to allow for the date picker to appear.
        $(clickedEle).parent().prev().find(".date-picker-input").datepicker({
            dateFormat: "yy-mm-dd"
        });
    }
    else if ($(clickedEle).parent().prev().hasClass("expandable-list-single")) {
        var insertElement = "";
        insertElement += "<div class='expandable-list-element expandable-list-single'>";
        insertElement += "<input type='text'></input>";
        insertElement += "</div>";
        $(clickedEle).parent().prev().after(insertElement);
    }
    else {
        // This is working with regular duration data.
        var insertElement = "";
        insertElement += "<div class='expandable-list-element'>";
        insertElement += "<p style='display: none;' class='popup-prompt'>Please enter times in military time in the format HH:MM</p>";
        insertElement += "<input class='expandable-list-time' type='text'></input>";
        insertElement += "<input class='expandable-list-time' type='text'></input>";
        insertElement += "</div>";
        $(clickedEle).parent().prev().after(insertElement);
    }
}

function dynamicListRemoveClick(clickedEle) {
    // There needs to be at least one element and the container for the add and remove buttons.
    if ($(clickedEle).parent().parent().children().length > 2) {
        // Remove the last element in the container that is not the container for the add and remove buttons.
        $(clickedEle).parent().prev().remove();
    }
}

function dynamicListSetTimeClick(clickedEle, start, end) {
    if ($(clickedEle).parent().parent().children().length > 2) {
        var insertElement = "";
        insertElement += "<div class='expandable-list-element'>";
        insertElement += "<p style='display: none;' class='popup-prompt'>Please enter times in military time in the format HH:MM</p>";
        insertElement += "<input class='expandable-list-time' type='text'>" + start + "</input>";
        insertElement += "<input class='expandable-list-time' type='text'>" + end + "</input>";
        insertElement += "</div>";
        $(clickedEle).parent().prev().after(insertElement);
    }
    else {
        var inputs = $(clickedEle).parent().prev().children("input");
        inputs.first().val(start);
        inputs.last().val(end);
    }
}

function dynamicListSetTimePMClicked(clickedEle) {
    dynamicListSetTimeClick(clickedEle, '14:00', '17:00');
}

function dynamicListSetTimeAMClicked(clickedEle) {
    dynamicListSetTimeClick(clickedEle, '06:00', '09:00');
}

function createValueField(fieldName, fieldType, fieldValue, setId, options) {
    var html = "";
    html += "<label>" + fieldName + "</label>";

    switch (fieldType) {
        case "textarea":
            html += "<textarea id='input" + setId + "'>" + fieldValue + "</textarea>";
            break;
        case "datepicker":
            html += "<input type='text' class='date-picker-input' id='input" + setId + "' value='" + fieldValue + "'></input>";
            break;
        case "datepickers":
            html += "<div id='input" + setId + "' class='expandable-list-section'>";
            if (fieldValue == null || fieldValue.length == 0) {
                html += "<div class='expandable-list-element expandable-list-date'>";
                html += "<input type='text' class='date-picker-input'></input>";
                html += "<input type='text' class='date-picker-input'></input>";
                html += "</div>";
            }
            else {
                for (var i = 0; i < fieldValue.length; ++i) {
                    var durationValue = fieldValue[i];
                    html += "<div class='expandable-list-element expandable-list-date'>";
                    html += "<input type='text' class='date-picker-input' value='" + durationValue.start + "'></input>";
                    html += "<input type='text' class='date-picker-input' value='" + durationValue.end + "'></input>";
                    html += "</div>";
                }
            }
            html += "<div class='expandable-list-button-section'>";
            html += "<button onclick='dynamicListAddClick(this);'>+</button>";
            html += "<button onclick='dynamicListRemoveClick(this);'>-</button>";
            html += "</div>";
            html += "</div>";
            break;
        case "durationpickers":
            html += "<div id='input" + setId + "' class='expandable-list-section'>";
            if (fieldValue == null || fieldValue.length == 0) {
                html += "<div class='expandable-list-element'>";
                html += "<p style='display: none;' class='popup-prompt'>Please enter times in military time in the format HH:MM</p>";
                html += "<input class='expandable-list-time' type='text'></input>";
                html += "<input class='expandable-list-time' type='text'></input>";
                html += "</div>";
            }
            else {
                for (var i = 0; i < fieldValue.length; ++i) {
                    var durationValue = fieldValue[i];
                    html += "<div class='expandable-list-element'>";
                    html += "<p style='display: none;' class='popup-prompt'>Please enter times in military time in the format HH:MM</p>";
                    html += "<input class='expandable-list-time' type='text' value='" + 
                        durationValue.start.trim(0, durationValue.start.length - 3) + "'></input>";
                    html += "<input class='expandable-list-time' type='text' value='" + 
                        durationValue.end.trim(0, durationValue.end.length - 3) + "'></input>";
                    html += "</div>";
                }
            }
            html += "<div class='expandable-list-button-section'>";
            html += "<button onclick='dynamicListAddClick(this);'>+</button>";
            html += "<button onclick='dynamicListRemoveClick(this);'>-</button>";
            html += "<button onclick='dynamicListSetTimePMClicked(this);'>PM</button>";
            html += "<button onclick='dynamicListSetTimeAMClicked(this);'>AM</button>";
            html += "</div>";
            html += "</div>";
            break;
        case "valuelist":
            html += "<div id='input" + setId + "' class='expandable-list-section'>";
            if (fieldValue == null || fieldValue.length == 0) {
                html += "<div class='expandable-list-element expandable-list-single'>";
                html += "<input type='text'></input>";
                html += "</div>";
            }
            else {
                for (var i = 0; i < fieldValue.length; ++i) {
                    var singleValue = fieldValue[i];
                    html += "<div class='expandable-list-element expandable-list-single'>";
                    html += "<input type='text' value='" + singleValue + "'></input>";
                    html += "</div>";
                }
            }
            html += "<div class='expandable-list-button-section'>";
            html += "<button onclick='dynamicListAddClick(this);'>+</button>";
            html += "<button onclick='dynamicListRemoveClick(this);'>-</button>";
            html += "</div>";
            html += "</div>";
            break;
        case "select":
            html += "<div class='select-style'>";
            html += "<select id='input" + setId + "'>";
            for (var i = 0; i < options.length; ++i) {
                html += "<option value='" + i + "'" + (fieldValue == i ? " selected" : "") + ">" + options[i] + "</option>";   
            }
            html += "</select>";
            html += "</div>";
            break;
        default:
            html += "<input id='input" + setId + "' type='" + fieldType + "' value='" + fieldValue + "'></input>";
            break;
    }

    return html;
}

function getFieldValue(fieldName) {
    nodeName = $("#input" + fieldName).prop('nodeName');
    nodeName = nodeName.toLowerCase();
    var fieldType;
    if (nodeName == "input") {
        fieldType = "text";
    }
    else if (nodeName == "div") {
        if ($("#input" + fieldName).find(".expandable-list-single").length > 0) {
            fieldType = "valuelist";
        }
        else {
            fieldType = "dynamiclist";
        }
    }
    else {
        fieldType = nodeName;
    }

    switch (fieldType) {
        case "dynamiclist":
            // Get all of the child elements.
            var allDurations = [];
            
            var results = $("#input" + fieldName).find("input");
            
            var tmpDurations = [];
            for (var i = 0; i < results.length; ++i) {
                var childEle = $(results[i]);
                var childEleVal = childEle.val();
                tmpDurations.push(childEleVal);
                if ((i + 1) % 2 == 0 && i != 0) {
                    var addObj = {
                        start: tmpDurations[0],
                        end: tmpDurations[1]
                    };
                    allDurations.push(addObj);
                    tmpDurations = [];
                }
            }
            return allDurations;
            break;
        case "valuelist":
            var allValues = [];
            var results = $("#input" + fieldName).find("input");
            
            for (var i = 0; i < results.length; ++i) {
                var childEle = $(results[i]);
                allValues.push(childEle.val());
            }
            
            return allValues;
            break;
        case "select":
        case "textarea":
        case "text":
            return $("#input" + fieldName).val();
            break;
    }

    return null;
}