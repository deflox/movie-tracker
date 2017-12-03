<?php

namespace App\Http\Controllers;

use App\Constants\ErrorCodeConstants;
use App\Exceptions\IsNotAMovieException;
use App\Genre;
use App\Movie;
use App\Rules\ImdbId;
use App\Rules\IsCorrectListType;
use App\Rules\IsCorrectOrderType;
use App\Rules\IsUniqueMovie;
use App\UserMovie;
use App\Util\API;
use App\Util\APIRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Controller responsible for all movie related actions.
 *
 * @package App\Http\Controllers
 */
class MoviesController extends Controller
{
    /**
     * Contains the API key.
     *
     * @var String
     */
    private $apiKey;

    /**
     * The possible ordering types.
     *
     * @var array
     */
    private $orderingTypes = [
        1 => ['movies.year', 'asc'],
        2 => ['movies.year', 'desc'],
        3 => ['movies.runtime', 'asc'],
        4 => ['movies.runtime', 'desc'],
    ];

    /**
     * MoviesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->apiKey = Config::get('keys.moviedb');
    }

    /**
     * Displays the movies page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function movies()
    {
        return view('index', [
            'userMovies' => UserMovie::watchedMovies(),
            'totalUserMovies' => UserMovie::getTotalUserMoviesForUser(1),
        ]);
    }

    /**
     * Displays the watchlist page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function watchlist()
    {
        return view('watchlist', [
            'userMovies' => UserMovie::unwatchedMovies(),
            'totalUserMovies' => UserMovie::getTotalUserMoviesForUser(0),
        ]);
    }

    /**
     * Gets the movie for the given id.
     *
     * @param  $id
     * @return string
     */
    public function get($id)
    {
        $userMovie = UserMovie::find($id);

        return API::response([
            'id' => $userMovie->id,
            'imgPath' => $userMovie->movie->imgPath,
            'title' => $userMovie->movie->title,
            'plot' => $userMovie->movie->plot,
            'year' => $userMovie->movie->year,
            'runtime' => $userMovie->movie->runtime,
            'genres' => $userMovie->movie->genresAsString(),
        ]);
    }

    /**
     * Adds a new movie for the user.
     *
     * @param  Request $request
     * @return string
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'imdbId' => ['required', new ImdbId(), new IsUniqueMovie()],
        ]);

        if ($validator->fails()) {
            return API::error(
                ErrorCodeConstants::VALIDATION_ERROR,
                null,
                $validator->errors()->messages()
            );
        }

        $validator = Validator::make($request->all(), [
            'hasWatched' => ['bail', 'numeric', new IsCorrectListType()],
        ]);

        if ($validator->fails()) {
            return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        }

        $imdbId = $request->get('imdbId');

        $movie = Movie::where('imdb_id', $imdbId)
            ->first();

        if ($movie === null) {
            $response = null;

            try {
                $response = $this->findMovie($imdbId);
            } catch (IsNotAMovieException $ex) {
                return API::error(ErrorCodeConstants::IS_NOT_A_MOVIE, $ex->getMessage());
            }

            if (isset($response->release_date)) $year = Carbon::createFromFormat('Y-m-d', $response->release_date)->format('Y');
            else $year = '1800';
            if (isset($response->poster_path)) $imgPath = substr($response->poster_path, 1, strlen($response->poster_path));
            else $imgPath = 'default';

            $movie = Movie::create([
                'imdb_id' => $response->imdb_id,
                'themoviedb_id' => $response->id,
                'title' => $response->title,
                'plot' => $response->overview !== "" ? $response->overview : 'No plot.',
                'runtime' => isset($response->runtime) ? $response->runtime : 0,
                'year' => $year,
                'imgPath' => $imgPath,
            ]);

            $this->assignGenres($movie, $response->genres);
        }

        $userMovie = UserMovie::create([
            'watched' => $request->get('hasWatched'),
            'user_id' => Auth::id(),
            'movie_id' => $movie->id,
        ]);

        return API::response([
            'listType' => $userMovie->watched,
        ]);
    }

    /**
     * Removes a movie from the users list.
     *
     * @param  Request $request
     * @return string
     */
    public function remove(Request $request)
    {
        $userMovieId = $request->get('userMovieId');

        $userMovie = UserMovie::find($userMovieId);

        if (UserMovie::destroy($userMovieId) === 0) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        else return API::response([
            'listType' => $userMovie->watched,
        ]);
    }

