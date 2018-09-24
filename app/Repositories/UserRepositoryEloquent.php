<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\UserRepository;
use App\Entities\User;
use App\Utilities\Response\Responses;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param array $request
     * @param null $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function allPaginate($request, $limit = null, $columns = ['*'], $method = "paginate")
    {
        $this->applyScope();
        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;
        $query = $this->model->select($columns);
        $requestData = $request->all();
        unset($requestData['page']);
        try {
            $query->where($requestData);
            $results = $query->{$method}($limit, $columns);
            $resultsPaginate = Responses::response200($results);
        }
        catch(\Illuminate\Database\QueryException $exception) {
            $messageExplode = explode('column ',$exception->getMessage());
            $columnExplode = explode("'",$messageExplode[1])[1];
            $resultsPaginate = Responses::response409($columnExplode);
        }
        $this->resetModel();

        return $this->parserResult($resultsPaginate);
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyScope();
        $limit = config('repository.pagination.limit', 15);
        $results = $this->model->where('id', $id)->paginate($limit, $columns);
        $results->appends(app('request')->query());
        if ($results->count() > 0) {
            $resultsPaginate = Responses::response200($results);
            $this->resetModel();

            return $this->parserResult($resultsPaginate);
        }
        $resultsPaginate = Responses::response404();
        $this->resetModel();

        return $this->parserResult($resultsPaginate);
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        if(isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }
        $model = $this->model->newInstance($attributes);
        try {
            $model->save();
            $resultsPaginate = Responses::response201($model);
        }
        catch(\Illuminate\Database\QueryException $exception) {
            $messageExplode = explode('column ',$exception->getMessage());
            $columnExplode = explode("'",$messageExplode[1])[1];
            $resultsPaginate = Responses::response409($columnExplode);
        }
        $this->resetModel();

        return $this->parserResult($resultsPaginate);
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $this->applyScope();

        $resultsPaginate = Responses::response404();
        $model = $this->model->where('id', $id)->first();
        if(isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }
        if ($model != null) {
            $model->fill($attributes);
            try {
                $model->save();
                $resultsPaginate = Responses::response200Update($model);
            }
            catch(\Illuminate\Database\QueryException $exception) {
                $messageExplode = explode('column ',$exception->getMessage());
                $columnExplode = explode("'",$messageExplode[1])[1];
                $resultsPaginate = Responses::response409($columnExplode);
            }
        }

        $this->resetModel();

        return $this->parserResult($resultsPaginate);
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $this->applyScope();

        $resultsPaginate = Responses::response404();
        $model = $this->model->where('id', $id)->first();
        if ($model != null) {
            $model->delete();
            $resultsPaginate = Responses::response200Delete($model);
        }

        $this->resetModel();

        return $this->parserResult($resultsPaginate);
    }
    
}
