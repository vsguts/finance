<?php

namespace app\components\reports;

interface ReportInterface
{

    public function getReportName();

    public function execute();

    public function exportGetColumns();

    public function exportPrepareData($data);

}
