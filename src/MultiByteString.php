<?php
	namespace MultiByteString;

	class MultiByteString{

		private string $encoding = "UTF-8";
		public string $currentString;

		public function __construct(
			public string $originalString,
		){
			$this->currentString = $this->originalString;
		}

		public function setEncoding(string $encoding): void{
			mb_internal_encoding($encoding);
			$this->encoding = $encoding;
		}

		public function getEncoding(): string{
			return $this->encoding;
		}

		public function findAllOccurrences(string $query): array{

			/**
			 * Steps
			 * 1) Use preg_match_all with "u" flag and find all occurrences of the query.
			 * 2) Iterate over each raw result from preg_match_all and adjust all start positions by
			 * the current index, plus one, multiplied by the byte difference of the query from strlen and mb_strlen.
			 * 3) Iterate over each raw result from preg_match_all and begin an accumulator of how many bytes are different
			 * between strlen and mb_strlen of all characters prior to the match and the previous match's end or the 0th
			 * string position if the first match.
			 */

			$byteLengthOfQuery = strlen($query);
			$multiByteLengthOfQuery = mb_strlen($query, $this->encoding);
			$byteDifferenceOfQuery = $byteLengthOfQuery - $multiByteLengthOfQuery;

			preg_match_all(
				pattern:sprintf("/%s/iu",
					preg_quote(str: $query, delimiter: "/") // Escape the query for preg match
				),
				subject: $this->currentString,
				matches:$matches,
				flags: PREG_OFFSET_CAPTURE,
			);

			$rawMatches = $matches[0];

			foreach($rawMatches as $index=>$match){
				// +1 on the index here guarantees that if the query has a byte difference,
				// then it is applied to all results
				$byteDifferenceOfQueryAtIndex = ( ($index + 1) * $byteDifferenceOfQuery);
				$rawMatches[$index][1] += $byteDifferenceOfQueryAtIndex;
			}

			$totalByteDifference = 0;
			$lastMatchEndPosition = 0;
			foreach($rawMatches as $index=>$match){
				// Get the previous string from this match
				$prevString = mb_substr(
					string: $this->currentString,
					start:$lastMatchEndPosition,
					length: ($match[1] - $lastMatchEndPosition),
					encoding: $this->encoding,
				);

				$prevStringByteDifference = strlen($prevString) - mb_strlen($prevString, $this->encoding);
				$totalByteDifference += $prevStringByteDifference;
				$rawMatches[$index][1] -= $totalByteDifference;
				$lastMatchEndPosition = $rawMatches[$index][1] + $multiByteLengthOfQuery;
			}

			$matches[0] = $rawMatches;

			return $matches;
		}

		/**
		 * Fetches a substring with padding of text before and after the start/end substring.
		 * @param int $start The multibyte start position of the stub
		 * @param int $stubMultiByteLength The multibyte length of the stub
		 * @param int $paddingSize The number of full-length characters to get before and after the stub. This is not a byte size, but a number of characters.
		 */
		public function getSubStringWithPadding(int $start, int $stubMultiByteLength, int $paddingSize = 35): StubResult{
			$multiByteLengthOfString = mb_strlen($this->currentString, $this->encoding);
			$beforeStub = "";
			$endStub = "";

			if ($start > 0){
				$beforeStubStartPosition = $start - $paddingSize;
				$beforePaddingSize = $paddingSize;

				// Clamp
				if ($beforeStubStartPosition < 0) {
					$beforeStubStartPosition = 0;
					$beforePaddingSize = $start;
				}

				// Get the string stub
				$beforeStub = mb_substr(
					string: $this->currentString,
					start: $beforeStubStartPosition,
					length: $beforePaddingSize,
					encoding: $this->encoding
				);
			}

			$end = $start + $stubMultiByteLength;

			if ($end < $multiByteLengthOfString){
				if ($end + $paddingSize < $multiByteLengthOfString) {
					$endStub = mb_substr(
						string:$this->currentString,
						start: $end,
						length: $paddingSize,
						encoding: $this->encoding
					);
				}else{
					$endStub = mb_substr(
						string:$this->currentString,
						start: $end,
						length: null,
						encoding: $this->encoding
					);
				}
			}

			$stubResult = new StubResult();
			$stubResult->beforeStub = $beforeStub;
			$stubResult->stub = mb_substr(
				string: $this->currentString,
				start: $start,
				length: $stubMultiByteLength,
				encoding: $this->encoding
			);
			$stubResult->afterStub = $endStub;

			return $stubResult;
		}

		/**
		 * Replaces a stub in the currentString property. Modifies the currentString property.
		 * @param int $start
		 * @param int $stubMultiByteLength
		 * @param string $replacement
		 * @return void
		 */
		public function replaceStub(int $start, int $stubMultiByteLength, string $replacement): void{
			$stringBeforeStub = mb_substr($this->currentString, 0, $start);
			$stringAfterStub = mb_substr($this->currentString, $start + $stubMultiByteLength);
			$this->currentString = sprintf("%s%s%s", $stringBeforeStub, $replacement, $stringAfterStub);
		}
	}