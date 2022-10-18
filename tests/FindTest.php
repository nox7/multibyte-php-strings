<?php
	require_once __DIR__ . "/../vendor/autoload.php";

	use MultiByteString\MultiByteString;
	use PHPUnit\Framework\TestCase;

	class FindTest extends TestCase
	{
		public function testFindOccurrenceInMultiByteString(): void{
			$query = "Rapid Response Plumbing, Heating & Air";
			$testString = <<<HTML
			<title>3 Benefits Of High Pressure Water Jetting For Your Home’s Pipes | Glenwood Plumber</title>
			<meta name="description" property="og:description" content="If you’re one of the many Glenwood homeowners out there with a particularly stubborn drain clog on your hands, it might be time to give up on those store-bought drain cleaning solutions and DIY rooters. In this article, our team of skilled plumbers here at Rapid Response Plumbing, Heating & Air are going to highlight just a few of the benefits of high pressure water jetting, and how it could be the drain cleaning solution you’ve been looking for.">
			HTML;

			$multibyte = new MultiByteString($testString);
			$findResults = $multibyte->findAllOccurrences($query);
			$this->assertCount(
				expectedCount: 1,
				haystack:$findResults,
			);

			$endPositionOfResult = 415 + mb_strlen($query);

			$this->assertEquals(
				expected: $endPositionOfResult,
				actual:$findResults[0]->endCharacterPositionOfMatch,
			);
		}
	}