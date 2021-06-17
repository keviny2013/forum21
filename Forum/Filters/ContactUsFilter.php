<?php

namespace App\Forum\Filters;

use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;
use App\Models\User;
use App\Models\Topic;

class ContactUsFilter extends Filter
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
     * Filter by favorited username.
     * Get all the topics favorited by the user with given username.
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function favorited($username)
    {
        $user = User::whereUsername($username)->first();

        $topicIds = $user ? $user->favorites()->pluck('id')->toArray() : [];

        return $this->builder->whereIn('id', $topicIds);
    }

    /**
     * Filter by tag name.
     * Get all the topics tagged by the given tag name.
     *
     * @param $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function tags($names)
    {
        $topics = array_map(function ($name) {
            return Topic::whereHas('tags', function ($q) use ($name) {
                $q->where('name', $name);
            })->get()->pluck('id')->toArray();
        }, $names);

        if (count($topics) > 1) {
            $topicIds = array_intersect(...$topics);
        } else {
            $topicIds = $topics[0];
        }


        return $this->builder->whereIn('id', $topicIds);
    }

    /**
     * Filter by community ids.
     * Get all the topics tagged by the given tag name.
     *
     * @param $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function community($id)
    {
        /*TODO: Adjust this page*/
        $query = $this->builder->getQuery();
        unset($query->wheres[0]);
        $where_bindings = $query->getRawBindings()['where'];
        unset($where_bindings[0]);
        unset($where_bindings[1]);
        $query->setBindings(array_values($where_bindings));
        $query->wheres = array_values($query->wheres);


        return $this->builder
            ->whereHas('community', function ($q) use ($id) {
                $q->where('id', $id);
            });
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

        $topicIds = array_unique($this->getCategoryTopics($category));

        return $this->builder->whereIn('id', $topicIds);
    }

    /**
     * Get all topics from the current and child categories
     *
     * @param $category
     * @return array
     */
    private function getCategoryTopics($category)
    {
        $topics = [];
        array_push($topics, ...$category->topics()->pluck('id')->toArray());
        foreach ($category->child as $cat) {
            array_push($topics, ...$this->getCategoryTopics($cat));
        }
        return $topics;
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

        $topicIds = $language ? $language->topics()->pluck('id')->toArray() : [];

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
                    ->orWhere('about', 'LIKE', '%' . $q . '%')
                    ->orWhere('facts', 'LIKE', '%' . $q . '%');
            });
    }

}
