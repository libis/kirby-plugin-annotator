<div class="annotator-field-section-wrapper" <?= isset($blockId) ? 'data-blockId="' . $blockId . '"' : '' ?> data-key="<?= base64_encode($annotator->key()) ?>" data-pageKey="<?= base64_encode($page->id()) ?>" data-zoomFactor="0">
  <?php kirby()->session()->set('annotator.page', $page->id()); ?>
  <?= snippet("annotatorTemplates/" . $annotator->toObject()->template(), ["file" => $annotator->toObject()->image(), "annotator" => $annotator]) ?>
</div>