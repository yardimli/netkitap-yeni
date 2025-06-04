<?php

	namespace App\Console\Commands;

	use Illuminate\Console\Command;
	use App\Models\Subject;
	use App\Models\ParentSubject;
	use Illuminate\Support\Facades\DB;

	class PopulateParentSubjects extends Command
	{
		protected $signature = 'app:populate-parent-subjects';
		protected $description = 'Populates parent_subjects table and links subjects to their parents';

		// Define parent categories and their Turkish translations
		private $parentSubjectDefinitions = [
			"Children's Literature" => "Çocuk Edebiyatı",
			"Fiction" => "Kurgu",
			"Poetry & Drama" => "Şiir ve Tiyatro",
			"History & Biography" => "Tarih ve Biyografi",
			"Science & Philosophy" => "Bilim ve Felsefe",
			"Religion, Mythology & Folklore" => "Din, Mitoloji ve Halkbilimi",
			"Social Sciences & Humanities" => "Sosyal ve Beşeri Bilimler",
			"Arts & Literature Studies" => "Sanat ve Edebiyat Çalışmaları",
			"Self-Help & Personal Development" => "Kişisel Gelişim ve Kendine Yardım",
			"Travel & Reportage" => "Seyahat ve Röportaj",
			"Sex" => "Cinsellik",
			"Action & Adventure" => "Aksiyon ve Macera",
			"Other Non-Fiction" => "Diğer Kurgu Dışı",
		];

		// Define keywords to map subjects to parent categories (EN Parent Name)
		// Order is important: more specific keywords should come first.
		private $keywordMap = [
			// Children's Literature (Highest Priority)
			"children's" => "Children's Literature",
			"çocuk" => "Children's Literature",
			"picture book" => "Children's Literature",

			"sex" => "Sex",
			"action & adventure" => "Action & Adventure",
			"aksiyon ve macera" => "Action & Adventure",

			// Poetry & Drama
			"poetry" => "Poetry & Drama", "şiir" => "Poetry & Drama",
			"playscript" => "Poetry & Drama", "oyun metinleri" => "Poetry & Drama",
			"play" => "Poetry & Drama", "oyunlar" => "Poetry & Drama", // "Oyunlar" (plural) is safer than "oyun"

			// Specific Fiction Sub-types (all map to "Fiction" parent)
			// Note: If "Historical Fiction" should be under "History", adjust mapping.
			// Current logic: Suffix "Fiction" or "Kurgu" makes it part of the "Fiction" parent.
			"classic fiction" => "Fiction", "klasik kurgu" => "Fiction",
			"historical fiction" => "Fiction", "tarihî kurgu" => "Fiction",
			"science fiction" => "Fiction", "bilim kurgu" => "Fiction",
			"fantasy" => "Fiction", "fantastik" => "Fiction",
			"romance" => "Fiction", "romantizm" => "Fiction",
			"horror" => "Fiction", "korku" => "Fiction",
			"thriller" => "Fiction", "gerilim" => "Fiction",
			"mystery" => "Fiction", "gizem" => "Fiction",
			"dystopian" => "Fiction", "distopik" => "Fiction",
			"westerns" => "Fiction",
			"short stories" => "Fiction", "kısa öyküler" => "Fiction",
			"satirical fiction" => "Fiction", "hicivli kurgu" => "Fiction",
			"humorous fiction" => "Fiction", "mizahi kurgu" => "Fiction",
			"war & combat fiction" => "Fiction", "savaş ve muharebe kurgusu" => "Fiction",
			"myth & legend told as fiction" => "Fiction", "kurgu olarak anlatılan mit ve efsane" => "Fiction",
			"crime & mystery fiction" => "Fiction", "polisiye ve gizem kurgu" => "Fiction",

			// General Fiction (if not caught by specific or children's)
			"fiction" => "Fiction", "kurgu" => "Fiction",

			// History & Biography
			"history" => "History & Biography", "tarih" => "History & Biography", // Must be after "historical fiction" if that goes to Fiction
			"biography" => "History & Biography", "biyografi" => "History & Biography",
			"autobiography" => "History & Biography", "otobiyografi" => "History & Biography",
			"memoirs" => "History & Biography", "anılar" => "History & Biography",
			"diaries" => "History & Biography", "günlükler" => "History & Biography",
			"letters & journals" => "History & Biography", "mektuplar ve günlükler" => "History & Biography",
			"true war" => "History & Biography", "gerçek savaş" => "History & Biography",
			"civil wars" => "History & Biography", "iç savaşlar" => "History & Biography",
			"slavery" => "History & Biography", "kölelik" => "History & Biography",
			"first world war" => "History & Biography", "birinci dünya savaşı" => "History & Biography",
			"second world war" => "History & Biography", "ikinci dünya savaşı" => "History & Biography", // Assuming non-fiction context
			"military history" => "History & Biography", "askerî tarih" => "History & Biography",
			"warfare & defence" => "History & Biography", "harp ve savunma" => "History & Biography",

			// Science & Philosophy
			"science: general issues" => "Science & Philosophy", "bilim: genel konular" => "Science & Philosophy",
			"science" => "Science & Philosophy", "bilim" => "Science & Philosophy", // Must be after "science fiction"
			"philosophy" => "Science & Philosophy", "felsefe" => "Science & Philosophy",
			"biology" => "Science & Philosophy", "biyoloji" => "Science & Philosophy",
			"life sciences" => "Science & Philosophy", "yaşam bilimleri" => "Science & Philosophy",
			"evolution" => "Science & Philosophy", "evrim" => "Science & Philosophy",
			"psychology" => "Science & Philosophy", "psikoloji" => "Science & Philosophy",
			"diseases" => "Science & Philosophy", "hastalıklar" => "Science & Philosophy",

			// Religion, Mythology & Folklore
			"religious" => "Religion, Mythology & Folklore", "dini" => "Religion, Mythology & Folklore",
			"spiritual" => "Religion, Mythology & Folklore", "ruhani" => "Religion, Mythology & Folklore",
			"mythology" => "Religion, Mythology & Folklore", "mitoloji" => "Religion, Mythology & Folklore",
			"myths & legends" => "Religion, Mythology & Folklore", "mitler ve efsaneler" => "Religion, Mythology & Folklore",
			"folklore" => "Religion, Mythology & Folklore", "halkbilimi" => "Religion, Mythology & Folklore",
			"theology" => "Religion, Mythology & Folklore", "ilahiyat" => "Religion, Mythology & Folklore",
			"bibles" => "Religion, Mythology & Folklore", "kutsal kitaplar" => "Religion, Mythology & Folklore",
			"witchcraft" => "Religion, Mythology & Folklore", "cadılık" => "Religion, Mythology & Folklore",
			"norse religion" => "Religion, Mythology & Folklore", "iskandinav dini" => "Religion, Mythology & Folklore",
			"celtic religion" => "Religion, Mythology & Folklore", "kelt dini" => "Religion, Mythology & Folklore",
			"ancient religions" => "Religion, Mythology & Folklore", "antik dinler" => "Religion, Mythology & Folklore",

			// Social Sciences & Humanities
			"social issues" => "Social Sciences & Humanities", "sosyal sorunlar" => "Social Sciences & Humanities",
			"sociology" => "Social Sciences & Humanities", "sosyoloji" => "Social Sciences & Humanities",
			"feminism" => "Social Sciences & Humanities", "feminizm" => "Social Sciences & Humanities",
			"gender & relationships" => "Social Sciences & Humanities", "cinsiyet ve ilişkiler" => "Social Sciences & Humanities",
			"law" => "Social Sciences & Humanities", "hukuk" => "Social Sciences & Humanities",
			"copyright law" => "Social Sciences & Humanities", "telif hukuku" => "Social Sciences & Humanities",
			"politics & government" => "Social Sciences & Humanities", "siyaset ve yönetim" => "Social Sciences & Humanities",
			"political science & theory" => "Social Sciences & Humanities", "siyaset bilimi ve kuramı" => "Social Sciences & Humanities",
			"political economy" => "Social Sciences & Humanities", "siyasi iktisat" => "Social Sciences & Humanities",

			// Arts & Literature Studies
			"architecture" => "Arts & Literature Studies", "mimarlık" => "Arts & Literature Studies",
			"literature: history & criticism" => "Arts & Literature Studies", "edebiyat: tarih ve eleştiri" => "Arts & Literature Studies",
			"literary essays" => "Arts & Literature Studies", "edebi denemeler" => "Arts & Literature Studies",
			"literature & literary studies" => "Arts & Literature Studies", "edebiyat ve edebi çalışmalar" => "Arts & Literature Studies",


			// Self-Help & Personal Development
			"self-help" => "Self-Help & Personal Development", "kendine yardım" => "Self-Help & Personal Development",
			"personal development" => "Self-Help & Personal Development", "kişisel gelişim" => "Self-Help & Personal Development",
			"dreams & their interpretation" => "Self-Help & Personal Development", "rüyalar ve yorumları" => "Self-Help & Personal Development",
			"public speaking guides" => "Self-Help & Personal Development", "topluluk önünde konuşma rehberleri" => "Self-Help & Personal Development",

			// Travel & Reportage
			"travel writing" => "Travel & Reportage", "seyahat yazıları" => "Travel & Reportage",
			"travel & holiday" => "Travel & Reportage", "seyahat ve tatil" => "Travel & Reportage",
			"reportage & collected journalism" => "Travel & Reportage", "röportaj ve gazetecilik derlemeleri" => "Travel & Reportage",
			"geographical discovery & exploration" => "Travel & Reportage", "coğrafi keşif ve araştırma" => "Travel & Reportage",

			// Fallbacks
			"etc." => "Other Non-Fiction", "vb." => "Other Non-Fiction",
		];


		public function handle()
		{
			$this->info('Starting to populate parent subjects...');

			DB::transaction(function () {
				$subjects = Subject::all();
				$parentSubjectsCache = []; // Cache for created parent subjects

				// Pre-populate cache with existing parent subjects to minimize DB queries
				foreach (ParentSubject::all() as $ps) {
					$parentSubjectsCache[$ps->name] = $ps;
				}

				foreach ($subjects as $subject) {
					$foundParentNameEn = null;
					$subjectNameLower = strtolower($subject->name);
					$subjectNameTrLower = strtolower($subject->name_tr);

					foreach ($this->keywordMap as $keyword => $parentNameEn) {
						if (str_contains($subjectNameLower, strtolower($keyword)) || str_contains($subjectNameTrLower, strtolower($keyword))) {
							$foundParentNameEn = $parentNameEn;
							break;
						}
					}

					if (!$foundParentNameEn) {
						// Default fallback if no keyword matches
						// You might want to inspect these manually
						// For now, we use "Other Non-Fiction", but "Fiction" could be another fallback if it's truly fiction.
						// Given the data, most uncategorized items are likely non-fiction or very specific.
						$this->warn("No specific keyword match for: '{$subject->name}' / '{$subject->name_tr}'. Assigning to 'Other Non-Fiction'.");
						$foundParentNameEn = "Other Non-Fiction";
					}


					$parentNameTr = $this->parentSubjectDefinitions[$foundParentNameEn] ?? 'Diğer Kurgu Dışı'; // Fallback TR

					// Find or create the parent subject
					if (!isset($parentSubjectsCache[$foundParentNameEn])) {
						$parent = ParentSubject::firstOrCreate(
							['name' => $foundParentNameEn],
							['name_tr' => $parentNameTr]
						);
						$parentSubjectsCache[$foundParentNameEn] = $parent;
						$this->info("Created Parent: {$parent->name} / {$parent->name_tr}");
					} else {
						$parent = $parentSubjectsCache[$foundParentNameEn];
					}

					if ($subject->parent_subject_id !== $parent->id) {
						$subject->parent_subject_id = $parent->id;
						$subject->save();
					}
				}
			});

			$this->info('Parent subjects populated and linked successfully.');
			return Command::SUCCESS;
		}
	}
