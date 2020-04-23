<?php

namespace App\Koloo;

use App\Wallet as Model;
use Illuminate\Support\Facades\Hash;

/**
 * Class Wallet
 *
 * @package \App\Koloo
 */
class Wallet
{

    const AMOUNT_HASH_TEMPLATE = '%s--%d-%s-%s';

    /**
     * Wallet
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
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

    public function getAmount() : int
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
    public function credit(int $amount): int
    {
        $this->model->amount += $amount;
        $this->model->touched = now();
        $this->model->save();

        $this->updateHash();

        return $this->getAmount();

    }

    /**
     * Debit the wallet and return the new value
     *
     * @param int $amount
     *
     * @return int
     */
    public function debit(int $amount): int
    {

        $this->model->amount -= $amount;
        $this->model->touched = now();
        $this->model->save();

        $this->updateHash();

        return $this->getAmount();
    }

    public function isValid(): bool
    {
        $money = money($this->model->amount, $this->getCurrency());
        if(!$this->model->touched && $money->isZero() && !$this->model->hash)
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
