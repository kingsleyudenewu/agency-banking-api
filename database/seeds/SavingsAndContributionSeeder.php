<?php

use Illuminate\Database\Seeder;
use App\Koloo\User as KolooUser;
use App\User as UserModel;

class SavingsAndContributionSeeder extends Seeder
{
    private   $agent;

    private  $customer;

    private $superAgent;

    private $savingCycle;

    public function __construct()
    {


    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->resetDb();

     //   $agent = new \App\Koloo\User(\App\User::hasRole(\App\User::ROLE_AGENT)->first());
       // $customer = new \App\Koloo\User(\App\User::hasRole(\App\User::ROLE_AGENT)->first());

        $this->generateUsers();
    }

    protected function resetDb()
    {

    }

    protected function generateUsers()
    {
        $this->superAgent  = new KolooUser($this->createUserByType('super-agent'));
        $this->superAgent->setParent(KolooUser::rootUser());

        $this->agent = new KolooUser($this->createUserByType('agent'));
        $this->agent->setParent($this->superAgent);

        $this->customer = new KolooUser($this->createUserByType('customer'));
        $this->customer->setParent($this->agent);
    }

    protected function createUserByType($type, $commission=0, $commissionForAgent=0)
    {
            $user = factory('App\User')->create(['country_code' => 'NG']);
            factory('App\Profile')->create(['user_id' => $user->id]);
            $role = \App\Role::where('name', $type)->first();
            $user->roles()->attach($role);

            \App\Wallet::start($user);

            return $user;
    }
}
