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

	class ImportEpubData extends Command
	{
		protected $signature = 'import:epub-data';
		protected $description = 'Imports epubbooks.csv and epubbooks-authors.csv data, downloads images, and populates database.';

		const BASE_IMAGE_URL = 'https://www.epubbooks.com';
		const AUTHOR_PHOTO_PATH = 'public/author-photos';
		const BOOK_COVER_PATH = 'public/book-covers';

		public function handle()
		{
			$this->info('Starting data import process...');

			// Ensure storage directories exist
			Storage::makeDirectory(self::AUTHOR_PHOTO_PATH);
			Storage::makeDirectory(self::BOOK_COVER_PATH);

			DB::transaction(function () {
				$this->importAuthors();
				$this->importBooks();
			});

			$this->info('Data import process completed successfully!');
			return Command::SUCCESS;
		}

		private function importAuthors()
		{
			$this->line('Importing authors...');
			$filePath = storage_path('app/public/import/epubbooks-authors.csv');

			if (!file_exists($filePath)) {
				$this->error("Author CSV file not found at: {$filePath}");
				return;
			}

			$csv = Reader::createFromPath($filePath, 'r');
			$csv->setHeaderOffset(0); // Assumes the first row is the header
			$records = Statement::create()->process($csv);

			$progressBar = $this->output->createProgressBar(iterator_count($records));
			$progressBar->start();

			foreach ($records as $record) {
				$authorName = trim($record['author_name']);
				if (empty($authorName)) {
					$this->warn("Skipping author record due to empty name: " . json_encode($record));
					$progressBar->advance();
					continue;
				}

				$imagePath = null;
				if (!empty($record['author_image-src'])) {
					$imageUrl = self::BASE_IMAGE_URL . $record['author_image-src'];
					$imagePath = $this->downloadImage($imageUrl, self::AUTHOR_PHOTO_PATH, Str::slug($authorName) . '-' . basename($record['author_image-src']));
				}

				Author::updateOrCreate(
					['name' => $authorName],
					[
						'born_death' => trim($record['born_death'] ?? ''),
						'image_path' => $imagePath,
						'biography' => trim($record['biography'] ?? ''),
					]
				);
				$progressBar->advance();
			}
			$progressBar->finish();
			$this->info("\nAuthors import finished.");
		}

		private function importBooks()
		{
			$this->line('Importing books...');
			$filePath = storage_path('app/public/import/epubbooks.csv');

			if (!file_exists($filePath)) {
				$this->error("Book CSV file not found at: {$filePath}");
				return;
			}

			$csv = Reader::createFromPath($filePath, 'r');
			$csv->setHeaderOffset(0);
			$records = Statement::create()->process($csv);

			$progressBar = $this->output->createProgressBar(iterator_count($records));
			$progressBar->start();

			foreach ($records as $record) {
				$bookName = trim($record['book_name']);
				if (empty($bookName)) {
					$this->warn("Skipping book record due to empty name: " . json_encode($record));
					$progressBar->advance();
					continue;
				}

				// Author
				$authorName = trim(str_replace('by', '', $record['author'] ?? ''));
				$author = null;
				if (!empty($authorName)) {
					$author = Author::firstOrCreate(['name' => $authorName]);
				}

				// Series
				$seriesName = trim(str_replace('series:', '', $record['series'] ?? ''));
				$series = null;
				if (!empty($seriesName) && strtolower($seriesName) !== 'none' && strtolower($seriesName) !== 'n/a') {
					// Remove potential numbering like (#3) for cleaner series names if desired
					$seriesName = preg_replace('/\s*\(\#\d+\)$/', '', $seriesName);
					$seriesName = trim($seriesName);
					if(!empty($seriesName)) {
						$series = Series::firstOrCreate(['name' => $seriesName]);
					}
				}

				// Cover Image
				$coverImagePath = null;
				if (!empty($record['cover_image-src'])) {
					$imageUrl = self::BASE_IMAGE_URL . $record['cover_image-src'];
					$coverImagePath = $this->downloadImage($imageUrl, self::BOOK_COVER_PATH, Str::slug($bookName) . '-' . basename($record['cover_image-src']));
				}

				$bookData = [
					'author_id' => $author?->id,
					'series_id' => $series?->id,
					'cover_image_path' => $coverImagePath,
					'description' => trim($record['description'] ?? ''),
					'excerpt' => trim($record['excerpt'] ?? ''),
				];

				// Using name and author_id to uniquely identify a book for updateOrCreate
				$book = Book::updateOrCreate(
					['name' => $bookName, 'author_id' => $author?->id],
					$bookData
				);


				// Subjects
				$subjectsString = trim(str_replace('subjects:', '', $record['subjects'] ?? ''));
				if (!empty($subjectsString)) {
					$subjectNames = array_map('trim', explode(',', $subjectsString));
					$subjectIds = [];
					foreach ($subjectNames as $subjectName) {
						if (!empty($subjectName)) {
							$subject = Subject::firstOrCreate(['name' => $subjectName]);
							$subjectIds[] = $subject->id;
						}
					}
					if (!empty($subjectIds)) {
						$book->subjects()->sync($subjectIds);
					}
				}
				$progressBar->advance();
			}
			$progressBar->finish();
			$this->info("\nBooks import finished.");
		}

		private function downloadImage(string $url, string $directory, string $filename): ?string
		{
			try {
				$response = Http::timeout(30)->get($url); // 30 second timeout
				if ($response->successful()) {
					$fullPath = $directory . '/' . $filename;
					Storage::put($fullPath, $response->body());
					// Return the path relative to the storage/app directory for DB storage
					// but accessible via /storage/{directory_name_after_public}/{filename}
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
