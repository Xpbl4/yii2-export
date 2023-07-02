<?php
/**
 * @author: Sergey Mashkov (serge@asse.com)
 * Date: 4/24/23 10:59 AM
 * Project: asse-db-template
 */

namespace xpbl4\export;

class ExportAssetBungle extends \yii\web\AssetBundle
{
	public $sourcePath = '@xpbl4/export/assets';

	public $js = ['js/jquery-select-columns.js'];

	public $css = [];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
