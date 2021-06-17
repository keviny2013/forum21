<?php

namespace App\Forum\Filters;

use App\Models\Category;
use App\Models\Language;
use App\Models\User;

class OpinionFilter extends Filter
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
     * Filter by category.
     * Get all the topics from specific category.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function category($id)
    {
        $category = Category::find($id);

        $opinions = array_unique($this->getCategoryOpinions($category));

        return $this->builder->whereIn('id', $opinions);
    }

    /**
     * Get all opinions from the current and child categories
     *
     * @param $category
     * @return array
     */
    private function getCategoryOpinions($category)
    {
        $ids = [];
        $opinions = $category->topics->map(function ($topic) {
            return $topic->opinions()->pluck('id');
        })->toArray();

        if (count($opinions)) {
            array_push($ids, ...array_merge(...$opinions));
        }

        foreach ($category->child as $cat) {
            array_push($ids, ...$this->getCategoryOpinions($cat));
        }
        return $ids;
    }

    /**
     * Filter by language.
     * Get all the topics from specific language.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function language($id)
    {
        $language = Language::find($id);

        $opinions = $language->topics->map(function ($topic) {
            return $topic->opinions()->pluck('id');
        })->toArray();

        $opinions = array_merge(...$opinions);

        return $this->builder->whereIn('id', $opinions);
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
                    ->orWhere('facts', 'LIKE', '%' . $q . '%')
                    ->orWhere('solution', 'LIKE', '%' . $q . '%')
                    ->orWhere('about', 'LIKE', '%' . $q . '%');
            });
    }
}
