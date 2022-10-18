<?php
	require_once __DIR__ . "/../vendor/autoload.php";

	use MultiByteString\MultiByteString;
	use MultiByteString\StubResult;
	use PHPUnit\Framework\TestCase;

	class ReplaceTest extends TestCase
	{
		public function testReplaceTestOnLongString(): void{
			$query = "No need to google “pressure washer";
			$replacement = "_REPLACED_";
			$queryMultiByteLength = mb_strlen($query);
			$testString = <<<HTML
			As homeowners, we all want a beautiful, comfortable and safe home. One of the most effective ways to properly maintain our home is by hiring professional pressure washers to regularly clean your home’s exteriors and pathways.

			There are plenty of companies to choose from in the Charleston area, but they’re not all providing the same service and value. Here’s why Charleston Spray Wash is the best choice to perform top-notch pressure washing for your home:
			
			We Follow Best Practices for Exceptional Cleaning
			The exterior of your home is exposed to the elements every single day, and therefore at a higher risk of damage and discoloration. Pressure washing is your solution to maintaining the beauty and functionality of your home, but it still requires care to avoid unintended damages.
			
			Contrary to what many people believe, you can apply too much pressure to your home’s surfaces and cause damage. At Charleston Spray Wash, we want to extend the lifespan of your home by providing exceptional home maintenance services through deep cleaning.
			
			What we do here is that we only apply optimal water pressure on specific surfaces such as glass, concrete, or brick. Using hot water coupled with superior cleaning techniques and premium cleaning solutions is our formula for deep cleaning. And trust us — it’s a tried-and-tested formula that works.
			
			We Guarantee 100 Percent Satisfaction
			Our promise: We want to make sure that our clients are completely delighted with the deep cleaning services that we provide. At Charleston Spray Wash, our team never cut corners and will never leave your home until everything is clean. Rest assured, we have time-tested methods and state-of-the-art power-washing equipment to ensure that our customers are happy and your home is deeply cleaned.
			
			We’re Experienced Pressure Washers
			Our team is highly experienced and skilled in pressure washing as we have been doing this for years. No job is too small or big for us — our team can tackle any dirt or grime. We’re experts at what we do, and our team is knowledgeable about anything about pressure washing. You can always rely on us to make sure that you are satisfied and your home lasts longer.
			
			We Offer Comprehensive Services
			Think of Charleston Spray Wash as your one-stop shop when it comes to pressure washing. Our ultimate goal is to ensure that your home is well-kept by providing comprehensive pressure washing services. We do everything here at Charleston Spray Wash — from deep washing your home exteriors to cleaning your roof, windows, and concrete.
			
			Hire Charleston Spray Wash for Your Home Maintenance Needs
			No need to google “pressure washers” because you’ve got everything you need at Charleston Spray Wash. Our team will make sure that your home is properly maintained, from pressure washing your home and pathways to cleaning your gutters and roof.
			
			We understand how important home maintenance is — it enhances the value of your home and makes the residents comfortable and safe. That’s why we’re your ONLY team for the pressure washers team in Charleston. Contact us today for an appointment or to learn more about our services. We are more than happy to assist.
			HTML;

			$resultString = <<<HTML
			As homeowners, we all want a beautiful, comfortable and safe home. One of the most effective ways to properly maintain our home is by hiring professional pressure washers to regularly clean your home’s exteriors and pathways.

			There are plenty of companies to choose from in the Charleston area, but they’re not all providing the same service and value. Here’s why Charleston Spray Wash is the best choice to perform top-notch pressure washing for your home:
			
			We Follow Best Practices for Exceptional Cleaning
			The exterior of your home is exposed to the elements every single day, and therefore at a higher risk of damage and discoloration. Pressure washing is your solution to maintaining the beauty and functionality of your home, but it still requires care to avoid unintended damages.
			
			Contrary to what many people believe, you can apply too much pressure to your home’s surfaces and cause damage. At Charleston Spray Wash, we want to extend the lifespan of your home by providing exceptional home maintenance services through deep cleaning.
			
			What we do here is that we only apply optimal water pressure on specific surfaces such as glass, concrete, or brick. Using hot water coupled with superior cleaning techniques and premium cleaning solutions is our formula for deep cleaning. And trust us — it’s a tried-and-tested formula that works.
			
			We Guarantee 100 Percent Satisfaction
			Our promise: We want to make sure that our clients are completely delighted with the deep cleaning services that we provide. At Charleston Spray Wash, our team never cut corners and will never leave your home until everything is clean. Rest assured, we have time-tested methods and state-of-the-art power-washing equipment to ensure that our customers are happy and your home is deeply cleaned.
			
			We’re Experienced Pressure Washers
			Our team is highly experienced and skilled in pressure washing as we have been doing this for years. No job is too small or big for us — our team can tackle any dirt or grime. We’re experts at what we do, and our team is knowledgeable about anything about pressure washing. You can always rely on us to make sure that you are satisfied and your home lasts longer.
			
			We Offer Comprehensive Services
			Think of Charleston Spray Wash as your one-stop shop when it comes to pressure washing. Our ultimate goal is to ensure that your home is well-kept by providing comprehensive pressure washing services. We do everything here at Charleston Spray Wash — from deep washing your home exteriors to cleaning your roof, windows, and concrete.
			
			Hire Charleston Spray Wash for Your Home Maintenance Needs
			_REPLACED_s” because you’ve got everything you need at Charleston Spray Wash. Our team will make sure that your home is properly maintained, from pressure washing your home and pathways to cleaning your gutters and roof.
			
			We understand how important home maintenance is — it enhances the value of your home and makes the residents comfortable and safe. That’s why we’re your ONLY team for the pressure washers team in Charleston. Contact us today for an appointment or to learn more about our services. We are more than happy to assist.
			HTML;

			$multibyte = new MultiByteString($testString);
			$findResults = $multibyte->findAllOccurrences($query);

			$lengthOfMatch = mb_strlen($findResults[0]->match);
			$start = $findResults[0]->endCharacterPositionOfMatch - $lengthOfMatch + 1;

			$multibyte->replaceStub(
				start: $start,
				stubMultiByteLength:$lengthOfMatch,
				replacement:$replacement,
			);

			$this->assertEquals(
				expected: $resultString,
				actual:$multibyte->currentString,
			);

		}
	}