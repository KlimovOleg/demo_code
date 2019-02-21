<?php

namespace frontend\models\seo;

abstract class SeoPageData implements ISeoPageData
{
    protected $title;
    protected $heading;
    protected $keywords;
    protected $description;
    protected $seoText;
    protected $canonicalUrl;

    abstract public function getTitle(): string;

    public function getHeading(): string
    {
        return (string) $this->heading;
    }

    public function getMetaKeywords(): string
    {
        return (string) $this->keywords;
    }

    public function getMetaDescription(): string
    {
        return (string) $this->description;
    }

    public function getSetText(): string
    {
        return (string) $this->seoText;
    }

    public function getCanonicalUrl(): string
    {
        return (string) $this->canonicalUrl;
    }
}