<?php

namespace Ingenious\Isupport\Concerns;

use Illuminate\Support\Facades\DB;
use Ingenious\Isupport\Models\Incident;


trait QueriesIsupportDatabase
{
    protected $query;

    /**
     * Set up the base query
     *
     * @return $this
     */
    protected function baseQuery()
    {
        $scope = $this->archive_flag ? "archive" : "active";

        $this->query = Incident::$scope();

        return $this;
    }

    //public function execute()
    //{
    //    return $this->query->paginate(5000);
    //}

    /**
     * Defer to the query object for any nonexistent params
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name,$args) {
       return $this->query->$name(...$args);
    }

    /**
     * Set a where clause on the query
     *
     * @param mixed ...$params
     * @return $this
     */
    protected function where(...$params)
    {
        $this->query = $this->query->where(...$params);

        return $this;
    }

    /**
     * Set a whereIn clause on the query
     *
     * @param mixed ...$params
     * @return $this
     */
    protected function whereIn(...$params)
    {
        $this->query = $this->query->whereIn(...$params);

        return $this;
    }

    /**
     * Set a whereBetween clause on the query
     *
     * @param mixed ...$params
     * @return $this
     */
    protected function whereBetween(...$params)
    {
        $this->query = $this->query->whereBetween(...$params);

        return $this;
    }
}