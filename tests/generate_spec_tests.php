<?php

//$testCases = array('C:\src\php-investigations\tolerant-php-parser\tests\..\php-langspec\tests\traits\traits.phpt');
$testCases = glob(__DIR__ . "\\..\\php-langspec\\tests\\**\\*.phpt");
$outTestCaseDir = __DIR__ . "\\cases\\php-langspec\\";
mkdir($outTestCaseDir);
file_put_contents($outTestCaseDir . "README.md", "Auto-generated from php/php-langspec tests");


foreach ($testCases as $idx=>$filename) {
    $myFile = file_get_contents($filename);

    $dirPrefix = $outTestCaseDir . basename(dirname($filename)) . "\\" ;
    $baseName = basename(basename($filename), ".phpt");
    mkdir($dirPrefix);

    $titleRegex = '/(?<=={15} |-{15} )\X*?(?= ={15}| -{15})(?=[\w\W]*--EXPECT)/';
    $codeRegex = '/(?<=={15}\\\n";|-{15}\\\n";)\X*?(?=echo "=* |echo "-* |--EXPECT)/';

    preg_match_all($titleRegex, $myFile, $titles);
    preg_match_all($codeRegex, $myFile, $codes);

    if (count($titles[0]) >= 1) {
        foreach ($titles[0] as $idx=>$title) {
            $fileToWrite = $dirPrefix . $baseName . "-" . pascalCase($title) . ".php";

            $code = "/* Auto-generated from php/php-langspec tests */" . $codes[0][$idx];
//            echo $title, $code;
            file_put_contents($fileToWrite, $code);
            if (!file_exists($fileToWrite . ".tree")) {
                file_put_contents($fileToWrite . ".tree", $code);
            }
            if (!file_exists($fileToWrite . ".tokens")) {
                file_put_contents($fileToWrite . ".tokens", $code);
            }
        }
    } else {
        $codeRegex = '/(?<=--FILE--)\X*?(?=--EXPECT)/';
        preg_match_all($codeRegex, $myFile, $codes);
        $fileToWrite = $dirPrefix . $baseName . ".php";
        $code = "/* Auto-generated from php/php-langspec tests */" . $codes[0][0];
        file_put_contents($fileToWrite, $code);
        if (!file_exists($fileToWrite . ".tree")) {
            file_put_contents($fileToWrite . ".tree", $code);
        }
        if (!file_exists($fileToWrite . ".tokens")) {
            file_put_contents($fileToWrite . ".tokens", $code);
        }
    }
}

function pascalCase($str) {
    $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    return $str;
}