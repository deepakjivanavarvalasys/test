<?php
namespace App\Repository\ReportsRepository;

use App\Models\Reports;
use Illuminate\Support\Facades\DB;

class ReportsRepository implements ReportsInterface
{

    Public function __construct(Reports $reports)
    {
        $this->Reports=$reports;
    }

    public function show()
    {

        $test=$this->Reports->totalrecordsAgentdata();

        return $test;
    }
}