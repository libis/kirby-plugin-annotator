<div class="annotation-pagination <?= $extraClasses ?? '' ?>" data-page="<?=$currentPage?>" data-pages="<?=$countPages?>">
  <div class="prev-arrow prev-icon pagination-icon">
    <?= svg($kirby->root('plugins').'/annotator/assets/icons/long-arrow.svg') ?>
  </div>
  <div class="next-arrow next-icon pagination-icon">
    <?= svg($kirby->root('plugins').'/annotator/assets/icons/long-arrow.svg') ?>
  </div>
</div>