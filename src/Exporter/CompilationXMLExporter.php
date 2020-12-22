<?php

namespace JackCompiler\Exporter;

use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

class CompilationXMLExporter
{
    protected ComplexCompiledData $complexCompiledData;
    protected array $skipAddingTagFor = [];

    public function __construct(ComplexCompiledData $complexCompiledData)
    {
        $this->complexCompiledData = $complexCompiledData;
        $this->skipAddingTagFor = [
            CompilationType::SUBROUTINE_CALL()->getKey(),
        ];
    }

    public function export(): string
    {
        $xml = new \SimpleXMLElement('<class/>');

        $this->addData($this->complexCompiledData, $xml);

        // format xml
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string) $xml->asXML(), LIBXML_NOEMPTYTAG);

        $xml = '';

        foreach ($dom->childNodes as $childNode) {
            $xml .= $dom->saveXML($childNode);
        }

        return $xml;
    }

    public function exportToFile(string $filename): void
    {
        file_put_contents($filename, $this->export());
    }

    protected function addData(ComplexCompiledData $complexData, \SimpleXMLElement $xml): void
    {
        /**
         * @var ComplexCompiledData|CompiledData $data
         */
        foreach ($complexData as $data) {
            if ($data instanceof CompiledData) {
                $xml->addChild(
                    $data->getType()->getValue(),
                    ' ' . htmlspecialchars(str_replace('"', '', $data->getValue())) . ' '
                );

                continue;
            }

            if ($data instanceof ComplexCompiledData) {
                if ($data->isEmpty()) {
                    $xml->addChild($data->getType()->getValue(), PHP_EOL);
                    continue;
                }

                // if the type of the complex compiled data is of the type we want to skip
                // because nand2 tetris expects a different format
                if (in_array($data->getType()->getKey(), $this->skipAddingTagFor, true)) {
                    $this->addData($data, $xml);

                    continue;
                }

                $this->addData($data, $xml->addChild($data->getType()->getValue()));
            }
        }
    }
}
