<?php
namespace App\Contracts\Hr;

use Illuminate\Support\Collection;

interface JobCardInterface
{
   protected function holidays(): Collection;

   protected function leave(): Collection;

}