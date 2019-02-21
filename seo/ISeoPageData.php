<?php

namespace frontend\models\seo;

interface ISeoPageData
{
    public function getTitle(): string;

    public function getHeading(): string;

    public function getMetaKeywords(): string;

    public function getMetaDescription(): string;

    public function getSetText(): string;

    public function getCanonicalUrl(): string;
}