<?php

namespace App\Models\Financial;

use App\Models\Transaction as BaseTransaction;

class Transaction extends BaseTransaction
{
    public function processedBy()
    {
        return $this->processor();
    }
}
