/**
 * Error codes from the API.
 */
var errorCodes = {
    validationError: "validation_error",
    isNotAMovieError: "is_not_a_movie",
    noPermissionError: "no_permissions"
};

/**
 * Fields of the add movie form.
 */
var addMovieFields = ['imdbId'];

/**
 * Delays the key up event so that the it only does the request
 * after the user stopped typing for an amount of time.
 *
 * @see https://stackoverflow.com/a/1909508
 */
var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

/**
 * Opens the dialog to add a new movie.
 *
 * @param watched
 */
function openAddDialog(watched) {
    var addDialog = bootbox.dialog({
        title: "Add a new movie",
        message: $("#add-modal").html(),
        onEscape: true,
        buttons: {
            add: {
                label: "Add movie",
                className: "btn btn-primary",
                callback: function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: window.MovieTracker.baseUrl + "/api/add",
                        data: {
                            '_token': window.MovieTracker.csrfToken,
                            'imdbId': $(".bootbox-body #imdbId").val(),
                            'watched': watched ? 1 : 0
                        },
                        success: function(response) {
                            if (!response.errors) {
                                addNewMovie(response.content.movie_id, response.content.title, response.content.imgPath);
                                addDialog.modal("hide");
                            }
                            if (response.errors) {
                                handleErrorResponse(response);
                            }
                        }
                    });
                    return false;
                }
            }
        }
    });
}

/**
 * Opens the dialog to show a single movie.
 *
 * @param movieId
 * @param isWatchList
 */
function openShowDialog(movieId, isWatchList) {
    var showDialog;
    var dialogButtons = {
        deleteMovie: {
            label: "Delete movie",
            className: "btn btn-danger",
            callback: function() {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: window.MovieTracker.baseUrl + "/api/remove",
                    data: {
                        '_token': window.MovieTracker.csrfToken,
                        'userMovieId': movieId
                    },
                    success: function (response) {
                        if (response.errors) {
                            handleErrorResponse(response);
                        }
                        if (!response.errors) {
                            $("#" + movieId).remove();
                            showDialog.modal("hide");
                        }
                    }
                });
                return false;
            }
        }
    };
    if (isWatchList) {
        dialogButtons.markAsWatched = {
            label: "Mark as watched",
            className: "btn btn-success",
            callback: function() {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: window.MovieTracker.baseUrl + "/api/watched",
                    data: {
                        '_token': window.MovieTracker.csrfToken,
                        'userMovieId': movieId
                    },
                    success: function (response) {
                        if (response.errors) {
                            handleErrorResponse(response);
                        }
                        if (!response.errors) {
                            $("#" + movieId).remove();
                            showDialog.modal("hide");
                        }
                    }
                });
                return false;
            }
        };
    }
    $.ajax({
        type: "get",
        dataType: "json",
        url: window.MovieTracker.baseUrl + "/api/get/" + movieId,
        success: function(response) {
            if (response.errors) {
                handleErrorResponse(response);
            }
            if (!response.errors) {
                showDialog = bootbox.dialog({
                    title: response.content.title,
                    message: substituteShowDialog($("#show-modal").html(), response.content),
                    onEscape: true,
                    backdrop: true,
                    buttons: dialogButtons
                });
            }
        }
    });
}

/**
 * Filters the movie list based on the filter inputs.
 *
 * @param listType
 * @param searchText
 * @param orderingType
 */
function filterMovieList(listType, searchText, orderingType) {
    showLoadingIcon();
    $.ajax({
        type: "post",
        dataType: "json",
        url: window.MovieTracker.baseUrl + "/api/filter",
        data: {
            '_token': window.MovieTracker.csrfToken,
            'searchText': searchText,
            'orderingType': orderingType,
            'listType': listType ? 1 : 0
        },
        success: function (response) {
            if (response.errors) {
                handleErrorResponse(response);
            }
            if (!response.errors) {
                for (var i = 0; i <= response.content.length; i++) {
                    addNewMovie(response.content[i].id, response.content[i].title, response.content[i].imgPath)
                }
            }
        }
    });
}

/**
 * Handles errors in case the API response has some.
 *
 * @param response
 */
function handleErrorResponse(response) {
    removeInputErrorMessages();
    switch (response.errorCode) {
        case errorCodes.validationError:
            showErrorMessage(response.errorMessage);
            showInputErrorMessages(addMovieFields, response.errorMessages);
            break;
        case errorCodes.isNotAMovieError:
            showErrorMessage(response.errorMessage);
            break;
        case errorCodes.noPermissionError:
            showNoPermissionAlert(response.errorMessage);
            break;
        default:
            showErrorMessage("An unknown error occurred. Please try again or contact the site administrator.");
    }
}

/**
 * Shows alert in the dialog box with the error message.
 *
 * @param errorMessage
 */
function showErrorMessage(errorMessage) {
    if ($(".bootbox-body").has(".alert").length > 0) {
        $(".bootbox-body").find(".alert").html(errorMessage);
    } else {
        $(".bootbox-body").prepend('<div class="alert alert-danger">' + errorMessage + '</div>');
    }
}

/**
 * Removes all error messages below the fields.
 */
function removeInputErrorMessages() {
    $(".bootbox-body .error").remove();
}

/**
 * Shows the error messages below the fields.
 *
 * @param inputFields
 * @param errorMessages
 */
function showInputErrorMessages(inputFields, errorMessages) {
    for (var i=0; i < inputFields.length; i++) {
        $(".bootbox-body #" + inputFields[i]).parent().append('<span class="error">' + errorMessages[inputFields[i]][0] + '</span>');
    }
}

/**
 * Shows the loading button for refreshing the movies.
 */
function showLoadingIcon() {
    $("#movies").html(
        '<div style="margin: 50px 0; text-align: center;"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>'
    );
}

/**
 * Shows the error in case user has no permissions.
 */
function showNoPermissionAlert() {
    bootbox.alert({
        message: "You do not have permission for this object!",
        backdrop: true
    });
}

/**
 * Adds the new movie to the existing movie list.
 *
 * @param movieId
 * @param title
 * @param path
 */
function addNewMovie(movieId, title, path) {
    var movieImage = '<div class="movie-image" id="' + movieId + '"><img src="https://image.tmdb.org/t/p/w640/' + path + '" title="' + title + '"></div>';
    if ($("#movies").has(".movie-image").length > 0) {
        $("#movies").prepend(movieImage);
    } else {
        $("#movies").html(movieImage)
    }
}

/**
 * Returns the substituted string by replacing the placeholders with the actual data.
 *
 * @param text
 * @param data
 * @returns {string|XML}
 */
function substituteShowDialog(text, data) {
    return text.replace("%IMAGE%", '<img src="https://image.tmdb.org/t/p/w640/' + data.imgPath + '" style="width: 180px">')
        .replace("%PLOT%", data.plot)
        .replace("%YEAR%", data.year)
        .replace("%RUNTIME%", data.runtime)
        .replace("%GENRES%", data.genres);
}