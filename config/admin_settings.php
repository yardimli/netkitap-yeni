<?php
	return [
		'openai_api_key' => env('OPENAI_API_KEY'),
		'openai_vision_model' => env('OPENAI_VISION_MODEL', 'gpt-4-vision-preview'),
		'openai_text_model' => env('OPENAI_TEXT_MODEL', 'o4-mini'),
		// ... other settings
	];
