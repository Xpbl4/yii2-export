<?php

namespace xpbl4\export;

/**
 * Writes CSV files.
 * @package xpbl4\export
 */
class CsvWriter extends BaseWriter
{
	/**
	 * Writes to a CSV file.
	 * @return string filename
	 */
	public function write()
	{
		$exporter = $this->source;
		$filename = \Yii::$app->runtimePath.'/'.uniqid().'.csv';

		$fp = fopen($filename, 'w');

		foreach ($exporter->export() as $rowNum => $row) {
			fputcsv($fp, $row);
		}

		return $filename;
	}
}
