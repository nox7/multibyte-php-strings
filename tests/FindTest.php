<?php
	require_once __DIR__ . "/../vendor/autoload.php";

	use MultiByteString\MultiByteString;
	use PHPUnit\Framework\TestCase;

	class FindTest extends TestCase
	{
		public function testFindOccurrenceInMultiByteString(): void{
			$testString = <<<HTML
			<title>3 Benefits Of High Pressure Water Jetting For Your Home’s Pipes | Glenwood Plumber</title>
			<meta name="description" property="og:description" content="If you’re one of the many Glenwood homeowners out there with a particularly stubborn drain clog on your hands, it might be time to give up on those store-bought drain cleaning solutions and DIY rooters. In this article, our team of skilled plumbers here at Rapid Response Plumbing, Heating & Air are going to highlight just a few of the benefits of high pressure water jetting, and how it could be the drain cleaning solution you’ve been looking for.">
			HTML;

			$multibyte = new MultiByteString($testString);
			$matches = $multibyte->findAllOccurrences("Rapid Response Plumbing, Heating & Air");
			$rawMatches = $matches[0];
			$this->assertCount(
				expectedCount: 1,
				haystack:$rawMatches,
			);

			$this->assertEquals(
				expected: 416,
				actual:$rawMatches[0][1],
			);
		}
	}