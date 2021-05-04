<?php
namespace App\Contracts\Hr;

use Illuminate\Support\Collection;

interface BonusInterface
{
   public function getBonusReport($input, $data);

   // public function getBonusByMonth($yearMonth);

}