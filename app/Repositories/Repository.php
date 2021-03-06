<?php 
namespace ProgrammingBlog\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use ProgrammingBlog\Models\BaseModel;
use ProgrammingBlog\Repositories\Contracts\RepositoryInterface;
use ProgrammingBlog\Repositories\Exceptions\RepositoryException;
use ProgrammingBlog\Repositories\Exceptions\ResourceNotFoundException;

/**
* Base Repository class
*
* @todo Ability to use cache on these getters
*/
abstract class Repository implements RepositoryInterface
{
    /**
     * App Context
     *
     * @var Container
     */
    private $app;

    /**
     * Model instance
     * 
     * @var BaseModel
     */
    protected $model;

    /**
     * Relatiopnships to include when fetching 
     * resource (eager loading).
     * 
     * @var array
     */
    protected $relationships = [];

    /**
     * Constructor
     *
     * @param Container $context 
     */
    public function __construct(Container $context)
    {
        $this->app = $context;
        $this->makeModel();
    }

    /**
     * Specify model class name
     * 
     * @return mixed
     */
    abstract function model();

    /**
     * Retrieves the model
     * @return BaseModel
     */
    private function getModel()
    {
        $model = $this->model;
        if (!empty($this->relationships)) {
            $model = $model->with($this->relationships);
        }
        return $model;
    }

    /**
     * Retrieves all model records
     *
     * @param  array  $columns properties to populate
     * @return App\Models\BaseModel
     */
    public function all($columns = array('*'))
    {
        return $this->getModel()->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id) : BaseModel
    {
        $model = $this->model;
        if (!empty($this->relationships)) {
            $model->with($this->relatiopnships);
        }
        return $model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getBy(array $conditions)
    {
        $queryBuilder = $this->model;

        foreach ($conditions as $column => $condition) {
            if (!is_array($condition)) {
                $condition = [$condition];
            }

            $queryBuilder = $queryBuilder->whereIn($column, $condition);
        }

        return $queryBuilder->get();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(int $id) : bool
    {
        return !empty($this->get($id));
    }

    /**
     * Throws if the resource does not exists.
     * 
     * @param  int    $id The resource ID
     * @throws ResourceNotFoundException
     */
    public function failIfNotExists(int $id)
    {
        if (!$this->exists($id)) {
            throw new ResourceNotFoundException('Can\'t find the required resource.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        return $this->get($id)->update($data);
    }

    public function updateBy($attr, $key, array $data)
    {
        return $this->model->where($attr, '=', $key)->update($data);
    }

    /**
     * Delete a storage model
     * 
     * @param  integer $id the model Id
     */
    public function delete($id)
    {
        return $this->model->delete();
    }

    /**
     * Populates the current repository with its corresponding model
     *
     * @return BaseModel
     */
    protected function makeModel() {
        $model = $this->app->make($this->model());
 
        if (!$model instanceof Model)
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
 
        return $this->model = $model;
    }

    /**
     * Adds relationships to eager load
     * 
     * @param  mixed $relationship 
     * @return Repository
     */
    public function include($relationship)
    {
        if (!is_array($relationship)) {
            $relationship = [$relationship];
        }

        $this->relationships = array_merge($this->relationships, $relationship);
        return $this;
    }

}