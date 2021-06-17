<?php

namespace App\Forum\Parent;

use App\Models\User;

trait Parentable
{
    static function formatModel($data, $type)
    {
        switch ($type) {
            default:
            case 'topic':
                return self::linkFragment($data,
                    [
                        'type' => $type
                    ]);
            case 'opinion':
                return self::opinionFragment($data, $type);
            case 'discussion':
                return self::discussionFragment($data, $type);
            case 'comment':
                $discussion = $data['discussion'];
                return [
                    'id'     => $data['id'],
                    'body'   => $data['body'],
                    'type'   => 'comment',
                    'parent' => self::discussionFragment($discussion, 'discussion')
                ];
        }
    }

    static function linkFragment($data, $rest = [])
    {
        return array_merge([
            'id'    => $data['id'],
            'slug'  => $data['slug'],
            'title' => $data['title'],
        ], $rest);
    }

    static function opinionFragment($data, $type)
    {
        $topic = self::linkFragment($data['topic']);

        return self::linkFragment($data,
            [
                'type'   => $type,
                'parent' => $topic
            ]);
    }

    static function discussionFragment($data, $type)
    {

        $parent = $data ? self::discussionParent($data->holder->first()) : null;

        return self::linkFragment($data,
            [
                'type'   => $type,
                'parent' => $parent
            ]);
    }

    static function discussionParent($parent)
    {

        if (!$parent) {
            return null;
        }
        $type = self::getTypeFromModel($parent->pivot->model_type);

        switch ($type) {
            default:
            case 'topic':
                return [
                    'id'    => $parent['id'],
                    'slug'  => $parent['slug'],
                    'title' => $parent['title'],
                    'type'  => $type
                ];
            case 'opinion':
                return [
                    'id'     => $parent['id'],
                    'slug'   => $parent['slug'],
                    'title'  => $parent['title'],
                    'type'   => $type,
                    'parent' => self::linkFragment(
                        $parent->topic,
                        ['type' => 'topic']
                    )
                ];
            case 'category':
                return [
                    'id'   => $parent['id'],
                    'name' => $parent['name'],
                    'type' => $type,
                ];
        }

    }

    static function getTypeFromModel($type)
    {
        $subject_type = explode('\\', $type);
        return strtolower(end($subject_type));
    }
}
