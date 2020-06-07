<?php

namespace App\Koloo;

use App\Contribution;
use App\Saving;
use App\Traits\LogTrait;

/**
 * Class SavingCommission
 *
 * @package \App\Koloo
 */
class SavingCommission
{
    protected $contribution;

    private static $instance = null;

    use LogTrait;


    private function __construct()
    {

    }

    /**
     * Saving is the amount a user is saving on a daily or weekly basis.
     *
     * @param \App\Contribution $contribution
     *
     * @return static|null
     */
    public static function getInstance(Contribution $contribution)
    {
        if (self::$instance == null)
        {
            self::$instance = new static();
        }

        self::$instance->logChannel = 'SavingCommission';
        self::$instance->setContribution($contribution);
        return self::$instance;
    }

    public function setContribution(Contribution $contribution)
    {
        $this->contribution  = $contribution;
    }

    /**
     *
     * @throws \Exception
     */
    public function computeCommission()
    {

        if($this->contribution->commissionComputed())
        {
            $this->logInfo('Attempted to re-compute commission: ID: '. $this->contribution->id);
            return;
        }


        $creator = User::findByInstance($this->contribution->creator);
        try {
            User::checkExistence($creator);
        } catch (Exceptions\UserNotFoundException $e) {
            $this->logError('The creator of this contribution was not found ID: ' .$this->contribution->id);
            throw new \Exception('contribution creator not found');
        }

        $this->logInfo('Calculating commission. ID: ' . $this->contribution->id . ' creator: ' . $creator->getName());

        $amountToCharge = percentOf($this->contribution->amount, settings('percent_to_charge'));
        $this->logInfo('Total amount to charge is: ' . number_format($amountToCharge/100,2) );

        $totalCommission = 100000 - $creator->getCommission();
        $creatorCommission = percentOf($amountToCharge, $creator->getCommission());

        $creator->earnCommission($creatorCommission, $this->contribution);

        $rootUser = User::rootUser();
        if($creator->getParentID() !== $rootUser->getId() && $creator->getParent())
        {
            $parent = $creator->getParent();
            $totalCommission  -= $parent->getCommission();
            $parentCommission = percentOf($amountToCharge, $parent->getCommission());
            $parent->earnCommission($parentCommission, $this->contribution);
        }

        $rootUser->earnCommission(percentOf($amountToCharge, $totalCommission),  $this->contribution);

    }
}
