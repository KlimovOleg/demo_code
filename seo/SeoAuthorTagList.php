<?php

namespace frontend\models\seo;

use frontend\view_objects\TagAuthorListVo;

class SeoAuthorTagList extends SeoPageData
{
    /** @var TagAuthorListVo */
    private $vo;

    /** @var int */
    private $page;

    public function __construct(TagAuthorListVo $vo)
    {
        $this->vo = $vo;
        $this->page = $vo->page + 1;
    }

    public function getTitle(): string
    {
        $title = \Yii::t('seo', 'Книги {tag-name} читать онлайн', [
            'tag-name' => $this->vo->tag->name
        ]);

        if ($this->page > 1) {
            $title .= ' - ' . \Yii::t('common', 'Страница {page}', ['page' => $this->page]);
        }

        return $title;
    }

    public function getHeading(): string
    {
        return \Yii::t('common', 'Книги') . ': ' . $this->vo->tag->name;
    }

    public function getMetaDescription(): string
    {
        $description = \Yii::t('seo', 'Книги {tag-name} читать онлайн бесплатно на {app-name}', [
            'tag-name' => $this->vo->tag->name,
            'app-name' => 'Litnet'
        ]);

        if ($this->page > 1) {
            $description .= ' - ' . \Yii::t('common', 'Страница {page}', ['page' => $this->page]);
        }

        return $description;
    }

}