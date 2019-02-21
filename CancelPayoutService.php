<?php

namespace common\models\commerce\payouts;

use common\models\BalanceHistory;
use common\models\User;
use common\traits\ErrorsTrait;

class CancelPayoutService
{
    use ErrorsTrait;

    /** @var Payout */
    private $payout;

    /** @var string */
    private $lang;

    /** @var float */
    private $amount;

    /** @var User */
    private $user;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
        $this->amount = $payout->amount;
        $this->lang = $payout->lang;
        $this->user = $this->findUser();
    }

    public function cancel(): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();

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

        if (!$this->deletePayout()) {
            $transaction->rollBack();
            $this->setError('Unable to delete payout');
            return false;
        }

        $transaction->commit();

        return true;
    }

    private function deletePayout(): bool
    {
        return (bool) $this->payout->delete();
    }

    private function changeRoyaltyBalance(): bool
    {
        $royalty = $this->user->royalty($this->lang)->getOrCreateEntity();
        $royalty->balance += $this->amount;
        return $royalty->save();
    }

    private function saveBalanceHistory(): bool
    {
        $currentBalance = $this->user->royalty($this->lang)->getBalance();

        $balanceHistory = new BalanceHistory([
            'user_id' => $this->user->id,
            'fh_id' => 0,
            'operation_type' => BalanceHistory::CORRECTION,
            'before' => $currentBalance,
            'sum' => $this->payout->amount,
            'after' => $currentBalance + $this->amount
        ]);

        return $balanceHistory->save();
    }

    private function findUser()
    {
        return User::findOne($this->payout->user_id);
    }

}