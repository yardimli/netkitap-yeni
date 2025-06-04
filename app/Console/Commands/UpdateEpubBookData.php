<?php

	namespace App\Console\Commands;

	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Str;
	use App\Models\Author;
	use App\Models\Book;
	use App\Models\Series;
	use App\Models\Subject;
	use League\Csv\Reader;
	use League\Csv\Statement;

	class UpdateEpubBookData extends Command
	{
		protected $signature = 'import:update-epub-books {--file=import/updated-epubbooks.csv : The path to the CSV file relative to storage/app/public}';
		protected $description = 'Updates book data from the new CSV format, including subtitles, series numbers, and re-syncs subjects.';

		const BASE_IMAGE_URL = 'https://www.epubbooks.com';
		// const AUTHOR_PHOTO_PATH = 'public/author-photos'; // Not used directly for author images in this command
		const BOOK_COVER_PATH = 'public/book-covers';

		public function handle()
		{
			$this->info('Starting book data update process...');

			// Ensure storage directory for book covers exists
			Storage::makeDirectory(self::BOOK_COVER_PATH);

			DB::transaction(function () {
				$this->importOrUpdateBooks();
			});

			$this->info('Book data update process completed successfully!');
			return Command::SUCCESS;
		}

		private function importOrUpdateBooks()
		{
			$this->line('Importing and updating books...');
			$relativePath = $this->option('file');
			$filePath = storage_path('app/public/' . $relativePath);

			if (!file_exists($filePath)) {
				$this->error("Book CSV file not found at: {$filePath}");
				return;
			}

			$csv = Reader::createFromPath($filePath, 'r');
			$csv->setHeaderOffset(0); // Assumes the first row is the header

			try {
				$records = Statement::create()->process($csv);
				$recordCount = iterator_count($records); // Count once
				// Re-process to get iterable records again, as iterator_count consumes it
				$records = Statement::create()->process($csv);
			} catch (\Exception $e) {
				$this->error("Error processing CSV: " . $e->getMessage());
				$this->error("Please ensure the CSV file '{$filePath}' has a valid header row and data.");
				return;
			}


			if ($recordCount === 0) {
				$this->warn("No records found in the CSV file: {$filePath}");
				return;
			}

			$progressBar = $this->output->createProgressBar($recordCount);
			$progressBar->start();

			foreach ($records as $record) {
				$bookName = trim($record['book_name'] ?? '');
				if (empty($bookName)) {
					$this->warn("Skipping book record due to empty name: " . json_encode($record));
					$progressBar->advance();
					continue;
				}

				// Author
				$authorName = trim(str_replace('by', '', $record['author'] ?? ''));
				$author = null;
				if (!empty($authorName)) {
					// Using updateOrCreate for author to potentially fill more details if this command were extended
					// For now, firstOrCreate is sufficient if only name is sourced from this CSV.
					$author = Author::firstOrCreate(['name' => $authorName]);
				}

				// Series
				$seriesNameInput = trim(str_replace('series:', '', $record['series'] ?? ''));
				$seriesId = null;
				$seriesNumber = null;

				if (!empty($seriesNameInput) && !in_array(strtolower($seriesNameInput), ['none', 'n/a', ''])) {
					$seriesNameToProcess = $seriesNameInput;

					// Extract series number like (#3)
					if (preg_match('/\s*\(\#(\d+)\)/', $seriesNameToProcess, $matches)) {
						$seriesNumber = (int)$matches[1];
						// Remove the number part from the series name
						$seriesNameToProcess = trim(preg_replace('/\s*\(\#(\d+)\)/', '', $seriesNameToProcess));
					}

					if (!empty($seriesNameToProcess)) {
						$series = Series::firstOrCreate(['name' => $seriesNameToProcess]);
						$seriesId = $series->id;
					} else {
						// If series name became empty after removing number (e.g. input was just "(#5)"),
						// then there's no valid series, so seriesNumber should also be null.
						$seriesNumber = null;
					}
				}

				// Cover Image
				$coverImagePath = null;
				if (!empty($record['cover_image-src'])) {
					$imageUrl = self::BASE_IMAGE_URL . $record['cover_image-src'];
					// Generate a unique filename part from the image URL to avoid collisions if bookName slug is identical
					$coverImagePath = $this->downloadImage($imageUrl, self::BOOK_COVER_PATH, Str::slug($bookName) . '-' . basename($record['cover_image-src']));
				}

				// Subtitle
				$subtitle = trim($record['subtitle'] ?? '');

				$bookDataForUpdate = [
					'series_id' => $seriesId,
					'series_number' => $seriesNumber,
					'subtitle' => !empty($subtitle) ? $subtitle : null, // Store null if empty
					'description' => trim($record['description'] ?? ''),
					'excerpt' => trim($record['excerpt'] ?? ''),
				];
				// Only include cover_image_path if it was successfully downloaded or already exists and valid
				if ($coverImagePath) {
					$bookDataForUpdate['cover_image_path'] = $coverImagePath;
				}


				// Using name and author_id to uniquely identify a book for updateOrCreate
				// The second array contains values that will be updated if the book exists,
				// or used for creation along with the first array if it doesn't.
				// Fields not in the second array (like name_tr, description_tr, subtitle_tr)
				// will retain their existing values on update.
				$book = Book::updateOrCreate(
					['name' => $bookName, 'author_id' => $author?->id], // Attributes to find the record
					$bookDataForUpdate // Values to update or create with
				);

				// Subjects: Clear existing subjects for this book and rebuild
				$subjectsString = trim(str_replace('subjects:', '', $record['subjects'] ?? ''));
				$subjectIds = [];
				if (!empty($subjectsString)) {
					$subjectNames = array_map('trim', explode(',', $subjectsString));
					foreach ($subjectNames as $sName) {
						if (!empty($sName)) {
							$subject = Subject::firstOrCreate(['name' => $sName]);
							$subjectIds[] = $subject->id;
						}
					}
				}
				// sync() will detach any subjects not in $subjectIds and attach/update new ones.
				// If $subjectIds is empty, it will detach all subjects from the book.
				$book->subjects()->sync($subjectIds);

				$progressBar->advance();
			}

			$progressBar->finish();
			$this->info("\nBooks import and update finished.");
		}

		private function downloadImage(string $url, string $directory, string $filename): ?string
		{
			if (empty($filename)) {
				$this->warn("Generated empty filename for URL {$url}. Skipping download.");
				return null;
			}

			//check if file already exists
			if (Storage::exists($directory . '/' . $filename)) {
				//$this->info("File already exists: {$directory}/{$filename}. Skipping download.");
				return Str::replaceFirst('public/', '', $directory) . '/' . $filename;
			}

			try {
				$response = Http::timeout(30)->get($url);
				if ($response->successful()) {
					$fullPath = $directory . '/' . $filename;
					Storage::put($fullPath, $response->body());
					return Str::replaceFirst('public/', '', $directory) . '/' . $filename;
				} else {
					$this->warn("Failed to download image from {$url}. Status: " . $response->status());
					return null;
				}
			} catch (\Exception $e) {
				$this->error("Exception downloading image from {$url}: " . $e->getMessage());
				return null;
			}
		}
	}
