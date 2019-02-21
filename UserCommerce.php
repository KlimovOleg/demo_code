<?php

namespace common\models\commerce;

use common\models\Books;
use common\models\docs\Contract;
use common\models\docs\DocAuthorExclusive;
use common\models\docs\UserDocuments;
use common\models\User;

class UserCommerce
{
    /** @var User */
    private $user;

    /** @var string lang version, code like 'ru', 'es', 'en' etc... */
    private $lang;

    /** @var UserCommerceChecker */
    private $checker;

    /** @var UserCommerceSettings */
    private $settings;

    public function __construct(User $user, string $lang)
    {
        $this->user = $user;
        $this->lang = $lang;
    }

    public function checker(): UserCommerceChecker
    {
        if(!$this->checker) {
            $this->checker = new UserCommerceChecker($this->user, $this->lang);
        }
        return $this->checker;
    }

    /**
     * @return UserCommerceSettings
     * @throws \yii\base\UserException
     */
    public function settings(): UserCommerceSettings
    {
        if(!$this->settings) {
            $this->settings = new UserCommerceSettings($this->user, $this->lang);
        }
        return $this->settings;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function documents(): UserDocuments
    {
        return $this->user->documents($this->lang);
    }

    public function issetApprovedContractSub(): bool
    {
        return Contract::find()
            ->where([
                'user_id' => $this->user->id,
                'type' => Contract::TYPE_SUB_COMMON,
                'status' => Contract::STATUS_ACCEPTED,
                'lang' => $this->lang
            ])
            ->count() > 0;
    }

    public function issetApprovedContractSale(): bool
    {
        return Contract::find()
                ->where([
                    'user_id' => $this->user->id,
                    'type' => Contract::TYPE_SALE_COMMON,
                    'status' => Contract::STATUS_ACCEPTED,
                    'lang' => $this->lang
                ])
                ->count() > 0;
    }

    public function hasCommerceStatus(): bool
    {
        return $this->issetApprovedContractSub();
    }

    public function isExclusiveAuthor(): bool
    {
        return DocAuthorExclusive::find()
            ->where([
                'user_id' => $this->user->id,
                'lang' => $this->lang,
                'status' => DocAuthorExclusive::STATUS_APPROVED
            ])->count() > 0;
    }

    public function canGetCommerceStatus(): bool
    {
        if ($this->hasCommerceStatus()) {
            return true;
        }

        return $this->checker()->checkRegularReaders()
            && $this->checker()->checkFollowers()
            && $this->checker()->checkFreeBook();
    }

    /**
     * @return array|Books[]
     */
    public function getPaidBooks(): array
    {
        return Books::find()
            ->with('author')
            ->innerJoin('selling_books', 'books.id = selling_books.book_id')
            ->where(['selling_books.user_id' => $this->user->id])
            ->orderBy('id DESC')
            ->all();
    }

    /**
     * @TODO: Получение комиссии/роялти автора по-новому
     * @return int
     */
    public function getFee(): int
    {
        if ($this->user->fee) {
            return $this->user->fee;
        }

        return 0;
    }

    /**
     * Есть ли соавтор
     * @return bool
     */
    public function issetCoAuthor(): bool
    {
        return $this->user->documents($this->lang)->issetCoAuthor();
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}