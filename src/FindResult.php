<?php
	namespace MultiByteString;

	class FindResult
	{
		/** @var string The string match itself */
		public string $match;
		/** @var int The ending position of the match based on character count and not byte count */
		public int $endCharacterPositionOfMatch;
	}