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

		/**
		 * @param string $query
		 * @return FindResult[]
		 */
		public function findAllOccurrences(string $query): array{

			$findResults = [];
			$buffer = "";
			$characters = mb_str_split($this->currentString, 1, "UTF-8");
			$characterLengthOfQuery = mb_strlen($query);

			foreach($characters as $index=>$char){
				$buffer .= $char;
				if (str_contains($buffer, $query)){
					$sub = mb_substr($buffer, -$characterLengthOfQuery);
					$result = new FindResult();
					$result->match = $sub;
					$result->endCharacterPositionOfMatch = $index;
					$findResults[] = $result;
					$buffer = "";
				}
			}

			return $findResults;
		}

		/**
		 * Fetches a substring with padding of text before and after the start/end substring.
		 * @param FindResult $findResult
		 * @param int $paddingSize The number of full-length characters to get before and after the stub. This is not a byte size, but a number of characters.
		 */
		public function getSubStringWithPadding(FindResult $findResult, int $paddingSize = 35): StubResult{
			$currentStringCharacters = mb_str_split($this->currentString, 1, $this->encoding);
			$characterCountOfFindMatch = mb_strlen($findResult->match);
			$endArrayPositionOfMatch = $findResult->endCharacterPositionOfMatch;
			$startArrayPositionOfMatch = $endArrayPositionOfMatch - $characterCountOfFindMatch;
			$startOfStub = $startArrayPositionOfMatch - $paddingSize;
			$endOfStub = $endArrayPositionOfMatch + $paddingSize;

			$beforeStub = "";
			$endStub = "";

			if ($startOfStub < 0){

				if ($startArrayPositionOfMatch > 0){
					$startOfStub = 0;
				}
			}

			if ($endOfStub > count($currentStringCharacters)){

				if ($endArrayPositionOfMatch < count($currentStringCharacters)){
					$endOfStub = count($currentStringCharacters) - 1;
				}

			}

			for ($i = $startOfStub; $i <= $startArrayPositionOfMatch; $i++){
				$beforeStub .= $currentStringCharacters[$i];
			}

			for ($i = ($endArrayPositionOfMatch + 1); $i <= $endOfStub; $i++){
				$endStub .= $currentStringCharacters[$i];
			}

			$stubResult = new StubResult();
			$stubResult->beforeStub = $beforeStub;
			$stubResult->stub = $findResult->match;
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