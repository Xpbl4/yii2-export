<?php

namespace xpbl4\export;

interface ExportInterface
{
	/**
	 * @return array data to export
	 */
	public function export();
}