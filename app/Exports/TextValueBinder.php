<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class TextValueBinder extends DefaultValueBinder
{
    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value) && preg_match('/^\d{15,}$/', $value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
