<?php
/**
 * This file is part of yii2-export.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/xpbl4/yii2-export
 */

namespace xpbl4\export\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;

/**
 * ExportAction
 *
 * Usage:
 *
 * ```php
 * public function actions()
 * {
 *     return [
 *         'export' => [
 *             'class' => 'xpbl4\export\actions\ExportAction',
 * 		       'model' => Post::class
 *         ]
 *     ];
 * }
 * ```
 *
 * @author Serge Mashkov
 *
 * @link https://github.com/xpbl4/yii2-export
 */
class ExportAction extends \yii\base\Action
{
	/**
	 * @var \yii\db\ActiveRecord model class name
	 */
	public $model;

	/**
	 * @var \yii\base\Model form class name
	 */
	public $form = '\xpbl4\export\models\ExportForm';

    /**
     * @var string Path to directory where files will be uploaded.
     */
    public $path;

	/**
	 * @var string Redirect url
	 */
	public $redirect;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->form === null)
            throw new InvalidConfigException('The "form" attribute must be set.');

        if ($this->model === null)
            throw new InvalidConfigException('The "model" attribute must be set.');

        if ($this->path === null)
			$this->path = \Yii::getAlias('@runtime/export');

		if ($this->redirect === null)
			$this->redirect = ['index'];
			//$this->redirect = Yii::$app->request->referrer;

		parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
	    ini_set('max_execution_time', 60);

	    $_result = Yii::$app->session->getFlash('export-result', ['limit' => -1, 'offset' => 0]);
	    $model = new $this->model;

		/** @var \xpbl4\export\models\ExportForm $form */
	    $form = new $this->form;
	    $form->setAttributes($_result);
	    $form->_time['start'] = microtime(true);

	    if ($form->load(Yii::$app->request->post())) {
		    $_exportPath = $this->path;
		    if (!empty($form->filename)) {
			    $_fileupload = pathinfo($form->filename);
			    $_fileexport = $_exportPath.'/'.md5($_fileupload['filename']).'.'.$_fileupload['extension'];
		    }

		    if (empty($form->filename) && ($_fileupload = UploadedFile::getInstance($form, 'filename'))) {
			    $form->filename = $_fileupload->name;

			    if (!file_exists($_exportPath)) mkdir($_exportPath, 0755, true);

			    $_fileexport = $_exportPath.'/'.md5($_fileupload->baseName).'.'.$_fileupload->extension;
			    $_fileupload->saveAs($_fileexport);
		    }

		    if ($form->validate() && $form->export($model, $_fileexport)) {
			    $type = 'success-timeout';
			    $message = ['File successfully exported into the database.'];
			    if (!empty($form->result['created'])) $message[] = Yii::t('app/export', 'The {count, plural, =0{no records} =1{one record} other{# records}} successfully created.', ['count' => count($form->result['created'])]);
			    if (!empty($form->result['updated'])) $message[] = Yii::t('app/export', 'The {count, plural, =0{no records} =1{one record} other{# records}} successfully updated.', ['count' => count($form->result['updated'])]);

			    if (!empty($form->result['error'])) {
				    $type = 'danger';
				    $message = ['File exported into the database.'];
				    if (count($form->result['complete']) > 0) {
					    $type = 'warning';
					    $message[] = Yii::t('app/export', 'The {count, plural, =0{no records} =1{one record} other{# records}} successfully exported.', ['count' => count($form->result['complete'])]);
				    }
				    $message[] = Yii::t('app/export', 'The {count, plural, =0{no records} =1{one record} other{# records}} not exported.', ['count' => count($form->result['error'])]);

				    $_summary = [];
				    foreach ($form->result['error'] as $_row => $_message) {
					    $_summary[] = '<li>row #'.$_row.': '.$_message[0].'</li>';
				    }
				    if (!empty($_summary)) $message[] = '<div class="box-overflow"><ul>'.implode('', $_summary).'</ul></div>';
			    }

			    Yii::$app->getSession()->setFlash($type, implode('<br />', $message));

			    return $this->controller->redirect($this->redirect);
		    }
	    }

	    if (Yii::$app->request->isAjax || Yii::$app->request->isPjax)
		    return $this->controller->renderAjax('@xpbl4/export/views/export', ['model' => $form, 'source' => $model]);

	    return $this->controller->render('@xpbl4/export/views/export', ['model' => $form, 'source' => $model]);
	}
}
