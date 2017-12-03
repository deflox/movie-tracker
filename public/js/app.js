/**
 * Fields of the add movie form.
 */
var addMovieFields = ['imdbId'];

/**
 * Possible directions for the pagination.
 */
var direction = {
    PREVIOUS: "previous",
    NEXT: "next"
};

/**
 * Delays the key up event so that the it only does the request after the user
 * stopped typing for an amount of time.
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
 * @param hasWatched
 */
function openAddDialog(hasWatched) {
    var addDialog = bootbox.dialog({
        title: "Add a new movie",
        message: $("#add-modal").html(),
        onEscape: true,
        buttons: {
            add: {
                label: "Add movie",
                className: "btn-primary",
                callback: function() {
                    apiCall({
                        url: window.MovieTracker.baseUrl + "/api/add",
                        type: requestType.POST,
                        data: {
                            'imdbId': $(".bootbox-body #imdbId").val(),
                            'hasWatched': hasWatched ? 1 : 0
                        },
                        callback: function(response) {
                            reset(response.content.listType);
                            addDialog.modal("hide");
                            successMessage("Movie added successfully.");
                        }
                    }, "btn-primary", addMovieFields);
                    return false;
                }
            }
        }
    });
}

/**
 * Opens the dialog to show a single movie.
 *
 * @param userMovieId
 * @param isWatchList
 */
function openShowDialog(userMovieId, isWatchList) {
    var showDialog;
    var dialogButtons = {
        deleteMovie: {
            label: "Delete movie",
            className: "btn-danger",
            callback: function() {
                apiCall({
                    url: window.MovieTracker.baseUrl + "/api/remove",
                    type: requestType.POST,
                    data: {
                        'userMovieId': userMovieId
                    },
                    callback: function(response) {
                        reset(response.content.listType);
                        showDialog.modal("hide");
                        successMessage("Movie deleted successfully.");
                    }
                }, "btn-danger");
                return false;
            }
        }
    };
    if (isWatchList) {
        dialogButtons.markAsWatched = {
            label: "Mark as watched",
            className: "btn-success",
            callback: function() {
                apiCall({
                    url: window.MovieTracker.baseUrl + "/api/watched",
                    type: requestType.POST,
                    data: {
                        'userMovieId': userMovieId
                    },
                    callback: function(response) {
                        reset(response.content.listType);
                        showDialog.modal("hide");
                        successMessage("Movie successfully marked as watched.");
                    }
                }, "btn-success");
                return false;
            }
        };
    }

    apiCall({
        url: window.MovieTracker.baseUrl + "/api/get/" + userMovieId,
        type: requestType.GET,
        callback: function(response) {
            showDialog = bootbox.dialog({
                title: response.content.title,
                message: substituteShowDialog($("#show-modal").html(), response.content),
                onEscape: true,
                backdrop: true,
                buttons: dialogButtons
            });
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
function getMovieList(listType, searchText, orderingType) {
    showLoadingIcon();
    apiCall({
        url: window.MovieTracker.baseUrl + "/api/filter/movies",
        type: requestType.POST,
        data: {
            "listType": listType,
            "searchText": searchText,
            "orderingType": orderingType
        },
        callback: function(response) {
            if (response.content.movies.length !== 0) {
                for (var i = 0; i <= response.content.movies.length-1; i++) {
                    addMovie(response.content.movies[i].id, response.content.movies[i].title, response.content.movies[i].imgPath);
                }
            } else {
                $("#movies").html('<p>No results found.</p>');
            }
            if (response.content.pagination === true) {
                addPagination();
            } else {
                removePagination();
            }
        }

    });
}

/**
 * Fetches the results for the next or previous page according to the direction
 * parameter.
 *
 * @param listType
 * @param searchText
 * @param orderingType
 * @param currentPage
 * @param direction
 */
function paginate(listType, searchText, orderingType, currentPage, direction) {
    showLoadingIcon();
    apiCall({
        url: window.MovieTracker.baseUrl + "/api/paginate/movies",
        type: requestType.POST,
        data: {
            "listType": listType,
            "searchText": searchText,
            "orderingType": orderingType,
            "currentPage": currentPage,
            "direction": direction
        },
        callback: function(response) {
            if (response.content.movies.length !== 0) {
                for (var i = 0; i <= response.content.movies.length-1; i++) {
                    addMovie(response.content.movies[i].id, response.content.movies[i].title, response.content.movies[i].imgPath);
                }
            } else {
                showErrorMessage(unknownError);
            }
            updatePagination(response.content.newPage, response.content.previousAvailable, response.content.nextAvailable);
        }
    });
}

/**
 * Removes the pagination from the site.
 */
function removePagination() {
    $("#pagination").remove();
}

/**
 * Adds the pagination to the page.
 */
function addPagination() {
    $("#pagination").remove();
    $("#movies").after(
        '<div id="pagination" class="clearfix"> <button class="btn btn-primary" id="previous" disabled> <i class="fa fa-arrow-left"></i> Previous </button> <button class="btn btn-primary pull-right" id="next"> Next <i class="fa fa-arrow-right"></i> </button> </div> <input type="hidden" id="page" value="1">'
    );
}

/**
 * Updates the existing pagination on the page.
 *
 * @param newPage
 * @param previousAvailable
 * @param nextAvailable
 */
function updatePagination(newPage, previousAvailable, nextAvailable) {
    $("#page").val(newPage);
    if (previousAvailable === true) $("#previous").prop('disabled', false);
    else $("#previous").prop('disabled', true);
    if (nextAvailable === true) $("#next").prop('disabled', false);
    else $("#next").prop('disabled', true);
}

/**
 * Shows the loading button for filtering the movies.
 */
function showLoadingIcon() {
    $("#movies").html(
        '<div style="margin: 50px 0; text-align: center; font-size: 15px"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>'
    );
}

/**
 * Adds a movie to the list but does not prepend the movie but instead appends the movie.
 *
 * @param movieId
 * @param title
 * @param path
 */
function addMovie(movieId, title, path) {
    var movieImg = "";
    if (path !== "default") {
        movieImg = '<img src="https://image.tmdb.org/t/p/w640/' + path + '" title="' + title + '" alt="' + title + '" id="' + movieId + '">';
    } else {
        movieImg = '<img src="' + window.MovieTracker.baseUrl + '/img/default.png" title="' + title + '" alt="' + title + '" id="' + movieId + '">';
    }
    if ($("#movies").has("img").length > 0) {
        $("#movies").append(movieImg);
    } else {
        $("#movies").html(movieImg);
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
    var movieImg = "";
    if (data.imgPath !== "default") {
        movieImg = '<img src="https://image.tmdb.org/t/p/w640/' + data.imgPath + '" style="width: 180px">';
    } else {
        movieImg = '<img src="' + window.MovieTracker.baseUrl + '/img/default.png" style="width: 180px">';
    }
    return text.replace("%IMAGE%", movieImg)
        .replace("%PLOT%", data.plot)
        .replace("%YEAR%", data.year)
        .replace("%RUNTIME%", data.runtime)
        .replace("%GENRES%", data.genres);
}

/**
 * Resets the whole view after adding or deleting a movie.
 */
function reset(listType) {
    $("#search").val('');
    $("#order").val('0');
    getMovieList(listType, $("#search").val(), $("#order").val());
}

/**
 * Adds a success message which will fade out after 3 seconds.
 *
 * @param message
 */
function successMessage(message) {
    $("h1").after(
        '<div id="message" class="alert alert-success">' + message + '</div>'
    );
    $("#message").delay(3000).fadeOut();
}