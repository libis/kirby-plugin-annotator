<div class="annotator-field-section one-colum-template">
  <?php if ($image = $file->toFile()): ?>
    <div class="annotator-field-section__annotator-field annotator-field">
      <div class="annotator-field__annotator__viewport annotator__viewport">
        <div class="annotator__viewport__annotator__canvas annotator__canvas <?= $image->width() > $image->height() ? 'w-big' : 'h-big' ?>">
          <picture class="<?= $image->width() > $image->height() ? 'picture-w-big' : 'picture-h-big' ?>">
            <source
              srcset="<?= $image->srcset('') ?>"
            >
            <img 
              class="<?= $image->width() > $image->height() ? 'img-w-big' : 'img-h-big' ?>"
              src="<?= $image->url() ?>" 
              alt="<?= $image->alt() ?>"
              srcset="<?= $image->srcset('') ?>"
            >
          </picture>
          <?php $i = 1; ?>
          <?php foreach($annotator->toObject()->markers()->toStructure() as $row): ?>
            <div class="annotator-field__annotator-pointer annotator-pointer annotator-pointer-selector" 
              style="top: <?= $row->y() ?>%; left: <?= $row->x() ?>%; transform: translate(-50%, -50%)" data-y="<?= $row->y() ?>" data-x="<?= $row->x() ?>" data-zoom="<?=$row->zoom() ?>" data-id="<?= $i ?>">
              <div class="annotator-pointer__annoator-mid-pointer annotator-mid-pointer">
              </div>
            </div>
            <?php $i++; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>  
  <?php endif ?>
  <div class="annotator-field-section__annotator-open-info annotator-open-info">
    <div class="annotator-open-info__annotator-open-info-icon annotator-open-info-icon">
      <?= svg($kirby->root('plugins').'/libis-annotator/assets/icons/info.svg') ?>
    </div>
  </div>
  <div class="annotator-field-section__annotator-info-section annotator-info-section">
    <div class="annotator-info-section__annotator-close-info annotator-close-info">
      <?= svg($kirby->root('plugins').'/libis-annotator/assets/icons/cross.svg') ?>
    </div>
    <div class="annotator-info-section__annotator-info annotator-info annotator-info-place">
      <?= snippet("infoTemplates/" . $annotator->toObject()->infoTemplate(), ["data" => $annotator->toObject()->introdata()->toObject()]) ?>
    </div>
    <?= snippet("annotatorComponents/annotatorNavigation", ["currentPage" => 0, "countPages" => $annotator->toObject()->markers()->toStructure()->count()]) ?>
  </div> 
</div>
