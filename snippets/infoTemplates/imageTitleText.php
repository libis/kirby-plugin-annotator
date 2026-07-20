<div class="annotator-title-image-textblock flex flex-col gap-20">
  <?php if ($data->image()->toFiles()->isNotEmpty() && $data->image()->toFiles()->count() > 0): ?> 
    <?php $mediaFiles = $data->image()->toFiles() ?>
    <div class="annotator-title-image-textblock__image-section image-section flex flex-row flex-wrap gap-15">
      <div class="image-section__image-wrapper image-wrapper h-90">
        <?php foreach($mediaFiles as $media): ?>      
          <picture class="w-full object-cover h-full">
            <source
              srcset="<?= $media->srcset('') ?>"
              type="image/webp"
            >
            <img 
              class="w-full object-cover h-full"
              src="<?= $media->resize(250)->url() ?>" 
              alt="<?= $media->alt() ?>"
              style="object-position: <?= $media->focus()->or('50% 50%') ?>"
            >
          </picture>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  <div class="annotator-title-image-textblock__text-wrapper text-wrapper flex flex-col gap-5">
    <h3 class="text-wrapper__annotator-title annotator-title h3"><?= $data->title() ?></h3>
    <p class="text-wrapper__annotator-text annotator-text body-text"><?= $data->text() ?></p>
  </div>
</div>