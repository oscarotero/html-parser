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
        'ASCII'        => 'ascii',
        'UTF-8'        => 'utf-8',
        'SJIS'         => 'shift_jis',
        'Windows-1251' => 'windows-1251',
        'Windows-1252' => 'windows-1252',
        'Windows-1254' => 'windows-1254',
        'ISO-8859-1'   => 'iso-8859-1',
        'ISO-8859-2'   => 'iso-8859-2',
        'ISO-8859-3'   => 'iso-8859-3',
        'ISO-8859-4'   => 'iso-8859-4',
        'ISO-8859-5'   => 'iso-8859-5',
        'ISO-8859-6'   => 'iso-8859-6',
        'ISO-8859-7'   => 'iso-8859-7',
        'ISO-8859-8'   => 'iso-8859-8',
        'ISO-8859-9'   => 'iso-8859-9',
        'ISO-8859-10'  => 'iso-8859-10',
        'ISO-8859-13'  => 'iso-8859-13',
        'ISO-8859-14'  => 'iso-8859-14',
        'ISO-8859-15'  => 'iso-8859-15',
        'ISO-8859-16'  => 'iso-8859-16',
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
        if (stripos($string, '<meta charset=') === false) {
            return $string;
        }

        $detected = mb_detect_encoding($string, implode(',', array_keys(self::ENCODINGS)), true);
        
        if ($detected && isset(self::ENCODINGS[$detected])) {
            $string = mb_convert_encoding($string, 'HTML-ENTITIES', $detected);
            $string = preg_replace(
                '/<head[^>]*>/',
                '<head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset='.self::ENCODINGS[$detected].'">',
                $string
            );
        }

        return $string;
    }
}
