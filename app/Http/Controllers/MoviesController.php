<?php

namespace App\Http\Controllers;

use App\Constants\ErrorCodeConstants;
use App\Constants\ErrorMessageConstants;
use App\Exceptions\IsNotAMovieException;
use App\Genre;
use App\Movie;
use App\Rules\ImdbId;
use App\Rules\IsCorrectListType;
use App\Rules\IsCorrectOrderType;
use App\Rules\IsCorrectWatchType;
use App\Rules\IsUniqueMovie;
use App\UserMovie;
use App\Util\API;
use App\Util\APIRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        $this->apiKey = env('THE_MOVIE_DB_API_KEY');
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
                ErrorMessageConstants::VALIDATION_ERRORS,
                $validator->errors()->messages()
            );
        }

        $validator = Validator::make($request->all(), [
            'watched' => ['bail', 'numeric', new IsCorrectWatchType()],
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

            $movie = Movie::create([
                'imdb_id' => $response->imdb_id,
                'themoviedb_id' => $response->id,
                'title' => $response->title,
                'plot' => $response->overview,
                'runtime' => $response->runtime,
                'year' => Carbon::createFromFormat('Y-m-d', $response->release_date)->format('Y'),
                'imgPath' => substr($response->poster_path, 1, strlen($response->poster_path)),
            ]);

            $this->assignGenres($movie, $response->genres);
        }

        UserMovie::create([
            'watched' => $request->get('watched'),
            'user_id' => Auth::id(),
            'movie_id' => $movie->id,
        ]);

        return API::response([
            'movie_id' => $movie->id,
            'title' => $movie->title,
            'imgPath' => $movie->imgPath,
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

        if (UserMovie::destroy($userMovieId) === 0) return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        else return API::response();
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
        else return API::response();
    }

    /**
     * Filters the movies based on the passed filter settings.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderingType' => ['bail', 'numeric', new IsCorrectOrderType($this->orderingTypes)],
            'listType' => ['required', new IsCorrectListType()],
        ]);

        if ($validator->fails()) {
            return API::error(ErrorCodeConstants::UNKNOWN_ERROR);
        }

        $query = DB::table('user_movies')
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id')
            ->select('user_movies.id', 'movies.title', 'movies.imgPath');

        if ($request->get('searchText') !== null) {
            $query->where('movies.title', 'like', '%'.$request->get('searchText').'%');
        }

        if ($request->get('orderingType') != 0) {
            $type = $this->orderingTypes[$request->get('orderingType')];
            $query->orderBy($type[0], $type[1]);
        }

        return API::response($query->get()->toArray());
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
