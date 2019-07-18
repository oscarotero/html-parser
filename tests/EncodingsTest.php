<?php
declare(strict_types = 1);

namespace HtmlParser\Tests;

use PHPUnit\Framework\TestCase;
use HtmlParser\Parser;
use DOMDocument;
use DOMDocumentFragment;

class EncodingsTest extends TestCase
{
    public function testRussian()
    {
        $document = Parser::parse(\file_get_contents(__DIR__.'/assets/tjournal.ru.html'));
        $title = $document->getElementsByTagName('title')->item(0);

        $this->assertSame(
            'Еврокомиссия проверит Amazon на предмет нарушения антимонопольного законодательства — Новости на TJ',
            $title->textContent
        );
    }    
}
