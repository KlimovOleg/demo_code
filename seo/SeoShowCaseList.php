<?php

namespace frontend\models\seo;

use frontend\view_objects\ShowCaseListVO;

class SeoShowCaseList extends SeoPageData
{
    /** @var ShowCaseListVO */
    private $vo;

    private $appName = 'Litnet';

    public function __construct(ShowCaseListVO $vo)
    {
        $this->vo = $vo;
        $this->initValues();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    private function initValues()
    {
        $genreName = $this->vo->getGenreName();
        $genreDesc = $this->vo->genre ? $this->vo->genre->seo_description : null;
        $this->keywords = $this->vo->genre ? $this->vo->genre->seo_keywords : null;

        $this->title = \Yii::t('seo', 'Книги {genre} читать онлайн бесплатно на Самиздат {app-name} - витрина жанра', [
            'genre' => \Yii::t('database', $genreName),
            'app-name' => $this->appName,
        ]);
        $this->heading = \Yii::t('seo', 'Книги в жанре {genre}', [
            'genre' => $genreName,
        ]);

        if ($genreName) {
            $this->description = \Yii::t('seo', 'Коллекция книг в жанре {genre}, можно бесплатно читать онлайн на самиздате {app-name}. {genreDesc}', [
                'genre' => \Yii::t('database', $genreName),
                'genreDesc' => $genreDesc,
                'app-name' => $this->appName,
            ]);
        } else {
            $this->description = \Yii::t('seo', 'Коллекция книг бесплатно читать онлайн на самиздате {app-name}.', [
                'app-name' => $this->appName,
            ]);
        }

        $page = $this->vo->page + 1;

        if ($page > 1) {
            $this->title = \Yii::t('seo', 'Страница {page}: {title}', [
                'page' => $page,
                'title' => $this->title
            ]);
            $this->description = \Yii::t('seo', 'Страница {page} : {description}', [
                'page' => $page,
                'description' => $this->description
            ]);
        }
    }
}