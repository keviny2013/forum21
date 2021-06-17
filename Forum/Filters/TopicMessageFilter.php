<?php

namespace App\Forum\Filters;

use App\Models\Topic;

class TopicMessageFilter extends Filter
{ 
    /**
     * Filter by Topics.
     * Get all the topic messages from specific topic.
     *
     * @param $topic_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function topicMessage($topic_id) 
    {
       
        $topic = Topic::find($topic_id);
       
        $topicMessages = $topic ? $topic->topicMessages()->pluck('id')->toArray() : [];
        
        return $this->builder->where('id', $topicMessages);
    }
}
