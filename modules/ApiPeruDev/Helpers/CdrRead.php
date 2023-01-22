<?php

namespace Modules\ApiPeruDev\Helpers;

class CdrRead
{
    public function getCrdContent($arcCdr)
    {
        $content = base64_decode($arcCdr);
        $filter = function ($filename) {
            return 'xml' === strtolower($this->getFileExtension($filename));
        };
        $files = (new Zip())->decompress($content, $filter);

        return 0 === count($files) ? '' : $files[0]['content'];
    }

    private function getFileExtension($filename)
    {
        $lastDotPos = strrpos($filename, '.');
        if (!$lastDotPos) {
            return '';
        }

        return substr($filename, $lastDotPos + 1);
    }

    public function getCdrData($xmlContent)
    {
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xmlContent);
            $xpath = new \DOMXPath($doc);
            $xpath->registerNamespace('x', $doc->documentElement->namespaceURI);

            $cdr_data = [];

            $resp = $xpath->query('/x:ApplicationResponse/cac:DocumentResponse/cac:Response');
            if ($resp->length === 1) {
                $obj = $resp[0];
                $cdr_data['code'] = $this->getValueByName($obj, 'ResponseCode');
                $cdr_data['message'] = $this->getValueByName($obj, 'Description');
            }

            $qr = $xpath->query('/x:ApplicationResponse/cac:DocumentResponse/cac:DocumentReference');
            if ($qr->length === 1) {
                $obj = $qr[0];
                $cdr_data['qr_url'] = $this->getValueByName($obj, 'DocumentDescription');
            }

            $nodes = $xpath->query('/x:ApplicationResponse/cbc:Note');
            $cdr_data['notes'] = [];
            if ($nodes->length > 0) {
                foreach ($nodes as $node) {
                    $cdr_data['notes'][] = $node->nodeValue;
                }
            }

            return $cdr_data;

        } catch (\Exception $e) {
            return null;
        }
    }

    private function getValueByName(\DOMElement $node, $name)
    {
        $values = $node->getElementsByTagName($name);
        if ($values->length !== 1) {
            return '';
        }

        return $values[0]->nodeValue;
    }
}
