<?php

namespace frontend\models\seo;

use common\components\context\Context;
use frontend\view_objects\GenreListVO;

class SeoGenreList extends SeoPageData
{
    /** @var GenreListVO */
    private $vo;

    private $appName = 'Litnet';

    public function __construct(GenreListVO $vo)
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
        $existPeriod = $this->vo->sortPeriod && $this->vo->sortPeriod > 0 ;
        $lang = Context::instance()->getContentLanguageCode();

        switch ($this->vo->sort) {
            case 'rate' :
                if ($lang == 'ru' && $this->vo->genre && $this->vo->genre->title && !$existPeriod) {
                    $this->title = $this->vo->genre->title;
                    $this->heading = $this->vo->genre->heading;
                    $this->description = $this->vo->genre->seo_description;
                    $this->keywords = $this->vo->genre->seo_keywords;
                } else {
                    $this->title = \Yii::t('seo', 'Книги {genre} читать онлайн бесплатно на Самиздат {app-name}', [
                        'genre' => \Yii::t('database', $genreName),
                        'app-name' => $this->appName,
                    ]);
                    $this->heading = \Yii::t('seo', 'Книги в жанре {genre}', [
                        'genre' => \Yii::t('database', $genreName),
                    ]);

                    if ($genreName) {
                        $this->description = \Yii::t('seo', 'Коллекция лучших книг в жанре {genre}, можно бесплатно читать онлайн на самиздате {app-name}. {genreDesc}', [
                            'genre' => \Yii::t('database', $genreName),
                            'genreDesc' => $genreDesc,
                            'app-name' => $this->appName,
                        ]);
                    } else {
                        $this->description = \Yii::t('seo', 'Коллекция лучших книг бесплатно читать онлайн на самиздате {app-name}.', [
                            'app-name' => $this->appName,
                        ]);
                    }
                }

                break;
            case 'latest' :
                $this->title = \Yii::t('seo', 'Новые книги {genre} читать онлайн бесплатно на Самиздат {app-name}', [
                    'genre' => \Yii::t('database', $genreName),
                    'app-name' => $this->appName,
                ]);
                $this->heading = \Yii::t('seo', 'Книги в жанре {genre}: новинки и недавно обновленные', [
                    'genre' => $genreName,
                ]);

                if ($genreName) {
                    $this->description = \Yii::t('seo', 'Коллекция новых книг в жанре {genre}, можно бесплатно читать онлайн на самиздате {app-name}. {genreDesc}', [
                        'genre' => \Yii::t('database', $genreName),
                        'genreDesc' => $genreDesc,
                        'app-name' => $this->appName,
                    ]);
                } else {
                    $this->description = \Yii::t('seo', 'Коллекция новых книг бесплатно читать онлайн на самиздате {app-name}.', [
                        'app-name' => $this->appName,
                    ]);
                }
                break;
            case 'popular' :
                $this->title = \Yii::t('seo', 'Популярные книги {genre} читать онлайн бесплатно на Самиздат {app-name}', [
                    'genre' => \Yii::t('database', $genreName),
                    'app-name' => $this->appName,
                ]);
                $this->heading = \Yii::t('seo', 'Популярные книги в жанре {genre}', [
                    'genre' => $genreName,
                ]);

                if ($genreName) {
                    $this->description = \Yii::t('seo', 'Коллекция популярных книг в жанре {genre}, можно бесплатно читать онлайн на самиздате {app-name}. {genreDesc}', [
                        'genre' => \Yii::t('database', $genreName),
                        'genreDesc' => $genreDesc,
                        'app-name' => $this->appName,
                    ]);
                } else {
                    $this->description = \Yii::t('seo', 'Коллекция популярных книг бесплатно читать онлайн на самиздате {app-name}.', [
                        'app-name' => $this->appName,
                    ]);
                }
                break;
            case 'comments' :
                $this->title = \Yii::t('seo', 'Обсуждаемые книги {genre} читать онлайн бесплатно на Самиздат {app-name}', [
                    'genre' => \Yii::t('database', $genreName),
                    'app-name' => $this->appName,
                ]);
                $this->heading = \Yii::t('seo', 'Обсуждаемые книги в жанре {genre}', [
                    'genre' => $genreName,
                ]);

                if ($genreName) {
                    $this->description = \Yii::t('seo', 'Коллекция обсуждаемых книг в жанре {genre}, можно бесплатно читать онлайн на самиздате {app-name}. {genreDesc}', [
                        'genre' => \Yii::t('database', $genreName),
                        'genreDesc' => $genreDesc,
                        'app-name' => $this->appName,
                    ]);
                } else {
                    $this->description = \Yii::t('seo', 'Коллекция обсуждаемых книг бесплатно читать онлайн на самиздате {app-name}.', [
                        'app-name' => $this->appName,
                    ]);
                }
                break;
            case 'show-case' :
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
                break;
            default:
                $this->title = \Yii::t('seo', 'Книги {genre} читать онлайн бесплатно на Самиздат {app-name}', [
                    'genre' => \Yii::t('database', $genreName),
                    'app-name' => $this->appName,
                ]);
                $this->heading = \Yii::t('seo', 'Книги в жанре {genre}', [
                    'genre' => \Yii::t('database', $genreName),
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
                break;
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