<?php

namespace common\models\commerce\payouts;

use common\components\context\LangVersion;
use common\models\BalanceHistory;
use common\models\FinanceHistory;
use common\models\Notices;
use common\models\User;
use common\traits\ErrorsTrait;

class CreatePayoutService
{
    use ErrorsTrait;

    /** @var User */
    private $user;

    /** @var string */
    private $lang;

    /** @var float */
    private $amount;

    /** @var int */
    private $registerPaymentId;

    /** @var Payout */
    private $payout;

    /** @var int */
    private $adminId;

    /** @var FinanceHistory|null */
    private $financeHistory;

    public function __construct(User $user, string $lang, float $amount)
    {
        $this->user = $user;
        $this->lang = $lang;
        $this->amount = $amount;
    }

    public function setRegisterPaymentId(int $id)
    {
        $this->registerPaymentId = $id;
    }

    public function create(): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();

        if (!$this->savePayout()) {
            $transaction->rollBack();
            $this->setError('Unable to save payout');
            return false;
        }

        if (!$this->saveFinanceHistory()) {
            $transaction->rollBack();
            $this->setError('Unable to save finance history');
            return false;
        }

        if (!$this->changeRoyaltyBalance()) {
            $transaction->rollBack();
            $this->setError('Unable to change royalty');
            return false;
        }

        if (!$this->saveBalanceHistory()) {
            $transaction->rollBack();
            $this->setError('Unable to save balance history');
            return false;
        }

        $transaction->commit();

        $this->sendNotices();

        return true;
    }

    private function savePayout(): bool
    {
        $this->payout = new Payout();
        $this->payout->user_id = $this->user->id;
        $this->payout->balance_before = $this->user->royalty($this->lang)->getBalance();
        $this->payout->amount = $this->amount;
        $this->payout->balance_after = $this->payout->balance_before - $this->amount;
        $this->payout->lang = $this->lang;
        $this->payout->ccy = LangVersion::LANG_VERSIONS_CONFIG[$this->lang]['ccy'];

        if($this->registerPaymentId) {
            $this->payout->reg_payment_id = $this->registerPaymentId;
        }

        if ($res = $this->issetDouble($this->payout)) {
            $this->setError('Similar payout has been already created');
            return false;
        }

        return $this->payout->save();
    }

    private function issetDouble(Payout $payout): bool
    {
        return Payout::find()
                ->where([
                    'user_id' => $payout->user_id
                ])
                ->andWhere('date > ADDDATE(NOW(), INTERVAL -3 SECOND)')
                ->count() > 0;
    }

    private function changeRoyaltyBalance(): bool
    {
        $royalty = $this->user->royalty($this->lang)->getOrCreateEntity();
        $royalty->balance -= $this->amount;
        return $royalty->save();
    }

    private function saveFinanceHistory(): bool
    {
        $history = new FinanceHistory();

        $history->object_id = $this->user->id;
        $history->user_id = $this->getAdminId();
        $history->sum = $this->payout->amount;
        $history->operation_type = FinanceHistory::PAYOUT;
        $history->pay_type_id = 0;
        $history->price = 0;
        $history->pay_fee = 0;
        $history->our_fee = 0;
        $history->income = 0;
        $history->outcome = 0;
        $history->profit = 0;
        $history->lang = $this->payout->lang;
        $history->ccy = $this->payout->ccy;
        $history->real_income_ccy = $this->payout->ccy;

        $this->financeHistory = $history;

        return $this->financeHistory->save();
    }

    private function saveBalanceHistory(): bool
    {
        $balanceHistory = new BalanceHistory([
            'user_id' => $this->user->id,
            'fh_id' => $this->financeHistory->id,
            'operation_type' => BalanceHistory::PAYOUT,
            'before' => $this->payout->balance_before,
            'sum' => $this->payout->amount,
            'after' => $this->payout->balance_after
        ]);

        return $balanceHistory->save();
    }

    private function sendNotices()
    {
        Notices::createNotice($this->user->id, $this->payout->id, Notices::PAYOUT_FOR_AUTHOR, $this->user->id);
    }

    public function setAdminId(int $id)
    {
        $this->adminId = $id;
    }

    private function getAdminId(): int
    {
        if($this->adminId) {
            return $this->adminId;
        }

        return \Yii::$app->user->getId();
    }

}