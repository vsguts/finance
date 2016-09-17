<?php

namespace app\components\reports;

use Yii;

interface ReportInterface
{

    public function getReportName();

    public function execute();

    public function exportGetColumns();

    public function exportPrepareData($data);

}
