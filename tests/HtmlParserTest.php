<?php
declare(strict_types = 1);

namespace HtmlParser\Tests;

use PHPUnit\Framework\TestCase;
use HtmlParser\Parser;
use DOMDocument;
use DOMDocumentFragment;

class HtmlParserTest extends TestCase
{
    public function testHtmlFragment()
    {
        $html = '<img src="http://example.com/image.png?123456" alt="Image"><span>Hello world</span>';

        $fragment = Parser::parseFragment($html);

        $this->assertInstanceOf(DOMDocumentFragment::class, $fragment);
        $this->assertCount(2, $fragment->childNodes);
        $this->assertSame('img', $fragment->childNodes->item(0)->tagName);
        $this->assertSame($html, Parser::stringify($fragment));
    }

    public function testHtmlDocument()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html><body>
        <img src="http://example.com/image.png?123456" alt="Image">
</body></html>
HTML;

        $document = Parser::parse($html);

        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertCount(1, $document->getElementsByTagName('html'));
        $this->assertCount(1, $document->getElementsByTagName('body'));
        $this->assertCount(1, $document->getElementsByTagName('img'));

        $this->assertSame($html, trim(Parser::stringify($document)));
    }

    public function testHtmlDocumentFragment()
    {
        $html = '<img src="http://example.com/image.png?123456" alt="Image">';

        $htmlFinal = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><img src="http://example.com/image.png?123456" alt="Image"></body></html>
HTML;

        $document = Parser::parse($html);

        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertCount(1, $document->getElementsByTagName('html'));
        $this->assertCount(1, $document->getElementsByTagName('body'));
        $this->assertCount(1, $document->getElementsByTagName('img'));

        $this->assertSame($htmlFinal, trim(Parser::stringify($document)));
    }

    public function testOnlyText()
    {
        $html = 'hello world';
        $document = Parser::parse($html);

        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertCount(1, $document->getElementsByTagName('html'));
        $this->assertCount(1, $document->getElementsByTagName('body'));
    }
}
