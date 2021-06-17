<?php

namespace App\Forum\Filters;

use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;
use App\Models\User;
use App\Models\Topic;

class CommunityFilter extends Filter
{
    /**
     * Filter by author username.
     * Get all the topics by the user with given username.
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function author($username)
    {
        $user = User::whereUsername($username)->first();

        $userId = $user ? $user->id : null;

        return $this->builder->whereUserId($userId);
    }

    /**
     * Filter by joined username.
     * Get all the topics favorited by the user with given username.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function me()
    {

        $user = auth()->user();

        $topicIds = $user ? $user->communities()->pluck('id')->toArray() : [];

        return $this->builder->cloneWithoutBindings(['private'])->whereIn('id', $topicIds);
    }

    /**
     * Filter by query.
     * Get all the topics where title or description contains given text.
     *
     * @param $q
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query($q)
    {
        return $this->builder
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', '%' . $q . '%')
                    ->orWhere('about', 'LIKE', '%' . $q . '%')
                    ->orWhere('facts', 'LIKE', '%' . $q . '%');
            });
    }

}
