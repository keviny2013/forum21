<?php

namespace App\Forum\Filters;

use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;
use App\Models\User;
use App\Models\Topic;

class DiscussionFilter extends Filter
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

        $discussions = array_unique($this->getCategoryDiscussions($category));

        return $this->builder->whereIn('id', $discussions);
    }

    /**
     * Get all discussions from the current and child categories
     *
     * @param $category
     * @return array
     */
    private function getCategoryDiscussions($category)
    {
        $ids = [];

        $categoryDiscussions = $category->discussions()->pluck('id')->toArray();

        $topicDiscussions = $category->topics->map(function ($topic) {
            return $topic->discussions()->pluck('id');
        })->toArray();

        $topicDiscussions = count($topicDiscussions) ? array_merge(...$topicDiscussions) : [];

        $opinionDiscussions = $category->topics->map(function ($topic) {
            $discussions = $topic->opinions->map(function ($opinion) {
                return $opinion->discussions()->pluck('id');
            })->toArray();
            $discussions = count($discussions) ? array_merge(...$discussions) : [];
            return $discussions;
        })->toArray();

        $opinionDiscussions = count($opinionDiscussions) ? array_merge(...$opinionDiscussions) : [];


        if (count($categoryDiscussions) || count($topicDiscussions) || count($opinionDiscussions)) {
            array_push($ids, ...array_merge($categoryDiscussions, $topicDiscussions, $opinionDiscussions));
        }

        foreach ($category->child as $cat) {
            array_push($ids, ...$this->getCategoryDiscussions($cat));
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

        $topicIds = $language ? $language->discussions()->pluck('id')->toArray() : [];

        return $this->builder->whereIn('id', $topicIds);
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
                    ->orWhere('goal', 'LIKE', '%' . $q . '%');
            });
    }
}
