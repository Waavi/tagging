<?php namespace Waavi\Tagging\Traits;

trait TaggableRepository
{

    /**
     *  Retrieve all records with any given tag.
     *
     *  @param array $tagNames Arrat with tags names.
     *  @param array $related Related object to include.
     *  @param integer $perPage Number of records to retrieve per page. If zero the whole result set is returned.
     *  @return \Illuminate\Database\Eloquent\Model
     */
    public function withAnyTag($tagNames, $related = [], $perPage = 0)
    {
        $results = $this->model
            ->with($related)
            ->withAnyTag($tagNames)
            ->orderBy('created_at', 'DESC');
        return $perPage ? $results->paginate($perPage) : $results->get();
    }

    /**
     *  Retrieve all records with all given tags.
     *
     *  @param array $tagNames Arrat with tags names.
     *  @param integer $perPage Number of records to retrieve per page. If zero the whole result set is returned.
     *  @return \Illuminate\Database\Eloquent\Model
     */
    public function withAllTags($tagNames, $related = [], $perPage = 0)
    {
        $results = $this->model
            ->with($related)
            ->withAllTags($tagNames)
            ->orderBy('created_at', 'DESC');
        return $perPage ? $results->paginate($perPage) : $results->get();
    }

}
