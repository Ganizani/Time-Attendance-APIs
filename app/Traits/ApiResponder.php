<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/13/18
 * Time: 8:10 PM
 */

namespace  App\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponder {

    public function successResponse($data, $code){

        return response()->json([
            'data'  => $data,
            'error' => '',
            'code'  => $code ],
            $code
        );
    }

    protected function errorResponse($message, $code){

        return response()->json([
            'data'  => '',
            'error' => $message,
            'code'  => $code ],
            $code
        );
    }

    protected function showAll(Collection $collection, $code = 200){

        if($collection->isEmpty()){
            return $this->successResponse($collection, $code);
        }

        //Paginate Data
        $collection = $this->paginate($collection);

        return $this->successResponse($collection, $code);
    }

    protected function showList(Collection $collection, $code = 200){

        if($collection->isEmpty()){
            return $this->successResponse($collection, $code);
        }

        return $this->successResponse($collection, $code);
    }

    protected function showOne(Collection $instance, $code = 200){

        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200){

        return $this->successResponse($message, $code);
    }

    protected function paginate(Collection $collection){

        $rules = [
            'per_page' => 'integer|min:2|max:200',
        ];

        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();

        $per_page = 10;

        if(request()->has('per_page')){
            $per_page = (int) request()->per_page;
        }

        $offeset = ($page - 1) * $per_page;

        $results = $collection->slice($offeset, $per_page)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $per_page , $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function sortData(Collection $collection){

        if(request()->has('sort_by')){

            $attribute = request()->sort_by;

            if(request()->has('order') && request()->order == "DESC"){
                $collection = $collection->sortByDesc($attribute);
            }
            else $collection = $collection->sortBy($attribute);
        }

        return $collection;
    }

    protected function filterData(Collection $collection){

        foreach (request()->query() as $query => $value) {
            $attribute = $query;

            if(isset($attribute, $value)){
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;
    }
}