<?php
use Kirby\Uuid\Uuid;
Kirby::plugin('libis/annotator', [
	'fields' => [
		'annotator' => [],
		'annotatorFile' => [],
	],
	'blueprints' => [
		'blocks/annotator' => __DIR__ . '/blueprints/blocks/annotator.yml',
	],
	'snippets' => [
		'blocks/annotator' => __DIR__ . '/snippets/blocks/annotator.php',
		'fields/annotator' => __DIR__ . '/snippets/fields/annotator.php',
		'annotatorTemplates/onecolumn' => __DIR__ . '/snippets/annotatorTemplates/onecolumn.php',
		'annotatorTemplates/twocolumn' => __DIR__ . '/snippets/annotatorTemplates/twocolumn.php',
		'infoTemplates/imageTitleText' => __DIR__ . '/snippets/infoTemplates/imageTitleText.php',
		'annotatorComponents/annotatorNavigation' => __DIR__ . '/snippets/annotatorComponents/annotatorNavigation.php'
	],
	'translations' => (function () {
		$translations = [];
		$dir = __DIR__ . '/translations';

		foreach (glob($dir . '/*.json') as $file) {
		$lang = basename($file, '.json');
		$json = file_get_contents($file);
		$translations[$lang] = json_decode($json, true);
		}

		return $translations;
	})(),

	'routes' => [
		[
			'pattern' => 'get/images/uuid',
			'method' => 'POST',
			'action' => function () {
				if(kirby()->user() && kirby()->user()->isLoggedIn()) {
					
					$uuids = $_POST['uuids'] ?? [];
					$files = [];

					foreach ($uuids as $uuid) {
						if ($uuidObj = Uuid::for($uuid)) {
							if ($file = $uuidObj->model()) {
								$files[] = $file;
							}
						}
					}

					$files = array_filter($files);

					return array_values(array_map(function ($file) {
						return [
							'id'   => $file->id(),
							'uuid' => $file->uuid()->toString(),
							'url'  => $file->url(),
							'filename' => $file->filename(),
							'template' => $file->template(),
							'panel' => [
								'image' => $file->panel()->image(),
								'url'   => $file->panel()->url()
							]
						];
					}, $files));

				}
				else {
					return [];
				}
			}
		],
		[
			'pattern' => '/annotator/files',
			'method' => 'GET',
			'action' => function () {
				$query = get('query');
				$searchQuery = get('q', '');

				$page = get('page', 1);
				$limit = get('limit', 5);

				if (empty($query)) {
					$result = page()->images();
				} else {
					$result = Kirby\Query\Query::factory($query)->resolve([]);
				}
				
				$files = $result instanceof Kirby\Cms\Files
					? $result
					: $result->toFiles();
				
				if ($searchQuery != "") {
					$files = $files->search($searchQuery);
				}

				$totalCount = $files->count();
				
				$files = $files->paginate([
					'page'  => $page,
					'limit' => $limit
				]);

				$files = $files->map(fn($file) => [
					'id'   => $file->id(),
					'uuid' => $file->uuid()->toString(),
					'url'  => $file->url(),
					'filename' => $file->filename(),
					'template' => $file->template(),
					'parent' => $file->parent()->url(),
					'panel' => [
						'image' => $file->panel()->image(),
						'url'   => $file->panel()->url()
					]
				])->values();

				return [
					'files' => $files,
					'total' => $totalCount
				];
			}
		],
		[
			'pattern' => '/content/annotator/data/(:all)/(:all)/(:all)',
      'method' => 'GET',
      'action' => function ($annotatorId, $annotatorType, $lang) {
				$pageId = kirby()->session()->get('annotator.page');
				$page = page($pageId);

				if($page) {
					if($annotatorType == "field") {
						$fieldName = base64_decode($annotatorId);
						if ($page->content()->has($fieldName)) {
							$annotatorBlock = $page->$fieldName();
						}
						else {
							return "field not found";
						}
					}
					elseif($annotatorType == "block") {
						foreach ($page->content()->data() as $fieldName => $value) {
							$field = $page->$fieldName();

							try {
								$blocks = $field->toBlocks();

								$block = $blocks->findBy('id', $annotatorId);

								if ($block) {
									$annotatorBlock = $block;
									break;
								}
							} catch (Exception $e) {
							}

							try {
								$layouts = $field->toLayouts();

								foreach ($layouts as $layout) {
									foreach ($layout->columns() as $column) {
										$blocks = $column->blocks();

										$block = $blocks->findBy('id', $annotatorId);

										if ($block) {
											$annotatorBlock = $block;
											break 3;
										}
									}
								}
							} catch (Exception $e) {
							}
						}
					}
					else {
						return "error";
					}

					if(isset($annotatorBlock) && $annotatorBlock != null) {
						$values = [];
						$i = 1;
						$annotator = $annotatorBlock->annotator()->toObject();

						$values[0] = snippet("infoTemplates/" . $annotator->infoTemplate(), ["data" => $annotator->introdata()->toObject()], true);
						if($annotator->markers()->isNotEmpty()) {
							foreach($annotator->markers()->toStructure() as $field) {
								$values[$i] = snippet("infoTemplates/" . $annotator->infoTemplate(), ["data" => $field], true);
								$i ++;
							}
							return kirby()->response()->json(['data' => $values]);
						}
						return kirby()->response()->json(['data' => $values]);
					}
					return "error";
				}

				return "error";
			}
		]
	]

]);
