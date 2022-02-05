<?php

namespace App\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Crawler;

trait CartAssertionsTrait
{
    public static function assertCartItemsCountEquals(Crawler $crawler, $expectedCount): void
    {
        $actualCount = $crawler
            ->filter('.col-md-8 .list-group-item')
            ->count();

        Assert::assertEquals(
            $expectedCount,
            $actualCount,
            "The cart should contain \"$expectedCount\" item(s). Actual: \"$actualCount\" item(s)."
        );
    }

    public static function assertCartIsEmpty(Crawler $crawler)
    {
        $infoText = $crawler
            ->filter('.alert-info')
            ->getNode(0)
            ->textContent;

        $infoText = self::normalizeWhitespace($infoText);

        Assert::assertEquals(
            'Your cart is empty. Go to the product list.',
            $infoText,
            'The cart should be empty.'
        );
    }

    public static function assertCartTotalEquals(Crawler $crawler, $expectedTotal)
    {
        $actualTotal = (float) $crawler
            ->filter('.col-md-4 .list-group-item span')
            ->getNode(0)
            ->textContent;

        Assert::assertEquals(
            $expectedTotal,
            $actualTotal,
            "The cart total should be equal to \"$expectedTotal €\". Actual: \"$actualTotal €\"."
        );
    }

    public static function assertCartContainsProductWithQuantity(Crawler $crawler, string $productName, int $expectedQuantity): void
    {
        $actualQuantity = (int) self::getItemByProductName($crawler, $productName)
            ->filter('input[type="number"]')
            ->attr('value');

        Assert::assertEquals(
            $expectedQuantity,
            $actualQuantity,
            "The quantity should be equal to \"$expectedQuantity\". Actual: \"$actualQuantity\"."
        );
    }

    public static function assertCartNotContainsProduct(Crawler $crawler, string $productName): void
    {
        Assert::assertEmpty(
            self::getItemByProductName($crawler, $productName),
            "The cart should not contain the product \"$productName\"."
        );
    }

    private static function getItemByProductName(Crawler $crawler, string $productName)
    {
        $items = $crawler->filter('.col-md-8 .list-group-item')->reduce(
            function (Crawler $node) use ($productName) {
                if ($node->filter('h5')->getNode(0)->textContent === $productName) {
                    return $node;
                }

                return false;
            }
        );

        return empty($items) ? null : $items->eq(0);
    }

    private static function normalizeWhitespace(string $value): string
    {
        return trim(preg_replace('/(?:\s{2,}+|[^\S ])/', ' ', $value));
    }
}
