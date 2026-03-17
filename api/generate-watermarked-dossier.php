<?php
// ============================================================
// Propriete intellectuelle de Mr Milianni Samir
// Createur de la marque AsArt'sDev
// Toute reproduction, divulgation ou utilisation est interdite
// ============================================================

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$inputFile = $argv[1] ?? 'dossier_base.pdf';
$outputFile = $argv[2] ?? 'dossier_final.pdf';

if (!is_file($inputFile)) {
    fwrite(STDERR, "Fichier introuvable: {$inputFile}" . PHP_EOL);
    exit(1);
}

$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($inputFile);

for ($i = 1; $i <= $pageCount; $i++) {
    $tpl = $pdf->importPage($i);
    $size = $pdf->getTemplateSize($tpl);

    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
    $pdf->useTemplate($tpl);

    // Ajout du filigrane
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(200, 200, 200);
    $pdf->SetXY(30, 150);

    if (method_exists($pdf, 'StartTransform') && method_exists($pdf, 'Rotate')) {
        $pdf->StartTransform();
        $pdf->Rotate(45);
        $pdf->Write(0, "Propriete intellectuelle de Mr Milianni Samir - AsArt'sDev");
        $pdf->StopTransform();
    } else {
        // Fallback si la rotation n'est pas disponible dans l'implementation active.
        $pdf->Write(0, "Propriete intellectuelle de Mr Milianni Samir - AsArt'sDev");
    }
}

$pdf->Output('F', $outputFile);
echo "PDF genere: {$outputFile}" . PHP_EOL;
