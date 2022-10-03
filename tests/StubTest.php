<?php
	require_once __DIR__ . "/../vendor/autoload.php";

	use MultiByteString\MultiByteString;
	use MultiByteString\StubResult;
	use PHPUnit\Framework\TestCase;

	class StubTest extends TestCase
	{
		public function testGetStub(): void{
			$query = "Rapid Response Plumbing, Heating & Air";
			$expectedBeforeStub = "r team of skilled plumbers here at ";
			$expectedAfterStub = " are going to highlight just a few ";
			$queryStartInTestString = 416;
			$queryMultiByteLength = mb_strlen($query);
			$testString = <<<HTML
			<title>3 Benefits Of High Pressure Water Jetting For Your Home’s Pipes | Glenwood Plumber</title>
			<meta name="description" property="og:description" content="If you’re one of the many Glenwood homeowners out there with a particularly stubborn drain clog on your hands, it might be time to give up on those store-bought drain cleaning solutions and DIY rooters. In this article, our team of skilled plumbers here at Rapid Response Plumbing, Heating & Air are going to highlight just a few of the benefits of high pressure water jetting, and how it could be the drain cleaning solution you’ve been looking for.">
			HTML;

			$multibyte = new MultiByteString($testString);
			$stubResult = $multibyte->getSubStringWithPadding(
				start: $queryStartInTestString,
				stubMultiByteLength: $queryMultiByteLength,
			);

			$this->assertInstanceOf(
				expected: StubResult::class,
				actual:$stubResult,
			);

			$this->assertEquals(
				expected: $expectedBeforeStub,
				actual:$stubResult->beforeStub,
			);

			$this->assertEquals(
				expected: $query,
				actual:$stubResult->stub,
			);

			$this->assertEquals(
				expected: $expectedAfterStub,
				actual:$stubResult->afterStub,
			);
		}

		public function testGetStubWithMultipleOccurrences(): void{
			$query = "Rapid Response Plumbing, Heating & Air";
			$queryMultiByteLength = mb_strlen($query);
			$testString = <<<HTML
			<title>3 Benefits Of High Pressure Water Jetting For Your Home’s Pipes | Glenwood Plumber | Rapid Response Plumbing, Heating & Air  ’</title>
			<meta name="description" property="og:description" content="’If you’re one of the many Glenwood homeowners out there with a particularly stubborn drain clog on your hands, it might be time to give up on those store-bought drain cleaning solutions and DIY rooters. In this article, our’ team of skilled plumbers here at Rapid Response Plumbing, Heating & Air are going to highlight just a few of the benefits of high pressure water jetting, and how it could be the drain cleaning solution you’ve been looking for.">
			HTML;

			$multibyte = new MultiByteString($testString);
			$matches = $multibyte->findAllOccurrences($query);

			$stubResult = $multibyte->getSubStringWithPadding(
				start: $matches[0][1][1],
				stubMultiByteLength: $queryMultiByteLength,
			);

			$this->assertInstanceOf(
				expected: StubResult::class,
				actual:$stubResult,
			);

			$this->assertEquals(
				expected: $query,
				actual:$stubResult->stub,
			);
		}
	}