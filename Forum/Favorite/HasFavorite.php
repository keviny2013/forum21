<?php

namespace App\Forum\Favorite;

use App\Models\Topic;

trait HasFavorite
{
    /**
     * Favorite the given topic.
     *
     * @param Topic $topic
     * @return mixed
     */
    public function favorite(Topic $topic)
    {
        if (! $this->hasFavorited($topic))
        {
            return $this->favorites()->attach($topic);
        }
    }

    /**
     * Unfavorite the given topic.
     *
     * @param Topic $topic
     * @return mixed
     */
    public function unFavorite(Topic $topic)
    {
        return $this->favorites()->detach($topic);
    }

    /**
     * Get the topics favorited by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Topic::class, 'favorites', 'user_id', 'topic_id')->withTimestamps();
    }

    /**
     * Check if the user has favorited the given topic.
     *
     * @param Topic $topic
     * @return bool
     */
    public function hasFavorited(Topic $topic)
    {
        return !! $this->favorites()->where('topic_id', $topic->id)->count();
    }
}
