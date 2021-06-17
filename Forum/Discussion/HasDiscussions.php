<?php

namespace App\Forum\Discussion;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasDiscussions
{

    /**
     * A model may have multiple discussions.
     */
    public function discussions(): MorphToMany
    {
        return $this->morphToMany(
            \App\Models\Discussion::class,
            'model',
            'model_has_discussions',
            'model_id',
            'discussion_id'
        );
    }

    public function addDiscussion($discussion)
    {
        $model = $this->getModel();

        $this->roles()->create($discussion, false);
        $model->load('discussions');

        return $this;
    }


}
