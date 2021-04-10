<?php
namespace App\Contracts\Merch;

use Illuminate\Support\Collection;

interface OrderInterface
{
   public function store(): Collection;
}