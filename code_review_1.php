<?php

class Example
{
    protected function getCacheKeys()
    {
        $resultCacheKeys = array(
            'IBLOCK_ID',
            'ID',
            'IBLOCK_SECTION_ID',
            'NAME',
            'LIST_PAGE_URL',
            'CANONICAL_PAGE_URL',
            'SECTION',
            'IPROPERTY_VALUES',
            'TIMESTAMP_X',
            'BACKGROUND_IMAGE',
            'USE_CATALOG_BUTTONS'
        );

        $this->initAdditionalCacheKeys($resultCacheKeys);

        if (
            $this->arParams['SET_TITLE']
            || $this->arParams['ADD_ELEMENT_CHAIN']
            || $this->arParams['SET_BROWSER_TITLE'] === 'Y'
            || $this->arParams['SET_META_KEYWORDS'] === 'Y'
            || $this->arParams['SET_META_DESCRIPTION'] === 'Y'
        ) {
            $this->arResult['META_TAGS'] = array();
            $resultCacheKeys[] = 'META_TAGS';

            if ($this->arParams['SET_TITLE']) {
                $this->arResult['META_TAGS']['TITLE'] = $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                    ? $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                    : $this->arResult['NAME'];
            }

            if ($this->arParams['ADD_ELEMENT_CHAIN']) {
                $this->arResult['META_TAGS']['ELEMENT_CHAIN'] = $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                    ? $this->arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                    : $this->arResult['NAME'];
            }

            if ($this->arParams['SET_BROWSER_TITLE'] === 'Y') {
                $browserTitle = Collection::firstNotEmpty(
                    $this->arResult['PROPERTIES'], array($this->arParams['BROWSER_TITLE'], 'VALUE'),
                    $this->arResult, $this->arParams['BROWSER_TITLE'],
                    $this->arResult['IPROPERTY_VALUES'], 'ELEMENT_META_TITLE'
                );
                $this->arResult['META_TAGS']['BROWSER_TITLE'] = is_array($browserTitle)
                    ? implode(' ', $browserTitle)
                    : $browserTitle;
                unset($browserTitle);
            }

            if ($this->arParams['SET_META_KEYWORDS'] === 'Y') {
                $metaKeywords = Collection::firstNotEmpty(
                    $this->arResult['PROPERTIES'], array($this->arParams['META_KEYWORDS'], 'VALUE'),
                    $this->arResult['IPROPERTY_VALUES'], 'ELEMENT_META_KEYWORDS'
                );
                $this->arResult['META_TAGS']['KEYWORDS'] = is_array($metaKeywords)
                    ? implode(' ', $metaKeywords)
                    : $metaKeywords;
                unset($metaKeywords);
            }

            if ($this->arParams['SET_META_DESCRIPTION'] === 'Y') {
                $metaDescription = Collection::firstNotEmpty(
                    $this->arResult['PROPERTIES'], array($this->arParams['META_DESCRIPTION'], 'VALUE'),
                    $this->arResult['IPROPERTY_VALUES'], 'ELEMENT_META_DESCRIPTION'
                );
                $this->arResult['META_TAGS']['DESCRIPTION'] = is_array($metaDescription)
                    ? implode(' ', $metaDescription)
                    : $metaDescription;
                unset($metaDescription);
            }
        }

        return $resultCacheKeys;
    }
}