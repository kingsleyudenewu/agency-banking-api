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

    private $totalSystemDeduction = 0;

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

        self::$instance->logChannel = 'koloo';
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
            throw new \Exception('Attempted to re-compute commission');
        }


        $creator = User::findByInstance($this->contribution->creator);
        try {
            User::checkExistence($creator);
        } catch (Exceptions\UserNotFoundException $e) {
            $this->logError('The creator of this contribution was not found ID: ' .$this->contribution->id);
            throw new \Exception('contribution creator not found');
        }

        $this->logInfo('Calculating commission. ID: ' . $this->contribution->id . ' creator: ' . $creator->getName());

        $contributionAmount = $this->contribution->amount;

        $systemChargesPercent  = doubleval(number_format(settings('percent_to_charge')/100, 2));


        $this->totalSystemDeduction = $totalDeduction = percentOf($contributionAmount,$systemChargesPercent);


        $this->logInfo('Total amount to charge is: ' . $totalDeduction );

        $creatorCommissionPercent = $creator->isSuperAgent() ? $creator->getCommissionForAgent() +  $creator->getCommission(): $creator->getCommission();

        $creatorCommission = doubleval(number_format($creatorCommissionPercent/100, 2));

        $totalCommission = 100 - $creatorCommission;

        $creatorCommissionEarned = percentOf($totalDeduction, $creatorCommission);

        $creator->earnCommission($creatorCommissionEarned, $this->contribution);

        $rootUser = User::rootUser();
        if($creator->getParentID() !== $rootUser->getId() && $creator->getParent())
        {

            $parent = $creator->getParent();
            $parentCommission  = doubleval(number_format($parent->getCommission() / 100,2));

            $totalCommission  -= $parentCommission;

            $parentCommissionEarned = percentOf($totalDeduction, $parentCommission);
            $parent->earnCommission($parentCommissionEarned, $this->contribution);
        }


        $rootUser->earnCommission(percentOf($totalDeduction, $totalCommission),  $this->contribution);

        $this->contribution->updateCommissionComputed();

        return true;

    }

    public function getSystemDeductions()
    {
        return $this->totalSystemDeduction;
    }
}
