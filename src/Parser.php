<?php
declare(strict_types = 1);

namespace HtmlParser;

use Exception;
use DOMNode;
use DOMDocument;
use DOMDocumentFragment;
use SimpleXMLElement;

class Parser
{
    private const ENCODINGS = [
        'UTF-8' => 'utf-8',
        'SJIS' => 'shift_jis',
        'ISO-8859-1' => 'utf-8',
    ];

    public static function stringify(DOMNode $node): string
    {
        if ($node instanceof DOMDocument) {
            return $node->saveHTML($node);
        }

        return $node->ownerDocument->saveHTML($node);
    }

    public static function parse(string $html): DOMDocument
    {
        $errors = libxml_use_internal_errors(true);
        $entities = libxml_disable_entity_loader(true);

        $html = trim(self::normalize($html));

        $document = new DOMDocument();
        $document->loadHTML($html);

        libxml_use_internal_errors($errors);
        libxml_disable_entity_loader($entities);

        return $document;
    }

    public static function parseFragment(string $html): DOMDocumentFragment
    {
        $html = "<html><head></head><body>{$html}</body></html>";
        $document = static::parse($html);
        $fragment = $document->createDocumentFragment();

        $body = $document->getElementsByTagName('body')->item(0);

        $nodes = [];

        foreach ($body->childNodes as $node) {
            $nodes[] = $node;
        }

        foreach ($nodes as $node) {
            $fragment->appendChild($node);
        }

        return $fragment;
    }

    private static function normalize(string $string): string
    {
        foreach (self::ENCODINGS as $encoding => $contentType) {
            if (mb_detect_encoding($string, $encoding, true) === $encoding) {
                $string = mb_convert_encoding($string, 'HTML-ENTITIES', $encoding);

                return preg_replace('/<head[^>]*>/', "<head><META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset='{$contentType}'>", $string);
            }
        }

        return $string;
    }
}
