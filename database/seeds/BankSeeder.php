<?php

use App\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::truncate();
        $banks = [array(
            "id" => "2f539150-5d95-11e8-b467-f9307086ff91", "name" => "ACCESS BANK", "code" => "044"), array(
            "id" => "2f53d0a0-5d95-11e8-b137-638077fc3bf7", "name" => "ACCESSMOBILE", "code" => "323"), array(
            "id" => "2f540000-5d95-11e8-87b3-9d9b8b5a094f", "name" => "ASO SAVINGS AND LOANS", "code" => "401"), array(
            "id" => "2f542c60-5d95-11e8-a3c6-31fc766289f6", "name" => "CELLULANT", "code" => "317"), array(
            "id" => "2f545b30-5d95-11e8-803e-499faca2f0c0", "name" => "CENTRAL BANK OF NIGERIA", "code" => "001"), array(
            "id" => "2f548b30-5d95-11e8-a95e-f15816e6591f", "name" => "CITIBANK", "code" => "023"), array(
            "id" => "2f54b720-5d95-11e8-8274-653d1a52560d", "name" => "CORONATION MERCHANT BANK", "code" => "559"), array(
            "id" => "2f54e600-5d95-11e8-b700-fbd8e6175358", "name" => "CORPORETTI", "code" => "310"), array(
            "id" => "2f550d30-5d95-11e8-b729-7fb1cb958d9a", "name" => "COVENANT MICROFINANCE BANK", "code" => "551"), array(
            "id" => "2f553610-5d95-11e8-80d4-45555c7de2b4", "name" => "DIAMOND BANK", "code" => "063"), array(
            "id" => "2f555ef0-5d95-11e8-8763-ed874bae23e0", "name" => "EARTHOLEUM (QIK QIK)", "code" => "302"), array(
            "id" => "2f558850-5d95-11e8-9320-d17d08339211", "name" => "ECOBANK NIGERIA", "code" => "050"), array(
            "id" => "2f55b040-5d95-11e8-961d-0949d08da35a", "name" => "ECOMOBILE", "code" => "307"), array(
            "id" => "2f55d7a0-5d95-11e8-86c8-d53a12391bca", "name" => "EKONDO MICROFINANCE BANK", "code" => "562"), array(
            "id" => "2f55ff40-5d95-11e8-916e-27b3b5c4a868", "name" => "ENTERPRISE BANK", "code" => "084"), array(
            "id" => "2f562b10-5d95-11e8-8d05-3d629e0dc3b8", "name" => "EQUITORIAL TRUST BANK", "code" => "040"), array(
            "id" => "2f565900-5d95-11e8-988c-1d9a55dad071", "name" => "E-TRANZACT", "code" => "306"), array(
            "id" => "2f5687c0-5d95-11e8-bc4e-59f1f978f332", "name" => "FBN M-MONEY", "code" => "309"), array(
            "id" => "2f56b400-5d95-11e8-8ff0-5ba725430884", "name" => "FBN MORTGAGES", "code" => "413"), array(
            "id" => "2f56e370-5d95-11e8-99cd-e1fb857098e0", "name" => "FETS (MY WALLET)", "code" => "314"), array(
            "id" => "2f570ed0-5d95-11e8-9b0d-0f7c8584553f", "name" => "FIDELITY BANK", "code" => "070"), array(
            "id" => "2f5739c0-5d95-11e8-8ced-d91e2e9381c7", "name" => "FIDELITY MOBILE", "code" => "318"), array(
            "id" => "2f576540-5d95-11e8-af59-0750ddb2a459", "name" => "FINATRUST MICROFINANCE BANK", "code" => "608"), array(
            "id" => "2f578ce0-5d95-11e8-9c3d-b9413c101347", "name" => "FIRST BANK OF NIGERIA", "code" => "011"), array(
            "id" => "2f57b580-5d95-11e8-b8c6-1fc8b9376aa9", "name" => "FIRST CITY MONUMENT BANK", "code" => "214"), array(
            "id" => "2f57dec0-5d95-11e8-b9b6-fd18b00c673a", "name" => "FIRST INLAND BANK", "code" => "085"), array(
            "id" => "2f5812a0-5d95-11e8-92a7-a74aa5ed5109", "name" => "FORTIS MICROFINANCE BANK", "code" => "501"), array(
            "id" => "2f583a40-5d95-11e8-8a4a-41302faceb4e", "name" => "FORTIS MOBILE", "code" => "308"), array(
            "id" => "2f586230-5d95-11e8-a415-d1054e753a63", "name" => "FSDH", "code" => "601"), array(
            "id" => "2f588920-5d95-11e8-afae-b136ddbb4d86", "name" => "GT MOBILE MONEY", "code" => "315"), array(
            "id" => "2f58afe0-5d95-11e8-9f1f-43c57b0dd56e", "name" => "GUARANTY TRUST BANK", "code" => "058"), array(
            "id" => "2f58d5a0-5d95-11e8-8922-7fcb6fc328c6", "name" => "HEDONMARK", "code" => "324"), array(
            "id" => "2f5901b0-5d95-11e8-88ff-73ed3db9edc9", "name" => "HERITAGE BANK", "code" => "030"), array(
            "id" => "2f592760-5d95-11e8-981f-f706946a0111", "name" => "IMPERIAL HOMES MORTGAGE BANK", "code" => "415"), array(
            "id" => "2f594fb0-5d95-11e8-ade3-cbde5470bcd2", "name" => "INTERCONTINENTAL BANK", "code" => "069"), array(
            "id" => "2f597820-5d95-11e8-bc58-9f4b7a3be311", "name" => "JAIZ BANK", "code" => "301"), array(
            "id" => "2f599ee0-5d95-11e8-91d6-b14b2b04dad8", "name" => "JUBILEE LIFE", "code" => "402"), array(
            "id" => "2f59c5a0-5d95-11e8-9afe-958db308a22d", "name" => "KEGOW", "code" => "303"), array(
            "id" => "2f59f010-5d95-11e8-9d2f-8b820a6d9796", "name" => "KEYSTONE BANK", "code" => "082"), array(
            "id" => "2f5a16c0-5d95-11e8-9c9b-fd15e8d1c525", "name" => "MAINSTREET BANK", "code" => "014"), array(
            "id" => "2f5a3f40-5d95-11e8-be64-6dbbd4c7829a", "name" => "MIMONEY (POWERED BY INTELLIFIN)", "code" => "330"), array(
            "id" => "2f5a7180-5d95-11e8-8484-4df84c19386b", "name" => "M-KUDI", "code" => "313"), array(
            "id" => "2f5a9910-5d95-11e8-9689-8b5becabb0a0", "name" => "MONETIZE", "code" => "312"), array(
            "id" => "2f5abd60-5d95-11e8-a6a5-39478df9fd8c", "name" => "MONEYBOX", "code" => "325"), array(
            "id" => "2f5ae1c0-5d95-11e8-a50f-cf21b9aea81f", "name" => "NEW PRUDENTIAL BANK", "code" => "561"), array(
            "id" => "2f5b05a0-5d95-11e8-a1e7-f17024ef4b2a", "name" => "NPF MFB", "code" => "552"), array(
            "id" => "2f5b3190-5d95-11e8-bc17-ef18870dc107", "name" => "OCEANIC BANK", "code" => "056"), array(
            "id" => "2f5b54c0-5d95-11e8-a24d-2d538e28f748", "name" => "OMOLUABI SAVINGS AND LOANS", "code" => "606"), array(
            "id" => "2f5b7a90-5d95-11e8-9615-99aa4b7c13e7", "name" => "ONE FINANCE", "code" => "565"), array(
            "id" => "2f5b9fd0-5d95-11e8-8c4a-c1fa76fa9a0b", "name" => "PAGA", "code" => "327"), array(
            "id" => "2f5bc4e0-5d95-11e8-b1ae-e73b9d3f889d", "name" => "PAGE MFBANK", "code" => "560"), array(
            "id" => "2f5beaa0-5d95-11e8-a414-3d896399c3ec", "name" => "PARALLEX", "code" => "502"), array(
            "id" => "2f5c0fd0-5d95-11e8-af5d-e7619109fcb8", "name" => "PARKWAY (READY CASH)", "code" => "311"), array(
            "id" => "2f5c3550-5d95-11e8-b432-4182621456f8", "name" => "PAYATTITUDE ONLINE", "code" => "329"), array(
            "id" => "2f5c59a0-5d95-11e8-88c3-17ff0c54fd67", "name" => "PAYCOM", "code" => "304"), array(
            "id" => "2f5c7ea0-5d95-11e8-92c1-d1486d3317a7", "name" => "PROVIDUS BANK", "code" => "101"), array(
            "id" => "2f5ca890-5d95-11e8-8a6e-e348e468d830", "name" => "SAFETRUST MORTGAGE BANK", "code" => "403"), array(
            "id" => "2f5cd280-5d95-11e8-83de-63d2ecb899d4", "name" => "SEED CAPITAL MICROFINANCE BANK", "code" => "609"), array(
            "id" => "2f5cfa20-5d95-11e8-b2c4-3364014d18d8", "name" => "SKYE BANK", "code" => "076"), array(
            "id" => "2f5d20b0-5d95-11e8-bf68-71a8496e0c59", "name" => "STANBIC IBTC BANK", "code" => "221"), array(
            "id" => "2f5d4650-5d95-11e8-95b3-05922294a4db", "name" => "STANBIC MOBILE", "code" => "304"), array(
            "id" => "2f5d6c30-5d95-11e8-9ed9-e5d126efc545", "name" => "STANDARD CHARTERED BANK", "code" => "068"), array(
            "id" => "2f5d92a0-5d95-11e8-b8ce-d7de3be5bf40", "name" => "STERLING BANK", "code" => "232"), array(
            "id" => "2f5db850-5d95-11e8-bf41-6d5608586004", "name" => "STERLING MOBILE", "code" => "326"), array(
            "id" => "2f5ddc40-5d95-11e8-8ddd-b9dc0c8b8add", "name" => "SUNTRUST", "code" => "100"), array(
            "id" => "2f5e09b0-5d95-11e8-b2e8-5fa06bbaccad", "name" => "TEASY MOBILE", "code" => "319"), array(
            "id" => "2f5e2df0-5d95-11e8-a817-d9cddfdbdda4", "name" => "TRUSTBOND", "code" => "523"), array(
            "id" => "2f5e54e0-5d95-11e8-a491-45ca016d21d1", "name" => "U-MO", "code" => "316"), array(
            "id" => "2f5e7e90-5d95-11e8-bc78-8936c0d90b51", "name" => "UNION BANK OF NIGERIA", "code" => "032"), array(
            "id" => "2f5ead00-5d95-11e8-9240-d1f073aadac2", "name" => "UNITED BANK FOR AFRICA", "code" => "033"), array(
            "id" => "2f5edcc0-5d95-11e8-a8b0-5992bdabfd03", "name" => "UNITY BANK", "code" => "215"), array(
            "id" => "2f5f0950-5d95-11e8-a01f-c32e3f976296", "name" => "VFD MICROFINANCE BANK", "code" => "566"), array(
            "id" => "2f5f35c0-5d95-11e8-a739-fd92101f293b", "name" => "VISUAL ICT", "code" => "328"), array(
            "id" => "2f5f6360-5d95-11e8-8d9f-61ebaa55dc22", "name" => "VTNETWORK", "code" => "320"), array(
            "id" => "2f5f9300-5d95-11e8-ae04-bd118647c369", "name" => "WEMA BANK", "code" => "035"), array(
            "id" => "2f5fc270-5d95-11e8-8f6f-5b15b3d7d706", "name" => "ZENITH BANK", "code" => "057"), array(
            "id" => "2f5ff4f0-5d95-11e8-afb5-e3191244edce", "name" => "ZENITH MOBILE", "code" => "322")];
        foreach ($banks as $bank){
            Bank::create($bank);
        }
    }
}
