<?php
namespace App\Contracts\Merch;

use Illuminate\Support\Collection;

interface PoBomInterface
{
   public function bom($orderId, $supplierId = null): Collection;
}