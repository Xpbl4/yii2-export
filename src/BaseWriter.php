<?php

namespace xpbl4\export;

/**
 * Writes files.
 * @package xpbl4\export
 */
abstract class BaseWriter extends \yii\base\BaseObject
{
	/**
	 * @var ExportInterface|\yii\db\ActiveRecord|string exporter instance or class name
	 */
	public $source;

	/**
	 * @var array options for the exporter
	 */
	public $options = [];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!$this->source) {
			throw new \yii\base\InvalidConfigException('The "source" property must be set.');
		}
		if (is_string($this->source)) {
			$this->source = new $this->source;
		}
	}

	/**
	 * Writes to a file.
	 * @return string filename
	 */
	public abstract function write();
}