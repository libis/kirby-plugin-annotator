<div class="annotator-field-section flex flex-row gap-40 flex-wrap items-end">
  <?php if ($image = $file->toFile()): ?>
    <div class="annotator-field-section__annotator-field annotator-field relative <?= $image->width() > $image->height() ? 'w-full' : 'w-full md:w-1/4' ?> lg:max-w-1/2">
      <div class="annotator-field__annotator__viewport annotator__viewport relative overflow-hidden w-full lg:max-h-[80dvh]">
        <div class="annotator__viewport__annotator__canvas annotator__canvas inline-block w-full origin-top-left will-change-transform duration-800 ease-out">
          <picture>
            <source
              srcset="<?= $image->srcset('') ?>"
            >
            <img 
              class="w-full object-contain h-auto select-none pointer-events-none block"
              src="<?= $image->url() ?>" 
              alt="<?= $image->alt() ?>"
              srcset="<?= $image->srcset('') ?>"
            >
          </picture>
          <?php $i = 1; ?>
          <?php foreach($annotator->toObject()->markers()->toStructure() as $row): ?>
            <div class="annotator-field__annotator-pointer annotator-pointer annotator-pointer-selector group flex flex-row justify-center items-center w-35 h-35 border-2 border-primary [.active]:border-secondary absolute rounded-4xl cursor-pointer" 
              style="top: <?= $row->y() ?>%; left: <?= $row->x() ?>%; transform: translate(-50%, -50%)" data-y="<?= $row->y() ?>" data-x="<?= $row->x() ?>" data-zoom="<?=$row->zoom() ?>" data-id="<?= $i ?>">
              <div class="annotator-pointer__annoator-mid-pointer annotator-mid-pointer h-8 w-8 bg-primary  group-[.active]:h-12 group-[.active]:w-12 group-[.active]:bg-secondary rounded-4xl">
              </div>
            </div>
            <?php $i++; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>  
  <?php endif ?>
  <div class="annotator-field-section__annotator-info-section annotator-info-section flex flex-col justify-end mb-20 gap-20 <?= $image->width() > $image->height() ? 'w-full' : 'w-full md:w-1/2' ?> lg:w-2/6">
    <div class="annotator-info-section__annotator-info annotator-info annotator-info-place">
      <?= snippet("infoTemplates/" . $annotator->toObject()->infoTemplate(), ["data" => $annotator->toObject()->introdata()->toObject()]) ?>
    </div>
    <?= snippet("annotatorComponents/annotatorNavigation", ["currentPage" => 0, "countPages" => $annotator->toObject()->markers()->toStructure()->count()]) ?>
  </div> 
</div>
