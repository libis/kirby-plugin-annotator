<div class="annotator-field-section two-colum-template">
  <?php if ($image = $file->toFile()): ?>
    <div class="annotator-field-section__annotator-field annotator-field <?= $image->width() > $image->height() ? 'w-big' : 'h-big' ?>">
      <div class="annotator-field__annotator__viewport annotator__viewport">
        <div class="annotator__viewport__annotator__canvas annotator__canvas">
          <picture>
            <source
              srcset="<?= $image->srcset('') ?>"
            >
            <img 
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
  <div class="annotator-field-section__annotator-info-section annotator-info-section <?= $image->width() > $image->height() ? 'info-w-big' : 'info-h-big' ?>">
    <div class="annotator-info-section__annotator-info annotator-info annotator-info-place">
      <?= snippet("infoTemplates/" . $annotator->toObject()->infoTemplate(), ["data" => $annotator->toObject()->introdata()->toObject()]) ?>
    </div>
    <?= snippet("annotatorComponents/annotatorNavigation", ["currentPage" => 0, "countPages" => $annotator->toObject()->markers()->toStructure()->count()]) ?>
  </div> 
</div>
