<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;

class Resources
{
    const FOURTEENTH_SAEIMA = 14;
    const BASE_URL = 'https://data.gov.lv';
    const RESOURCE_SAEIMAS_SEDES = '/dati/dataset/saeimas-sedes';
    const CLASS_RESOURCE_LIST = 'resource-list';
    const CLASS_HEADING = 'heading';
    const ATTRIBUTE_DATA_ID = 'data-id';
    const ATTRIBUTE_HREF = 'href';
    const ATTRIBUTE_TITLE = 'title';
    const FILE_EXTENSION = 'xml';
    const FILE_CATEGORY = 'vote';

    protected $resourceList = [];

    public function getResourceList(): array
    {
        $httpClient = new Client();

        $response = $httpClient->get(self::BASE_URL . self::RESOURCE_SAEIMAS_SEDES);
        $html = (string)$response->getBody();

        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $xpath = new DOMXPath($doc);
        $queryResourceList = '//ul[@class = "' . self::CLASS_RESOURCE_LIST . '"]/li[position() < last()]';
        $resourceNodes = $xpath->query($queryResourceList);

        foreach ($resourceNodes as $resourceNode) {
            $queryHeading = 'a[@class="' . self::CLASS_HEADING . '"]';
            $headingNode = $xpath->query($queryHeading, $resourceNode)->item(0);

            $title = $headingNode->getAttribute(self::ATTRIBUTE_TITLE);

            if (!str_contains($title, self::FILE_CATEGORY) || strtok($title, '.') < self::FOURTEENTH_SAEIMA) {
                continue;
            }

            $fileName = $this->getFileName($title);
            $dataId = $resourceNode->getAttribute(self::ATTRIBUTE_DATA_ID);
            $href = $headingNode->getAttribute(self::ATTRIBUTE_HREF);

            $this->resourceList[$dataId] = self::BASE_URL . $href . '/download/' . $fileName;
        }

        return $this->resourceList;
    }

    private function getFileName(string $title): string
    {
        $fileName = preg_replace('#[ -]+#', '-', $title);

        if (substr($fileName, -3) === strtoupper(self::FILE_EXTENSION)) {
            $fileName = substr($fileName, 0, -3) . '.' . self::FILE_EXTENSION;
        } else {
            $fileName = $fileName . '.' . self::FILE_EXTENSION;
        }

        return $fileName;
    }
}
