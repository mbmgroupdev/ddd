<?php
namespace App\Contracts\Hr;

use Illuminate\Support\Collection;

interface EmployeeInterface
{
   public function getEmployees($input, $date);
}