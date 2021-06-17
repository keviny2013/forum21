<?php

namespace App\Forum\Moderator;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasModerators
{

    private function _moderators(): MorphToMany
    {
        return $this->morphToMany(
            \App\Models\User::class,
            'model',
            'model_has_moderators',
            'model_id',
            'user_id'
        );
    }

    /**
     * A model may have multiple moderators.
     */
    public function moderators(): MorphToMany
    {
        return $this->_moderators()->where('status', 'accepted');
    }

    /**
     * A model may have multiple moderators.
     */
    public function moderatorRequests(): MorphToMany
    {
        return $this->_moderators()->where('status', 'request');
    }

    public function addModerator($moderator)
    {
        $model = $this->getModel();

        $this->roles()->create($moderator, false);
        $model->load('moderators');

        return $this;
    }


}
