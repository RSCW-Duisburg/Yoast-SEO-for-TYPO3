<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Page\PageInformation;

class AdvancedRobotsGenerator
{
    protected RecordService $recordService;

    public function __construct(RecordService $recordService = null)
    {
        if ($recordService === null) {
            $recordService = GeneralUtility::makeInstance(RecordService::class);
        }
        $this->recordService = $recordService;
    }

    public function generate(array $params): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if ($activeRecord instanceof Record && $activeRecord->shouldGenerateRobotsTag()) {
            $record = $activeRecord->getRecordData();
        } else {
            $record = $this->getRecordFromParams($params);
        }

        $noImageIndex = (bool)($record['tx_yoastseo_robots_noimageindex'] ?? false);
        $noArchive = (bool)($record['tx_yoastseo_robots_noarchive'] ?? false);
        $noSnippet = (bool)($record['tx_yoastseo_robots_nosnippet'] ?? false);
        $noIndex = (bool)($record['no_index'] ?? false);
        $noFollow = (bool)($record['no_follow'] ?? false);

        if ($noImageIndex || $noArchive || $noSnippet || $noIndex || $noFollow) {
            $metaTagManagerRegistry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
            $manager = $metaTagManagerRegistry->getManagerForProperty('robots');
            $manager->removeProperty('robots');

            $robots = [];
            if ($noImageIndex) {
                $robots[] = 'noimageindex';
            }
            if ($noArchive) {
                $robots[] = 'noarchive';
            }
            if ($noSnippet) {
                $robots[] = 'nosnippet';
            }
            $robots[] = $noIndex ? 'noindex' : 'index';
            $robots[] = $noFollow ? 'nofollow' : 'follow';

            $manager->addProperty('robots', implode(',', $robots));
        }
    }

    private function getRecordFromParams(array $params): array
    {
        /** @var ServerRequestInterface $request */
        $request = $params['request'] ?? null;
        if ($request instanceof ServerRequestInterface) {
            $pageInformation = $request->getAttribute('frontend.page.information');
            if ($pageInformation instanceof PageInformation) {
                return $pageInformation->getPageRecord();
            }
        }
        return [];
    }
}
