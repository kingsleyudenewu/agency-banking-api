<?php

namespace App\Koloo;

use App\Traits\LogTrait;
use App\Wallet as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class Wallet
 *
 * @package \App\Koloo
 */
class Wallet
{
    use LogTrait;

    const AMOUNT_HASH_TEMPLATE = '%s--%d-%s-%s';

    /**
     * Wallet
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->logChannel = 'koloo';
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getId(): string
    {
        return $this->model->id;
    }

    public function getOwner(): User
    {
        return User::find($this->getModel()->user->id);
    }

    public function getAmount()
    {
        return $this->model->amount;
    }

    /**
     * Increase the amount field and return the new value
     *
     * @param int $amount
     *
     * @return int
     */
    public function credit($amount)
    {

        $this->logInfo('Crediting wallet with ' .  $amount);

        try {
            DB::beginTransaction();
            $this->model->amount += $amount;
            $this->model->touched = now();
            $this->model->save();
            $this->updateHash();

            DB::commit();

            $this->logInfo('Done wallet with ' .  $amount);

        } catch (\Exception $e)
        {
            $this->logError($e->getMessage());
            DB::rollBack();
        }

        return $this->getAmount();

    }

    /**
     * Debit the wallet and return the new value
     *
     * @param int $amount
     *
     * @return int
     */
    public function debit($amount)
    {

        $this->logInfo('debiting wallet with ' .  $amount);

        try {
            DB::beginTransaction();
            $this->model->amount -= $amount;
            $this->model->touched = now();
            $this->model->save();

            $this->updateHash();

            DB::commit();
            $this->logInfo('done debiting  wallet with ' .  $amount);
        } catch (\Exception $e)
        {
            $this->logError($e->getMessage());
            DB::rollBack();
        }

        return $this->getAmount();
    }

    public function isValid(): bool
    {
        if(!$this->model->touched && $this->model->amount == 0  && !$this->model->hash)
        {
            return true;
        }
        return Hash::check($this->getAmountHashPlain(), $this->model->hash);

    }

    private function getCurrency()
    {
        return $this->model->currency ?
            strtoupper($this->model->currency) :
            settings('default_currency', 'NGN');
    }

    private function updateHash()
    {
        $this->model->hash  = Hash::make($this->getAmountHashPlain());
        $this->model->save();
    }

    private function getAmountHashPlain(): string
    {

        return sprintf(static::AMOUNT_HASH_TEMPLATE,
            env('APP_KEY'), $this->getAmount(),
            $this->getId(), $this->model->created_at
        );
    }
}
