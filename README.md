# yii2-export

Extension for the Yii2 framework for exporting data with [PhpSpreadsheet].

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

    php composer.phar require --prefer-dist xpbl4/yii2-export "*"

or add

    "xpbl4/yii2-export": "*"

to the require section of your `composer.json` file.

## Usage

Implement the `ExportInterface`:

    class Contact extends \yii\db\ActiveRecord implements ExportInterface
    {
        ...

        public function export() {
            return $this->attributes();
        }
    }

Now you can export data using `ExcelWriter` and your class as the `source`:

    public function actionExport()
    {
		$writer = new ExcelWriter(['source' => Contact::className()]);
        $filename = $writer->write('Xlsx');
        Yii::$app->response->sendFile($filename, 'contacts.xlsx')->send();
    }

[PhpSpreadsheet]: https://github.com/PHPOffice/PhpSpreadsheet