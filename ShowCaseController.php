<?php

namespace frontend\controllers;

use common\helpers\Url;
use common\models\books_widgets\widgets\GainingPopularity;
use common\models\books_widgets\widgets\MostCommented;
use common\components\context\Context;
use frontend\services\WidgetService;
use common\models\Genre;
use common\models\books_widgets\widgets\HotNewBooks;
use common\models\books_widgets\widgets\TopLiveRating;
use common\models\books_widgets\widgets\RecentlyUpdatedBooks;
use frontend\view_objects\ShowCaseListVO;
use Yii;

class ShowCaseController extends FrontendController
{

    private $service;
    private $hotNewBooks;
    private $topLiveRating;
    private $recentlyUpdatedBooks;
    private $mostCommented;
    private $gainingPopularity;

    public function __construct($id, $module, WidgetService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->hotNewBooks = (new HotNewBooks())->forUser(Yii::$app->user);
        $this->topLiveRating = (new TopLiveRating())->forUser(Yii::$app->user);
        $this->recentlyUpdatedBooks = (new RecentlyUpdatedBooks())->forUser(Yii::$app->user);
        $this->mostCommented = (new MostCommented())->forUser(Yii::$app->user);
        $this->gainingPopularity = (new GainingPopularity())->forUser(Yii::$app->user);
    }

    public function actionIndex()
    {
        $alias = \Yii::$app->request->get('alias');
        $lang = Context::instance()->getContentLanguageCode();
        $genre = Genre::find()->where(['alias' => $alias, 'lang'=>$lang])->limit(1)->one();

        if ($genre) {
            $this->service->setGenreId($genre->id);
            $this->hotNewBooks->fromGenre($genre->id);
            $this->topLiveRating->fromGenre($genre->id);
            $this->recentlyUpdatedBooks->fromGenre($genre->id);
            $this->mostCommented->fromGenre($genre->id);
            $this->gainingPopularity->fromGenre($genre->id);
        }
        if ($this->theme != 'mobile_v2') {
            $alias = empty($alias)? 'all' : $alias;
            return $this->redirect('/top/'.$alias);
        }
        $params['alias']  = $alias;
        $vo = new ShowCaseListVO($params, $lang);

        return $this->render('index',
            [
                'topLiveRating' => $this->topLiveRating,
                'recentlyUpdatedBooks' =>  $this->recentlyUpdatedBooks,
                'hotNewBooks' => $this->hotNewBooks,
                'mostCommented' =>$this->mostCommented,
                'gainingPopularity' => $this->gainingPopularity,
                'genre' => $genre,
                'vo' => $vo
            ]);
    }
}