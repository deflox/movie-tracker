/**
 * Error codes which get passed from the API.
 */
var apiErrorCode = {
    validationError: "validation_error",
    isNotAMovieError: "is_not_a_movie",
    noPermissionError: "no_permissions"
};

/**
 * Possible request types.
 */
var requestType = {
    GET: "GET",
    POST: "POST"
};

/**
 * Default data type.
 */
var defaultDataType = "json";

/**
 * Error message in case an unknown error occurred.
 */
var unknownError = "An unknown error occurred. Please try again or contact the site administrator.";

/**
 * Makes an API call.
 *
 * @param requestSettings
 * @param inputFields
 */
function apiCall(requestSettings, inputFields) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    requestSettings.dataType = defaultDataType;
    requestSettings.success = function(response) {
        if (response.errors) handleApiErrorResponse(response, inputFields);
        else requestSettings.callback(response);
    };
    requestSettings.error = function () {
        showErrorMessage(unknownError);
    };
    $.ajax(requestSettings);
}

/**
 * Handles response in case of an error.
 *
 * @param response
 * @param inputFields
 */
function handleApiErrorResponse(response, inputFields) {
    switch (response.errorCode) {
        case apiErrorCode.validationError:
            showInputErrorMessages(response.errorMessages, inputFields);
            break;
        case apiErrorCode.isNotAMovieError:
            showErrorMessage(response.errorMessage);
            break;
        case apiErrorCode.noPermissionError:
            showErrorMessage(response.errorMessage);
            break;
        default:
            showErrorMessage(unknownError);
    }
}

/**
 * Shows alert in the dialog box with the error message.
 *
 * @param errorMessage
 */
function showErrorMessage(errorMessage) {
    $(".alert-danger").remove();
    if ($(document).has(".bootbox-body").length > 0) {
        $(".bootbox-body").prepend('<div class="alert alert-danger">' + errorMessage + '</div>');
    } else {
        showAlert(errorMessage);
    }
}

/**
 * Shows the error messages below the fields.
 *
 * @param inputFields
 * @param errorMessages
 */
function showInputErrorMessages(errorMessages, inputFields) {
    $(".bootbox-body .error").remove();
    for (var i=0; i < inputFields.length; i++) {
        $(".bootbox-body #" + inputFields[i]).parent().append('<span class="error">' + errorMessages[inputFields[i]][0] + '</span>');
    }
}

/**
 * Opens an alert containing the passed message.
 *
 * @param message
 */
function showAlert(message) {
    bootbox.alert({
        message: message,
        backdrop: true
    });
}