    /**
     * Marks the given movie as watched.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsWatched(Request $request)
    {
        $userMovie = UserMovie::find($request->get('userMovieId'));
        $userMovie->watched = 1;

        if (!$userMovie->save()) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        else return API::response([
            'listType' => 0,
        ]);
    }

    /**
     * Filters the movies according to the passed search text and ordering type.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderingType' => ['bail', 'numeric', new IsCorrectOrderType($this->orderingTypes)],
            'listType' => ['required', 'numeric',  Rule::in(['1', '0'])],
        ]);

        if ($validator->fails()) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);

        // Build query to filter user movies
        $query = $this->getFilterQuery(
            $request->get('listType'),
            $request->get('searchText'),
            $request->get('orderingType')
        );

        // Fetch data
        $totalMovies = $query->get()->count();
        $movies = $query->limit(UserMovie::LIMIT)->get();

        return API::response([
            'pagination' => ($totalMovies > UserMovie::LIMIT) ? true : false,
            'movies' => $movies,
        ]);
    }

    /**
     * Finds the filtered movies for either the next or previous page.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderingType' => ['bail', 'numeric', new IsCorrectOrderType($this->orderingTypes)],
            'listType' => ['required', 'numeric',  Rule::in(['1', '0'])],
            'currentPage' => ['numeric', 'min:1'],
            'direction' => [Rule::in(['next', 'previous'])],
        ]);

        if ($validator->fails()) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);

        // Build query to filter user movies
        $query = $this->getFilterQuery(
            $request->get('listType'),
            $request->get('searchText'),
            $request->get('orderingType')
        );

        $currentPage = $request->get('currentPage');
        $wantedPage = $request->get('direction') === "next" ? $currentPage + 1 : $currentPage - 1;
        $allUserMovies = $query->get()->count();
        $numberOfPages = ceil($allUserMovies / UserMovie::LIMIT);
        $offset = ($wantedPage - 1) * UserMovie::LIMIT;

        if ($wantedPage === 0) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        if ($allUserMovies <= UserMovie::LIMIT) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        if ($wantedPage > $numberOfPages) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);

        $moviesForPage = $query->offset($offset)->limit(UserMovie::LIMIT)->get();

        return API::response([
            'newPage' => $wantedPage,
            'previousAvailable' => ($wantedPage > 1) ? true : false,
            'nextAvailable' => (($allUserMovies - $offset) > UserMovie::LIMIT) ? true : false,
            'movies' => $moviesForPage,
        ]);
    }

    /**
     * Builds the query to filter the user movies with the passed parameters.
     *
     * @param  $listType
     * @param  $searchText
     * @param  $orderingType
     * @return mixed
     */
    private function getFilterQuery($listType, $searchText, $orderingType) {
        $query = DB::table('user_movies')
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id')
            ->select('user_movies.id', 'movies.title', 'movies.imgPath')
            ->where('user_movies.watched', $listType)
            ->where('user_movies.user_id', Auth::id());

        if ($searchText !== null) {
            $query->where('movies.title', 'like', '%'.$searchText.'%');
        }

        if ($orderingType !== '0') {
            $type = $this->orderingTypes[$orderingType];
            $query->orderBy($type[0], $type[1]);
        } else {
            // Otherwise use default ordering
            $query->orderBy('user_movies.created_at', 'desc');
        }

        return $query;
    }

    /**
     * Makes an request to The Movie DB API to fetch movie information for an given
     * imdb id.
     *
     * @param  $imdbId
     * @throws IsNotAMovieException
     * @return array
     */
    private function findMovie($imdbId)
    {
        $request = new APIRequest("https://api.themoviedb.org/3/find/".$imdbId."?api_key=".$this->apiKey."&language=en-US&external_source=imdb_id");
        $response = $request->get()->asObject();

        if (count($response->movie_results) === 0) {
            throw new IsNotAMovieException("The given IMDb Id is not a movie.");
        }

        $request = new APIRequest("https://api.themoviedb.org/3/movie/".$response->movie_results[0]->id."?api_key=".$this->apiKey."&language=en-US");
        return $request->get()->asObject();
    }

    /**
     * Assigns the genres to the movie.
     *
     * @param $movie
     * @param $genres
     */
    private function assignGenres($movie, $genres)
    {
        $genreIds = [];

        foreach ($genres as $genre) {
            $existingGenre = Genre::where('name', $genre->name)
                ->first();
            if ($existingGenre === null) {
                $newGenre = Genre::create([
                    'name' => $genre->name,
                ]);
                array_push($genreIds, $newGenre->id);
            } else {
                array_push($genreIds, $existingGenre->id);
            }
        }

        $movie->genres()->attach($genreIds);
    }
}
