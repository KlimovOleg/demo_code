<?php

namespace frontend\models\seo;

use frontend\view_objects\TagListVo;

class SeoTagList extends SeoPageData
{
    /** @var TagListVo */
    private $vo;

    /** @var int */
    private $page;
    private $isSeoTags = false;

    public function __construct(TagListVo $vo)
    {
        $this->vo = $vo;
        $this->page = $vo->page + 1;
        if ($this->vo->tag->seo) {
            $this->isSeoTags = true;
        }
    }

    public function getTitle(): string
    {
        if ($this->isSeoTags) {
            $title = $this->vo->tag->seo->title;
        } else {
            $title = \Yii::t('seo', 'Книги {tag-name} читать онлайн', [
                'tag-name' => $this->vo->tag->name
            ]);
        }
        if ($this->page > 1) {
            $title .= ' - ' . \Yii::t('common', 'Страница {page}', ['page' => $this->page]);
        }

        return $title;
    }

    public function getHeading(): string
    {
        if ($this->isSeoTags) {
            $heading = $this->vo->tag->seo->title;
        } else {
            $heading = \Yii::t('common', 'Книги') . ': ' . $this->vo->tag->name;
        }
        return $heading;
    }

    public function getMetaDescription(): string
    {
        if ($this->isSeoTags) {
            $description = $this->vo->tag->seo->description;
        } else {
            $description = \Yii::t('seo', 'Книги {tag-name} читать онлайн бесплатно на {app-name}', [
                'tag-name' => $this->vo->tag->name,
                'app-name' => 'Litnet'
            ]);
        }
        if ($this->page > 1) {
            $description .= ' - ' . \Yii::t('common', 'Страница {page}', ['page' => $this->page]);
        }

        return $description;
    }

    public function getKeywords()
    {
        if ($this->isSeoTags) {
            $keywords = $this->vo->tag->seo->keywords;
        } else {
            $keywords = null;
        }
        return $keywords;
    }

}