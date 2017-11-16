/**
 * Fields of the add movie form.
 */
var addMovieFields = ['imdbId'];

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
                className: "btn btn-primary",
                callback: function() {
                    apiCall({
                        url: window.MovieTracker.baseUrl + "/api/add",
                        type: requestType.POST,
                        data: {
                            'imdbId': $(".bootbox-body #imdbId").val(),
                            'hasWatched': hasWatched ? 1 : 0
                        },
                        callback: function(response) {
                            addMovie(response.content.movie_id, response.content.title, response.content.imgPath);
                            addDialog.modal("hide");
                        }
                    }, addMovieFields);
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
            className: "btn btn-danger",
            callback: function() {
                apiCall({
                    url: window.MovieTracker.baseUrl + "/api/remove",
                    type: requestType.POST,
                    data: {
                        'userMovieId': userMovieId
                    },
                    callback: function() {
                        $("#" + userMovieId).remove();
                        showDialog.modal("hide");
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
                apiCall({
                    url: window.MovieTracker.baseUrl + "/api/watched",
                    type: requestType.POST,
                    data: {
                        'userMovieId': userMovieId
                    },
                    callback: function() {
                        $("#" + userMovieId).remove();
                        showDialog.modal("hide");
                    }
                });
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
function filterMovieList(listType, searchText, orderingType) {
    showLoadingIcon();
    apiCall({
        url: window.MovieTracker.baseUrl + "/api/filter",
        type: requestType.POST,
        data: {
            'searchText': searchText,
            'orderingType': orderingType,
            'listType': listType
        },
        callback: function(response) {
            if (response.content.length !== 0) {
                for (var i = 0; i <= response.content.length-1; i++) {
                    addMovie(response.content[i].id, response.content[i].title, response.content[i].imgPath)
                }
            } else {
                $("#movies").html("<p>No results found.</p>");
            }
        }
    });
}

/**
 * Shows the loading button for filtering the movies.
 */
function showLoadingIcon() {
    $("#movies").html(
        '<div style="margin: 50px 0; text-align: center;"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>'
    );
}

/**
 * Adds the new movie to the existing movie list.
 *
 * @param movieId
 * @param title
 * @param path
 */
function addMovie(movieId, title, path) {
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