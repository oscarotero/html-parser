<?php
declare(strict_types = 1);

namespace HtmlParser\Tests;

use PHPUnit\Framework\TestCase;
use HtmlParser\Parser;
use DOMDocument;
use DOMElement;
use DOMXPath;
use DOMDocumentFragment;

class EncodingsTest extends TestCase
{
    public function encodingDataProvider(): array
    {
        return [
            [
                'tjournal.ru.html',
                'Еврокомиссия проверит Amazon на предмет нарушения антимонопольного законодательства — Новости на TJ',
                'Ведомство предполагает, что площадка собирает и использует данные от сторонних продавцов.',
            ],
            [
                'blockpost.com.html',
                '삼성페이에 암호화폐 결합될까?...삼성전자 블록체인TF, 서비스사업실로 이관 | 블록포스트 - 믿음직한 블록체인 정보 포털',
                '삼성전자가 최근 무선사업부 산하에 있던 블록체인TF를 서비스사업실로 이관한 것으로 전해졌다. 지난 3월 출시한 전략 스마트폰 갤럭시 S10에 ‘삼성 블록체인 키스토어’와 ‘삼성 블록체인 월렛’ 탑재를 주도한 블록체인TF가 삼성페이 등 스마트폰용 핵심 서비스 개발‧운용을 맡는 하는 서비스사업실로 합쳐진 것이다. 블록체인‧암호화폐 업계 관계자 및 정보기술(IT',
            ],
            [
                'marketing.itmedia.co.jp.html',
                ' 「マーケティング4.0」とは結局どういうことなのか？ (1/2) - ITmedia マーケティング',
                '「マーケティング4.0」とは何か。本稿では、首都大学東京大学院准教授の水越康介氏とネスレ日本の津田匡保氏の講演から、その概念と実践について探る。【更新】 (1/2)',
            ],
            [
                'jeanjean.bandcamp.com.html',
                "Coquin L'éléphant | Jean Jean",
                "Coquin L'éléphant, by Jean Jean",
                'title',
            ],
            [
                'vk.com.html',
                "О нас | ВКонтакте",
                'ВКонтакте — крупнейшая социальная сеть в России и странах СНГ. Наша миссия — соединять людей, сервисы и компании, создавая простые и удобные инструменты коммуникации.',
                'description',
                'windows-1251',
            ],
            [
                'lib.ru.html',
                "Lib.Ru: Библиотека Максима Мошкова",
                '',
                '',
                'koi8-r',
            ],
        ];
    }

    /**
     * @dataProvider encodingDataProvider
     */
    public function testRussian(string $file, string $title, string $description = null, string $metaName = 'description', string $encoding = null)
    {
        $document = Parser::parse(\file_get_contents(__DIR__."/assets/{$file}"), $encoding);
        $titleElement = $document->getElementsByTagName('title')->item(0);
        
        $this->assertSame($title, $titleElement->textContent);
        
        if ($description) {
            $descriptionElement = self::xpathQuery($document, './/meta[@name="'.$metaName.'"]');
            $this->assertSame($description, $descriptionElement->getAttribute('content'));
        }
    }

    private static function xpathQuery(DOMDocument $document, $query): ?DOMElement
    {
        $xpath = new DOMXPath($document);
        $entries = $xpath->query($query);

        return $entries->length ? $entries->item(0) : null;
    }
}
