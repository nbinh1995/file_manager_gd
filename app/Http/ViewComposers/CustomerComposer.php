<?php

namespace App\Http\ViewComposers;

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\View\View;

class CustomerComposer
{
    protected $unpaidCustomers;

    public function __construct()
    {
        // $this->unpaidCustomers = Customer::unpaid(Setting::get('unpaid_amount', config('job.unpaid-amount')))
        //     ->get();
    }

    public function compose(View $view)
    {
        // $view->with('unpaidCount', $this->unpaidCustomers->count());
    }
}
