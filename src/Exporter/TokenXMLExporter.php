<?php

namespace JackCompiler\Exporter;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenizedData;

class TokenXMLExporter
{
    protected TokenizedData $tokenizedData;

    public function __construct(TokenizedData $tokenizedData)
    {
        $this->tokenizedData = $tokenizedData;
    }

    public function export(string $filename): void
    {
        $xml = new \SimpleXMLElement('<tokens/>');

        while ($this->tokenizedData->hasMoreTokens()) {
            /**
             * @var Token $currentToken
             */
            $currentToken = $this->tokenizedData->currentToken();

            $xml->addChild($currentToken->getType()->getValue(), ' ' . $currentToken->getValue() . ' ');
            $this->tokenizedData->nextToken();
        }

        // format xml
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string) $xml->asXML());

        foreach ($dom->childNodes as $childNode) {
            $xml .= $dom->saveXML($childNode);
        }

        file_put_contents($filename, $xml);
    }
}
