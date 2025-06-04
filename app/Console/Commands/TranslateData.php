<?php

	namespace App\Console\Commands;

	use Illuminate\Console\Command;
	use App\Models\Author;
	use App\Models\Book;
	use App\Models\Series;
	use App\Models\Subject;
	use App\Services\OpenAiService;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\File; // Added for file operations

	class TranslateData extends Command
	{
		protected $signature = 'translate:data {--model=} {--limit=0} {--force-all}';
		protected $description = 'Translates existing English data to Turkish using OpenAI, skipping already translated fields.';
		protected OpenAiService $openAiService;

		public function __construct(OpenAiService $openAiService)
		{
			parent::__construct();
			$this->openAiService = $openAiService;
		}

		public function handle()
		{
			if (empty(config('admin_settings.openai_api_key'))) {
				$this->error('OpenAI API Key is not configured. Please set it in admin_settings.');
				return Command::FAILURE;
			}
			$this->info('Starting data translation process...');
			$modelOption = $this->option('model');
			$limit = (int) $this->option('limit');
			$forceAll = $this->option('force-all');

			if ($modelOption) {
				$modelOption = strtolower($modelOption);
				if ($modelOption === 'author' || $modelOption === 'authors') {
					$this->translateAuthors($limit, $forceAll);
				} elseif ($modelOption === 'book' || $modelOption === 'books') {
					$this->translateBooks($limit, $forceAll);
				} elseif ($modelOption === 'series') {
					$this->translateSeries($limit, $forceAll);
				} elseif ($modelOption === 'subject' || $modelOption === 'subjects') {
					$this->translateSubjects($limit, $forceAll);
				} else {
					$this->error("Invalid model specified. Available options: Author, Book, Series, Subject.");
				}
			} else {
				$this->translateAuthors($limit, $forceAll);
				$this->translateBooks($limit, $forceAll);
				$this->translateSeries($limit, $forceAll);
				$this->translateSubjects($limit, $forceAll);
			}
			$this->info('Data translation process completed!');
			return Command::SUCCESS;
		}

		private function getRandomTranslationExamples(): string
		{
			$filePath = base_path('resources/translation-blocks/martin-eden.json');
			$examplesString = "";

			if (!File::exists($filePath)) {
				Log::warning("Translation examples file not found: {$filePath}");
				return "No general style examples available.";
			}

			$jsonContent = File::get($filePath);
			$examples = json_decode($jsonContent, true);

			if (json_last_error() !== JSON_ERROR_NONE || !is_array($examples) || empty($examples)) {
				Log::warning("Invalid or empty JSON in translation examples file: {$filePath}. Error: " . json_last_error_msg());
				return "General style examples are incorrectly formatted or empty.";
			}

			shuffle($examples);
			$selectedExamples = array_slice($examples, 0, 2);

			if (empty($selectedExamples)) {
				return "No general style examples found in the file.";
			}

			$count = 2;
			foreach ($selectedExamples as $example) {
				if (isset($example['English']) && isset($example['Turkish'])) {
					$examplesString .= "Style Example {$count}:\nEnglish: \"{$example['English']}\"\nTurkish: \"{$example['Turkish']}\"\n\n";
					$count++;
				}
			}

			return rtrim($examplesString);
		}

		private function translateText(string $textToTranslate, string $specificContext = "general text"): ?string
		{
			if (empty(trim($textToTranslate))) {
				return null;
			}

			$generalStyleExamples = $this->getRandomTranslationExamples();

			$prompt = "You are a professional translator. Your task is to translate English text to Turkish.
It is more important that the reader understands the meaning and can follow the story/context than a literal, word-for-word translation.
Aim for natural, fluent Turkish.

Here are some general style examples to guide the tone and quality of translation:
{$generalStyleExamples}

Context for the current text to be translated: {$specificContext}.

Now, please provide the Turkish translation for the following English text. Output ONLY the Turkish translation.
English text: \"{$textToTranslate}\"
Turkish translation:";

			$messages = [
				['role' => 'user', 'content' => $prompt]
			];

			// Using a lower temperature for more deterministic translations
			$response = $this->openAiService->generateText($messages, 0.3, 4000);

			if (isset($response['error'])) {
				$this->error("OpenAI API Error for '{$specificContext}': " . $response['error']);
				Log::error("OpenAI Translation Error for '{$specificContext}': " . $response['error'], ['text' => $textToTranslate]);
				return null;
			}
			return trim($response['content'] ?? '', '"'); // Trim quotes OpenAI might add
		}

		private function translateAuthors(int $limit, bool $forceAll)
		{
			$this->line('Translating authors...');
			$query = Author::query();
			if (!$forceAll) {
				$query->where(function ($q) {
					$q->where(function ($sq) {
						$sq->whereNotNull('name')->where(function ($ssq) {
							$ssq->whereNull('name_tr')->orWhere('name_tr', '');
						});
					})->orWhere(function ($sq) {
						$sq->whereNotNull('biography')->where(function ($ssq) {
							$ssq->whereNull('biography_tr')->orWhere('biography_tr', '');
						});
					});
				});
			}

			if ($limit > 0) {
				$query->limit($limit);
			}
			$authors = $query->get();

			if ($authors->isEmpty()) {
				$this->info("No authors require translation or limit reached.");
				return;
			}

			$progressBar = $this->output->createProgressBar($authors->count());
			$progressBar->start();

			foreach ($authors as $author) {
				$updated = false;
				if (!empty($author->name) && (empty($author->name_tr) || $forceAll)) {
					$translatedName = $this->translateText($author->name, "Author's full name: {$author->name}");
					if ($translatedName) {
						$author->name_tr = $translatedName;
						$updated = true;
					}
				}

				if (!empty($author->biography) && (empty($author->biography_tr) || $forceAll)) {
					$translatedBio = $this->translateText($author->biography, "Author's biography for {$author->name}");
					if ($translatedBio) {
						$author->biography_tr = $translatedBio;
						$updated = true;
					}
				}

				if ($updated) {
					$author->save();
				}
				$progressBar->advance();
				sleep(1); // To avoid hitting API rate limits too quickly
			}
			$progressBar->finish();
			$this->info("\nAuthors translation finished.");
		}

		private function translateBooks(int $limit, bool $forceAll)
		{
			$this->line('Translating books...');
			$query = Book::query();
			if (!$forceAll) {
				$query->where(function ($q) {
					$q->where(function ($sq) { // Name
						$sq->whereNotNull('name')->where(function ($ssq) {
							$ssq->whereNull('name_tr')->orWhere('name_tr', '');
						});
					})->orWhere(function ($sq) { // Subtitle
						$sq->whereNotNull('subtitle')->where(function ($ssq) {
							$ssq->whereNull('subtitle_tr')->orWhere('subtitle_tr', '');
						});
					})->orWhere(function ($sq) { // Description
						$sq->whereNotNull('description')->where(function ($ssq) {
							$ssq->whereNull('description_tr')->orWhere('description_tr', '');
						});
					});
				});
			}

			if ($limit > 0) {
				$query->limit($limit);
			}
			$books = $query->get();

			if ($books->isEmpty()) {
				$this->info("No books require translation or limit reached.");
				return;
			}

			$progressBar = $this->output->createProgressBar($books->count());
			$progressBar->start();

			$generalStyleExamples = $this->getRandomTranslationExamples(); // Get once for all books in this run

			foreach ($books as $book) {
				$fieldsToTranslate = [];
				if (!empty($book->name) && (empty($book->name_tr) || $forceAll)) {
					$fieldsToTranslate['name'] = $book->name;
				}
				if (!empty($book->subtitle) && (empty($book->subtitle_tr) || $forceAll)) {
					$fieldsToTranslate['subtitle'] = $book->subtitle;
				}
				if (!empty($book->description) && (empty($book->description_tr) || $forceAll)) {
					$fieldsToTranslate['description'] = $book->description;
				}


				if (empty($fieldsToTranslate)) {
					$progressBar->advance();
					sleep(1); // Still sleep to be consistent with rate limits if many are skipped
					continue;
				}

				$jsonInputPayload = json_encode($fieldsToTranslate);

				// Provide context about the book if available fields are already translated.
				$bookContext = "This is for the book titled '{$book->name}'.";
				if (!empty($book->name_tr)) {
					$bookContext .= " The known Turkish title is '{$book->name_tr}'.";
				}
				if (!empty($book->subtitle) && !empty($book->subtitle_tr)){
					$bookContext .= " The known Turkish subtitle is '{$book->subtitle_tr}'.";
				}


				$prompt = "You will receive a JSON object containing specific fields of a book that need translation from English to Turkish. The fields could be 'name' (book title), 'subtitle', and/or 'description'.
Do a free translation to turkish of the norwegian text. It's more important the reader will understand and follow the story than a literal translation.
Use the examples of translation pairs bellow as a guide for the language i want.

Examples:
{$generalStyleExamples}

Remember: it should be a free translation to turkish of the norwegian text. It's more important the reader will understand and follow the story than a literal translation.
It is crucial that you return ONLY a valid JSON object. This JSON object should contain the SAME KEYS as the input, but with their values translated to Turkish.
For example, if input is {\"name\": \"The Great Gatsby\", \"description\": \"A story about...\"}, output should be {\"name\": \"Muhteşem Gatsby\", \"description\": \"Bir hikaye...\"}.
If an input field's value is empty or null (though typically we send non-empty fields), its translated counterpart should reflect that (e.g., empty string or null).

Specific context: {$bookContext}

Input JSON to translate:
{$jsonInputPayload}

Turkish JSON translation (ensure this is ONLY the JSON object):";

				$messages = [['role' => 'user', 'content' => $prompt]];
				$response = $this->openAiService->generateText($messages, 0.3, 4000); // Max tokens might need adjustment based on typical description length

				$updated = false;
				if (isset($response['error'])) {
					$this->error("OpenAI API Error for book ID {$book->id}: " . $response['error']);
					Log::error("OpenAI Book Translation Error for book ID {$book->id}", ['error' => $response['error'], 'input_payload' => $jsonInputPayload]);
				} elseif (isset($response['content'])) {
					$translatedJsonString = $response['content'];
					// Sometimes AI wraps JSON in ```json ... ```, try to strip it.
					if (strpos(trim($translatedJsonString), '```json') === 0) {
						$translatedJsonString = preg_replace('/^```json\s*([\s\S]*?)\s*```$/', '$1', $translatedJsonString);
					}

					$translatedData = json_decode(trim($translatedJsonString), true);

					if (json_last_error() === JSON_ERROR_NONE && is_array($translatedData)) {
						if (isset($translatedData['name']) && array_key_exists('name', $fieldsToTranslate)) {
							$book->name_tr = $translatedData['name'];
							$updated = true;
						}
						if (isset($translatedData['subtitle']) && array_key_exists('subtitle', $fieldsToTranslate)) {
							$book->subtitle_tr = $translatedData['subtitle'];
							$updated = true;
						}
						if (isset($translatedData['description']) && array_key_exists('description', $fieldsToTranslate)) {
							$book->description_tr = $translatedData['description'];
							$updated = true;
						}
						if (!$updated && !empty($fieldsToTranslate)) {
							Log::warning("Book Translation: OpenAI returned valid JSON, but keys did not match fields to translate or were empty.", [
								'book_id' => $book->id,
								'fields_to_translate' => $fieldsToTranslate,
								'received_json_keys' => array_keys($translatedData),
								'raw_response' => $response['content']
							]);
						}
					} else {
						$this->error("Failed to decode JSON response for book ID {$book->id}. Error: " . json_last_error_msg());
						Log::error("OpenAI Book Translation - Invalid JSON Response for book ID {$book->id}", [
							'json_error' => json_last_error_msg(),
							'response_content' => $response['content'],
							'input_payload' => $jsonInputPayload
						]);
					}
				}

				if ($updated) {
					$book->save();
				}

				$progressBar->advance();
				sleep(1); // API rate limit
			}

			$progressBar->finish();
			$this->info("\nBooks translation finished.");
		}

		private function translateSeries(int $limit, bool $forceAll)
		{
			$this->line('Translating series...');
			$query = Series::query();
			if (!$forceAll) {
				$query->whereNotNull('name')->where(function ($q) {
					$q->whereNull('name_tr')->orWhere('name_tr', '');
				});
			}
			if ($limit > 0) {
				$query->limit($limit);
			}
			$seriesItems = $query->get();

			if ($seriesItems->isEmpty()) {
				$this->info("No series require translation or limit reached.");
				return;
			}

			$progressBar = $this->output->createProgressBar($seriesItems->count());
			$progressBar->start();

			foreach ($seriesItems as $series) {
				if (!empty($series->name) && (empty($series->name_tr) || $forceAll)) {
					$translatedName = $this->translateText($series->name, "Book series name: {$series->name}");
					if ($translatedName) {
						$series->name_tr = $translatedName;
						$series->save();
					}
				}
				$progressBar->advance();
				sleep(1); // API rate limit
			}
			$progressBar->finish();
			$this->info("\nSeries translation finished.");
		}

		private function translateSubjects(int $limit, bool $forceAll)
		{
			$this->line('Translating subjects...');
			$query = Subject::query();
			if (!$forceAll) {
				$query->whereNotNull('name')->where(function ($q) {
					$q->whereNull('name_tr')->orWhere('name_tr', '');
				});
			}
			if ($limit > 0) {
				$query->limit($limit);
			}
			$subjectItems = $query->get();

			if ($subjectItems->isEmpty()) {
				$this->info("No subjects require translation or limit reached.");
				return;
			}

			$progressBar = $this->output->createProgressBar($subjectItems->count());
			$progressBar->start();

			foreach ($subjectItems as $subject) {
				if (!empty($subject->name) && (empty($subject->name_tr) || $forceAll)) {
					$translatedName = $this->translateText($subject->name, "Subject category or genre: {$subject->name}");
					if ($translatedName) {
						$subject->name_tr = $translatedName;
						$subject->save();
					}
				}
				$progressBar->advance();
				sleep(1); // API rate limit
			}
			$progressBar->finish();
			$this->info("\nSubjects translation finished.");
		}
	}
