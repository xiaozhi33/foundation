<?php
include_once 'Sample_Header.php';

// Template processor instance creation
echo date('H:i:s') , ' Creating new TemplateProcessor instance...' , EOL;
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('resources/1.docx');

// Will clone everything between ${tag} and ${/tag}, the number of times. By default, 1.
//$templateProcessor->cloneBlock('CLONEME', 3);

// Everything between ${tag} and ${/tag}, will be deleted/erased.
//$templateProcessor->deleteBlock('DELETEME');


// setting to DB
$templateProcessor ->setValue("niandu" , "2018");
$templateProcessor ->setValue("xdx" , "11111111111.231");
$templateProcessor ->setValue("xdx-heji" , "22222222.212");

echo date('H:i:s'), ' Saving the result document...', EOL;
$templateProcessor->saveAs('results/1.docx');

echo getEndingNotes(array('Word2007' => 'docx'));
if (!CLI) {
    include_once 'Sample_Footer.php';
}
