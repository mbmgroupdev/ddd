<?php
namespace App\Contracts\Hr;

use Illuminate\Support\Collection;

interface SalaryInterface
{
   public function getSalaryReport($input, $data);

   // public function getSalaryByMonth($yearMonth);

}