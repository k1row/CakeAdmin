<?php
$this->Csv->addRow($th);
foreach ($td as $t) {
    $this->Csv->addRow($t['Conversion']);
}
$this->Csv->setFilename($filename);
echo $this->Csv->render(true, 'sjis', 'utf-8');
?>
