<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Models\Transaction\BankAccount;
use App\Models\Transaction\Payout as PayoutModel;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentServiceContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Razorpay X
 * Payout Service
 */
class PayoutAction implements PaymentProviderPayoutContract
{

    protected RazorpayApi $api;
    protected PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider;
    protected ?PayoutModel $payoutModel = null;
    /**
     * @var mixed|null
     */
    private array $payeeContact=[];
    /**
     * @var mixed|null
     */
    private array $payeeFundAccount=[];
    protected ?BankAccount $bankAccountModel = null;


    public function __construct(RazorpayApi $api,PaymentProviderContract $paymentProvider)
    {
        $this->api = $api;
        $this->paymentProvider = $paymentProvider;
    }



    public function fetch($id)
    {
        return $this->api->payout->fetch($id)->toArray();
    }











    public function toBank(PayoutModel|Model $payout):array
    {
        try {

            $this->payoutModel = $payout;
            $this->payoutModel->loadMissing('user','event');

            $this->bankAccountModel = $this->payoutModel->user->defaultBankAccount();
            throw_if(is_null($this->bankAccountModel),'no default/primary bank account fourd for '.$this->payoutModel->user->email);

            $this->prepare($this->bankAccountModel);


            if (empty($this->payoutModel->provider_ref_id) && !$this->payoutModel->paid)
            {
                // Initiate Payment to Bank Account
                $result = $this->api->payout->create([
                    "account_number" => $this->paymentProvider->getCompanyBankAccount(),
                    "fund_account_id" => $this->payeeFundAccount['id'],
                    "amount" => $payout->net_payable_amount->getAmount(),
                    "currency" => $payout->net_payable_amount->getCurrency()->getCurrency(),
                    "mode" => $this->paymentProvider->payoutMode(),
                    "purpose" => "payout",
                    "queue_if_low_balance" => true,
                    "reference_id" => $this->payeeContact['reference_id'],
                    "narration" => config('app.name')." Fund Transfer",
                ])->toArray();

                return array_merge($result,['bank_account_id' => $this->bankAccountModel->id]);


            }
            return [];

        }catch (\Throwable $e)
        {
            report($e);

            return [];
        }

    }

    protected function getCreatedContactInfo()
    {
        return  $this->api->contact->create([
            'name' => $this->payoutModel->user->name,
            'email' => $this->payoutModel->user->email,
            'contact' => $this->payoutModel->user->contact,
            'type' => 'vendor',
            'reference_id' => md5($this->payoutModel->user->email),
            'notes' => [

            ],
        ])->toArray();
    }


    protected function getCreatedFundAccountInfo()
    {
        return $this->api->fund_account->create([
            "contact_id" => $this->payeeContact['id'],
            "account_type" => "bank_account",
            "bank_account" => [
                "name" => $this->bankAccountModel->account_name,
                "ifsc" => $this->bankAccountModel->ifsc,
                "account_number" => $this->bankAccountModel->account_no
            ],
        ])->toArray();
    }


    protected function prepare(BankAccount $bankAccount)
    {
        if (!is_null($bankAccount->payout_config))
        {
            // Get FundContact
            if (isset($bankAccount->payout_config[BankAccount::FUND_CONTACT]) && !empty($bankAccount->payout_config[BankAccount::FUND_CONTACT]))
            {
                $this->payeeContact = $bankAccount->payout_config[BankAccount::FUND_CONTACT];
            }else{
                $this->payeeContact = $this->getCreatedContactInfo();
            }
            // Get FundAccount
            if (isset($bankAccount->payout_config[BankAccount::FUND_ACCOUNT]) && !empty($bankAccount->payout_config[BankAccount::FUND_ACCOUNT]))
            {
                $this->payeeFundAccount = $bankAccount->payout_config[BankAccount::FUND_ACCOUNT];
            }else{
                $this->payeeFundAccount = $this->getCreatedFundAccountInfo();
            }


        }else{
            $this->payeeContact = $this->getCreatedContactInfo();
            $this->payeeFundAccount = $this->getCreatedFundAccountInfo();


        }

        $this->bankAccountModel->payout_config = [
            BankAccount::FUND_CONTACT => $this->payeeContact,
            BankAccount::FUND_ACCOUNT => $this->payeeFundAccount,
        ];
        $this->bankAccountModel->save();


    }



}
