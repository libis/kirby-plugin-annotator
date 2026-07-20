<div class="annotator-field-section one-colum-template">
  <?php if ($image = $file->toFile()): ?>
    <div class="annotator-field-section__annotator-field annotator-field">
      <div class="annotator-field__annotator__viewport annotator__viewport">
        <div class="annotator__viewport__annotator__canvas annotator__canvas inline-block <?= $image->width() > $image->height() ? 'h-full' : 'md:w-fit' ?>">
          <picture class="<?= $image->width() > $image->height() ? '' : 'md:block md:w-fit' ?>">
            <source
              srcset="<?= $image->srcset('') ?>"
            >
            <img 
              class="w-full object-contain h-auto select-none pointer-events-none block <?= $image->width() > $image->height() ? '' : 'md:w-auto md:max-h-[90vh]' ?>"
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
  <div class="annotator-field-section__annotator-open-info annotator-open-info hidden absolute md:bottom-115 md:right-0 w-70 h-70 bg-secondary p-5 flex flex-row justify-center items-center">
    <div class="annotator-open-info__annotator-open-info-icon annotator-open-info-icon [&_svg]:w-40 [&_path]:fill-primary [&_circle]:stroke-primary">
      <?= svg($kirby->root('plugins').'/libis-annotator/assets/icons/info.svg') ?>
    </div>
  </div>
  <div class="annotator-field-section__annotator-info-section annotator-info-section flex flex-col gap-20 justify-end w-full md:max-w-1/2 lg:max-w-2/5 md:px-30 lg:px-60 md:py-45 md:absolute md:bottom-45 md:-right-30 lg:-right-60 md:bg-secondary md:text-primary md:[&_path]:stroke-primary">
    <div class="annotator-info-section__annotator-close-info annotator-close-info cursor-pointer hidden md:block [&_path]:stroke-primary [&_path]:stroke-3 [&_svg]:w-30 absolute right-30 lg:right-60 top-30 lg:top-35">
      <?= svg($kirby->root('plugins').'/libis-annotator/assets/icons/cross.svg') ?>
    </div>
    <div class="annotator-info-section__annotator-info annotator-info annotator-info-place">
      <?= snippet("infoTemplates/" . $annotator->toObject()->infoTemplate(), ["data" => $annotator->toObject()->introdata()->toObject()]) ?>
    </div>
    <?= snippet("annotatorComponents/annotatorNavigation", ["currentPage" => 0, "countPages" => $annotator->toObject()->markers()->toStructure()->count()]) ?>
  </div> 
</div>
