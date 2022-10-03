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
			$this->encoding = $encoding;
		}

		public function getEncoding(): string{
			return $this->encoding;
		}

		public function findAllOccurrences(string $query): array{

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
			$lastMatchEndPosition = 0;
			$nextStartPositionAdjustmentCarryOver = 0;

			// Iterate over each match and push the match backwards by the multibyte / byte difference
			foreach($rawMatches as $index=>$rawMatch){
				$startPosition = $rawMatch[1];

				if ($nextStartPositionAdjustmentCarryOver > 0){
					$startPosition -= $nextStartPositionAdjustmentCarryOver;
					$nextStartPositionAdjustmentCarryOver = 0;
				}

				// Push this backwards by the byte difference of the query
				$startPosition -= $byteDifferenceOfQuery;

				// Get the last match end position to this start position as a stub
				$thisStub = mb_substr($this->currentString, $lastMatchEndPosition, $startPosition, $this->encoding);

				// Get the length difference
				$stubByteLength = strlen($thisStub);
				$stubMultiByteLength = mb_strlen($thisStub, $this->encoding);
				$stubByteDifference = $stubByteLength - $stubMultiByteLength;

				if ($stubByteDifference){
					$nextStartPositionAdjustmentCarryOver = $stubByteDifference;
				}

				// Push the start position back by this byte difference
				$startPosition -= $stubByteDifference;

				// Set the last end position as the start position + multibyte length of the query
				$lastMatchEndPosition = $startPosition + $multiByteLengthOfQuery;

				// Adjust the start position of this match result to be $startPosition
				$rawMatches[$index][1] = $startPosition;

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
				$beforeStub = mb_substr($this->currentString, $beforeStubStartPosition, $beforePaddingSize, $this->encoding);
			}

			$end = $start + $stubMultiByteLength;

			if ($end < $multiByteLengthOfString){
				if ($end + $paddingSize < $multiByteLengthOfString) {
					$endStub = mb_substr($this->currentString, $end, $paddingSize, $this->encoding);
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
			$stubResult->stub = mb_substr($this->currentString, $start, $stubMultiByteLength, $this->encoding);
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